<?php
function isActiveForm($form, $activeForm) {
    return $form === $activeForm ? 'active' : '';
}

function showError($error) {
    if (!empty($error)) {
        return '<div class="error-message">' . htmlspecialchars($error) . '</div>';
    }
    return '';
}