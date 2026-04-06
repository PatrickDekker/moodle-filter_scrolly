<?php

defined('MOODLE_INTERNAL') || die();

/**
 * Scrolly filter
 * Replaces [scrolly]...[/scrolly] blocks with interactive HTML
 */
class filter_scrolly extends moodle_text_filter {

    /**
     * Main filter function
     * This is called by Moodle for every text block
     */
    public function filter($text, array $options = array()) {
        
        if (empty($text) || !is_string($text)) {
            return $text;
        }

        /**
         * Match:
         * [scrolly] ... [/scrolly]
         * 
         * (.*?) = everything between the tags
         */
        $pattern = '/\[scrolly(?:\s[^\]]*)?\](.*?)\[\/scrolly\]/is';


        $text = preg_replace_callback($pattern, function($matches) {

            // Get context (reliable in filters)
            $context = $this->context;

            // RAW CONTENT (everything inside the tag)
            $raw = trim($matches[1]);

            // PARSE CONFIG (key: "value")
            // Example:
            // type: "truefalse" correct: "false"
            $config = [];

            preg_match_all('/(\w+):\s*"([\s\S]*?)"/', $raw, $matchesConfig, PREG_SET_ORDER);

            foreach ($matchesConfig as $m) {
                $config[strtolower($m[1])] = trim($m[2]);
            }

            $type = 'truefalse'; // default = truefalse

            if (!empty($config['type'])) {
                $type = strtolower($config['type']);
            }

            // RENDER BASED ON TYPE
            if ($type === 'truefalse') {
                return self::render_truefalse($config);
            }

            if ($type === 'multichoice') {
                return self::render_multichoice($config);
            }

            if ($type === 'accordion') {
                return self::render_accordion($config);
            }
            
            // fallback → show original content if type unknown
            return $matches[0];
        
        }, $text);

        return $text;
    } // end filter

     /**
     * Render a true/false scrolly block
     */
    private static function render_truefalse($config) {

        // Determine correct answer (default = false)
        $correct = 'false';
        if (!empty($config['correct'])) {
            $correct = strtolower($config['correct']);
        }

        // Button labels (default via language or fallback)
        $truelabel = !empty($config['truelabel']) 
            ? $config['truelabel'] 
            : get_string('labeltrue', 'filter_scrolly');

        $falselabel = !empty($config['falselabel']) 
            ? $config['falselabel'] 
            : get_string('labelfalse', 'filter_scrolly');

        // Show reset button (default = true)
        $showreset = !isset($config['showreset']) || $config['showreset'] !== 'false';

        // label resetButton
        if (!empty($config['resetlabel'])) {
            $resetlabel = $config['resetlabel'];
        } else {
            try {
                $resetlabel = get_string('resetlabel', 'filter_scrolly');
            } catch (Exception $e) {
                $resetlabel = 'Reset';
            }
        }

        // Question text (HTML allowed)
        $question = '';
        if (!empty($config['question'])) {
            $question = $config['question'];
        }

        // Feedback texts (defaults if not provided)
        $correctfeedback = !empty($config['correctfeedback']) ? $config['correctfeedback'] : 'Correct.';
        $incorrectfeedback = !empty($config['incorrectfeedback']) ? $config['incorrectfeedback'] : 'Niet correct.';

        // Template
        $template = '
        <div class="scrolly-block"
            data-type="truefalse"
            data-correct="{{correct}}"
            data-correctfeedback="{{correctfeedback}}"
            data-incorrectfeedback="{{incorrectfeedback}}"
            data-showreset="{{showreset}}">
            <p>{{question}}</p>
            <button class="scrolly-true">{{truelabel}}</button>
            <button class="scrolly-false">{{falselabel}}</button>
            <button class="scrolly-reset" style="display:none">{{resetlabel}}</button>
            <div class="scrolly-feedback"></div>
        </div>
        ';

        // Data for template
        $data = [
            'correct' => $correct,
            'question' => $question,
            'correctfeedback' => $correctfeedback,
            'incorrectfeedback' => $incorrectfeedback,
            'truelabel' => $truelabel,
            'falselabel' => $falselabel,
            'showreset' => $showreset ? 'true' : 'false',
            'resetlabel' => $resetlabel
        ];

        return self::render_template($template, $data);
    } // end render_truefalse

     /**
     * Render a multiplechoic scrolly block
     */
    private static function render_multichoice($config) {

        // Question
        $question = !empty($config['question']) ? $config['question'] : '';
    
        // Options (split by |)
        $options = [];
        if (!empty($config['options'])) {
            $options = explode('|', $config['options']);
        }
    
        // Correct answer
        $correct = !empty($config['correct']) ? trim($config['correct']) : '';
    
        // Feedback
        $correctfeedback = !empty($config['correctfeedback']) ? $config['correctfeedback'] : 'Correct.';
        $incorrectfeedback = !empty($config['incorrectfeedback']) ? $config['incorrectfeedback'] : 'Niet correct.';
    
        // Reset
        $showreset = !isset($config['showreset']) || $config['showreset'] !== 'false';
    
        $resetlabel = !empty($config['resetlabel'])
            ? $config['resetlabel']
            : get_string('resetlabel', 'filter_scrolly');

        if (!empty($config['checklabel'])) {
            $checklabel = $config['checklabel'];
        } else {
            try {
                $checklabel = get_string('checklabel', 'filter_scrolly');
            } catch (Exception $e) {
                $checklabel = 'Check';
            }
        } 
    
        // Build options HTML
        $optionshtml = '';
        $groupname = 'scrolly_' . uniqid();
        foreach ($options as $index => $option) {
            $value = trim($option);
    
            $optionshtml .= '
           <label>
                <input type="radio" name="' . $groupname . '" value="' . htmlspecialchars($value) . '">
                <span>' . htmlspecialchars($value) . '</span>
            </label><br>';
        }
    
        // Template
        $template = '
        <div class="scrolly-block"
            data-type="multichoice"
            data-correct="{{correct}}"
            data-correctfeedback="{{correctfeedback}}"
            data-incorrectfeedback="{{incorrectfeedback}}"
            data-showreset="{{showreset}}">
            <p>{{question}}</p>
            <div class="scrolly-options">
                {{options}}
            </div>
            <button class="scrolly-check">{{checklabel}}</button>
            <button class="scrolly-reset" style="display:none">{{resetlabel}}</button>
            <div class="scrolly-feedback"></div>
        </div>
        ';
    
        $data = [
            'question' => $question,
            'options' => $optionshtml,
            'correct' => $correct,
            'correctfeedback' => $correctfeedback,
            'incorrectfeedback' => $incorrectfeedback,
            'showreset' => $showreset ? 'true' : 'false',
            'resetlabel' => $resetlabel,
            'checklabel' => $checklabel
        ];
    
        return self::render_template($template, $data);
    }

    /**
    * Render a accordion scrolly block
    */
    private static function render_accordion($config) {

        // --------------------------------------------------
        // STEP 1: Determine accordion title
        // --------------------------------------------------
        $title = !empty($config['title'])
            ? $config['title']
            : get_string('accordiontitle', 'filter_scrolly');
    
        // --------------------------------------------------
        // STEP 2: Build items HTML (string-based, no loop engine)
        // --------------------------------------------------
        $itemshtml = '';
    
        if (!empty($config['items'])) {
    
            // Split items using "||"
            $pairs = explode('||', $config['items']);
    
            foreach ($pairs as $pair) {
    
                // Split into title and content using "::"
                $parts = explode('::', $pair, 2);
    
                $itemtitle = isset($parts[0]) ? trim($parts[0]) : '';
                $content   = isset($parts[1]) ? trim($parts[1]) : '';
    
                // --------------------------------------------------
                // Item template (single accordion item)
                // --------------------------------------------------
                $itemtemplate = '
                <div class="scrolly-accordion-item">
                    <div class="scrolly-accordion-toggle">{{itemtitle}}</div>
                    <div class="scrolly-accordion-content">{{content}}</div>
                </div>
                ';
    
                // Replace placeholders for this item
                $itemdata = [
                    'itemtitle' => $itemtitle,
                    'content'   => $content
                ];
    
                $itemshtml .= self::render_template($itemtemplate, $itemdata);
            }
        }
    
        // --------------------------------------------------
        // STEP 3: Main accordion template
        // --------------------------------------------------
        $template = '
        <div class="scrolly-accordion">
            <div class="scrolly-accordion-title">{{title}}</div>
            {{items}}
        </div>
        ';
    
        $data = [
            'title' => $title,
            'items' => $itemshtml
        ];
    
        return self::render_template($template, $data);
    }

        /**
     * Simple template renderer
     * Replaces {{key}} with value
     */
    private static function render_template($template, $data) {
        foreach ($data as $key => $value) {
            $template = str_replace('{{' . $key . '}}', $value, $template);
        }
        return $template;
    } // end render_template
}