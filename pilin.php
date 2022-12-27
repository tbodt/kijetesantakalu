<?php

require_once 'ilo/poki.php';
require_once 'ilo/sijelo.php';
require_once 'ilo/ante.php';

$pilin = $_POST['pilin'] ?? null;
if ($pilin === 'pona') {
    $pilin = 1;
} elseif ($pilin === 'ike') {
    $pilin = -1;
} else {
    // o pilin!
    header('location: https://www.youtube.com/watch?v=NgWCLg4H_4U&t=84s');
    exit();
}

[$nanpa_ilo, $nanpa_sijelo] = nimi_mi_pilin();
$poki->prepare('insert or replace into pilin (nanpa_ilo, nanpa_jan, pilin, nanpa_nimi, tenpo) values (?, ?, ?, ?, strftime(\'%Y-%m-%dT%H:%M:%fZ\'))')
     ->execute([$nanpa_ilo, $nanpa_sijelo, $pilin, $_POST['nanpa']]);

header('location: '.$_SERVER['HTTP_REFERER']);
