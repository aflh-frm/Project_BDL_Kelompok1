<?php
// Function to sanitize inputs
function clean_input($data) {
    return htmlspecialchars(trim($data));
}
?>