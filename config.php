<?php
// config.php — shared bootstrap, include at top of every entry script
declare(strict_types=1);

// Load credentials from environment (set via Apache/Nginx vhost, .env loader, or php.ini)
// NEVER commit real credentials. Falls back to local dev defaults only.
define('DB_HOST', getenv('UNGIZWE_DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('UNGIZWE_DB_NAME') ?: 'ungizwedb');
define('DB_USER', getenv('UNGIZWE_DB_USER') ?: 'root');
define('DB_PASS', getenv('UNGIZWE_DB_PASS') ?: '');

// Never display raw errors to users in production
ini_set('display_errors', '0');
ini_set('log_errors', '1');
error_reporting(E_ALL);

function db_connect(): mysqli
{
    $conn = @new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        error_log('DB connection failed: ' . $conn->connect_error);
        http_response_code(500);
        die('A server error occurred. Please try again later.');
    }
    $conn->set_charset('utf8mb4');
    return $conn;
}

// --- CSRF helpers ---
function csrf_token(): string
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start([
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
        ]);
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_verify(?string $token): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    return isset($_SESSION['csrf_token'], $token) && hash_equals($_SESSION['csrf_token'], $token);
}

// --- Basic per-session rate limiting (crude but effective against form spam) ---
function rate_limit(string $key, int $maxAttempts = 5, int $windowSeconds = 300): bool
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }
    $now = time();
    $bucket = $_SESSION['rl_' . $key] ?? ['count' => 0, 'reset' => $now + $windowSeconds];
    if ($now > $bucket['reset']) {
        $bucket = ['count' => 0, 'reset' => $now + $windowSeconds];
    }
    $bucket['count']++;
    $_SESSION['rl_' . $key] = $bucket;
    return $bucket['count'] <= $maxAttempts;
}