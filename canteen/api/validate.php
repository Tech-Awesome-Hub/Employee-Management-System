<?php
// Always check and start session first
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['department'])) {
    // No department in session, maybe invalid login
    die(json_encode(['success' => false, 'message' => 'Session expired or invalid.']));
}

?>