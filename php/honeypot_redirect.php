<?php
require_once __DIR__ . 'honeypot_functions.php';

session_start();
$honeypot = new HoneypotSystem();

// Log the redirection reason
$reason = $_POST['js_detection'] ?? 'suspicious_activity';
$honeypot->logAttack('Honeypot Redirect', "Reason: $reason, IP: " . $_SERVER['REMOTE_ADDR']);

// Set honeypot mode
$_SESSION['honeypot_mode'] = true;

// Return to the same login page - now in honeypot mode
header('Location: login.php');
exit();
?>