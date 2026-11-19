<?php
function sanitize($data) {
    return htmlspecialchars(trim($data));

    // htmlspecialchars() converts < > & to safe HTML — prevents XSS attacks
    // trim() removes whitespace from the beginning and end of the string
}
    
?>