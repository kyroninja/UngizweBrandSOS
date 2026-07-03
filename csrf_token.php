<?php
// csrf_token.php — issues a CSRF token for static pages (e.g. index.html)
// that can't run PHP themselves. Called via fetch() before form submit.
require_once __DIR__ . '/config.php';

$token = csrf_token();

header('Content-Type: application/json');
header('Cache-Control: no-store');
echo json_encode(['csrf_token' => $token]);