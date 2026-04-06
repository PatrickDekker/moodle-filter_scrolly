// Listen for all clicks on the page
document.addEventListener('click', function(e) {

    const btn = e.target;

    // --------------------------------------------------
    // HANDLE TRUE / FALSE BUTTONS
    // --------------------------------------------------
    if (btn.classList.contains('scrolly-true') || 
        btn.classList.contains('scrolly-false')) {

        const container = btn.closest('.scrolly-block');
        const feedback = container.querySelector('.scrolly-feedback');

        // Prevent answering twice
        if (container.classList.contains('answered')) {
            return;
        }

        // Get correct answer from data attribute
        const correctAnswer = container.dataset.correct;

        // Determine user answer
        const userAnswer = btn.classList.contains('scrolly-true') ? 'true' : 'false';

        // Get feedback texts (with fallback)
        const correctFeedback = container.dataset.correctfeedback || 'Correct.';
        const incorrectFeedback = container.dataset.incorrectfeedback || 'Niet correct.';

        // --------------------------------------------------
        // Check answer
        // --------------------------------------------------
        if (userAnswer.trim() === correctAnswer.trim()) {
            feedback.textContent = correctFeedback;
            container.classList.add('correct');
        } else {
            feedback.textContent = incorrectFeedback;
            container.classList.add('incorrect');
        }

        // Mark as answered
        container.classList.add('answered');

        // Disable buttons
        const buttons = container.querySelectorAll('button');
        buttons.forEach(b => {
            if (!b.classList.contains('scrolly-reset')) {
                b.disabled = true;
            }
        });

        // --------------------------------------------------
        // Show reset button (if present)
        // --------------------------------------------------
        const resetBtn = container.querySelector('.scrolly-reset');
        const showReset = container.dataset.showreset !== 'false';

        if (resetBtn && showReset) {
            resetBtn.style.display = 'inline-block';
        }

        return;
    }

    // --------------------------------------------------
    // HANDLE MULTICHOICE CHECK BUTTON
    // --------------------------------------------------
    if (btn.classList.contains('scrolly-check')) {

        const container = btn.closest('.scrolly-block');
        const feedback = container.querySelector('.scrolly-feedback');

        // Prevent answering twice
        if (container.classList.contains('answered')) {
            return;
        }

        const correctAnswer = container.dataset.correct;

        const selected = container.querySelector('input[type="radio"]:checked');

        if (!selected) {
            feedback.textContent = 'Kies eerst een antwoord.';
            return;
        }

        const userAnswer = selected.value;

        const correctFeedback = container.dataset.correctfeedback || 'Correct.';
        const incorrectFeedback = container.dataset.incorrectfeedback || 'Niet correct.';

        // --------------------------------------------------
        // Check answer
        // --------------------------------------------------
        if (userAnswer.trim() === correctAnswer.trim()) {
            feedback.textContent = correctFeedback;
            container.classList.add('correct');
        } else {
            feedback.textContent = incorrectFeedback;
            container.classList.add('incorrect');
        }

        // Mark as answered
        container.classList.add('answered');

        // Disable radios
        const radios = container.querySelectorAll('input[type="radio"]');
        radios.forEach(r => r.disabled = true);

        // Disable check button
        btn.disabled = true;

        // --------------------------------------------------
        // Show reset button
        // --------------------------------------------------
        const resetBtn = container.querySelector('.scrolly-reset');
        const showReset = container.dataset.showreset !== 'false';

        if (resetBtn && showReset) {
            resetBtn.style.display = 'inline-block';
        }

        return;
    }

    // --------------------------------------------------
    // HANDLE RESET BUTTON
    // --------------------------------------------------
    if (btn.classList.contains('scrolly-reset')) {

        const container = btn.closest('.scrolly-block');
        const feedback = container.querySelector('.scrolly-feedback');

        // Remove state classes
        container.classList.remove('answered', 'correct', 'incorrect');

        // Enable buttons again
        const buttons = container.querySelectorAll('button');
        buttons.forEach(b => {
            b.disabled = false;
        });

        // Reset radios
        const radios = container.querySelectorAll('input[type="radio"]');
        radios.forEach(r => {
            r.checked = false;
            r.disabled = false;
        });

        // Clear feedback
        feedback.textContent = '';

        // Hide reset button again
        btn.style.display = 'none';

        return;
    } // end if

    // --------------------------------------------------
    // HANDLE ACCORDION TOGGLE
    // --------------------------------------------------
    if (btn.classList.contains('scrolly-accordion-toggle')) {

        // Find the clicked accordion item
        const item = btn.closest('.scrolly-accordion-item');

        // Find the parent accordion container
        const container = btn.closest('.scrolly-accordion');

        // Check if this item is already open
        const isOpen = item.classList.contains('active');

        // --------------------------------------------------
        // STEP 1: Close ALL items
        // --------------------------------------------------
        container.querySelectorAll('.scrolly-accordion-item').forEach(i => {
            i.classList.remove('active');
        });

        // --------------------------------------------------
        // STEP 2: Open clicked item (if it was closed)
        // --------------------------------------------------
        if (!isOpen) {
            item.classList.add('active');
        }

        // --------------------------------------------------
        // IMPORTANT:
        // We do NOT use style.display anymore!
        // Visibility is fully controlled by CSS:
        //
        // .scrolly-accordion-content { display: none; }
        // .active .scrolly-accordion-content { display: block; }
        // --------------------------------------------------

        return;
    }
});