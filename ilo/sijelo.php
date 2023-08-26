<?php

require_once 'ilo/poki.php';

assert(session_status() === PHP_SESSION_NONE);
session_start([
    'gc_maxlifetime' => 14 * 24 * 60 * 60,
    'cookie_lifetime' => 14 * 24 * 60 * 60,
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax', // la pakala li ken ala!!!1!
]);

function o_sijelo() {
    global $poki;
    global $sijelo;
    if (isset($_SESSION['nanpa_sijelo'])) {
        $alasa_sijelo = $poki->prepare('select * from sijelo where nanpa = ?');
        $alasa_sijelo->execute([$_SESSION['nanpa_sijelo']]);
        $GLOBALS['sijelo'] = $alasa_sijelo->fetch();
        if ($sijelo !== false) {
            return;
        }
    }
    header('location: /sijelo.php');
    exit();
}
