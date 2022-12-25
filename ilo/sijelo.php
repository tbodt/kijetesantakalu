<?php

assert(session_status() === PHP_SESSION_NONE);
session_start([
    'gc_maxlifetime' => 14 * 24 * 60 * 60,
    'cookie_lifetime' => 14 * 24 * 60 * 60,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax', // la pakala li ken ala!!!1!
]);

function o_sijelo() {
    if (isset($_SESSION['nanpa_sijelo'])) {
        return;
    }
    header('location: /sijelo.php');
    exit();
}
