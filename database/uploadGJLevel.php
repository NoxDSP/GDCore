<?php
/**
 * Upload Level (GD 1.0-1.8)
 * Legacy endpoint for older GD versions
 */

// Redirect to newer endpoint
$_POST['gameVersion'] = $_POST['gameVersion'] ?? 1;
require __DIR__ . '/uploadGJLevel21.php';
