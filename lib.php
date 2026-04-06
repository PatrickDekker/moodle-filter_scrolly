<?php
defined('MOODLE_INTERNAL') || die();

/**
 * Load JS and CSS for the scrolly filter
 * This runs before page output starts
 */
function filter_scrolly_before_http_headers() {
    global $PAGE;

    // Safety check (PAGE should normally exist)
    if (empty($PAGE)) {
        return;
    }

    // Load stylesheet
    $PAGE->requires->css('/filter/scrolly/styles.css');

    // Load JavaScript
    $PAGE->requires->js(new moodle_url('/filter/scrolly/script.js'));
}
