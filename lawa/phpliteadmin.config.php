<?php

chdir(__DIR__.'/..');
require_once 'ilo/sijelo.php';
require_once 'ilo/poki.php';

// jan lawa taso o ken kepeken wawa sewi ni!

o_sijelo();
if ((int) $sijelo['lawa'] !== WawaLawa::LAWA) {
    die();
}

$mu_sqlite = 'sqlite:';
if (strpos(NIMI_POKI, $mu_sqlite) !== 0) {
    die();
}
$nimi_poki = substr(NIMI_POKI, strlen($mu_sqlite));

$password = '';
$directory = false;
$databases = [
    [
        'path' => $nimi_poki,
        'name' => $nimi_poki,
    ]
];

