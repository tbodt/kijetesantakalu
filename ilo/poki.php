<?php

require_once 'ilo/ilonitaso.php';

$poki = new PDO(NIMI_POKI, null, null, array(
    PDO::ATTR_PERSISTENT => true,
));
$poki->exec('pragma foreign_keys = on');
$poki->exec('pragma journal_mode = wal');

function mute_pona_pilin($pona, $ike) {
    // https://www.evanmiller.org/how-not-to-sort-by-average-rating.html
    if ($pona + $ike == 0) {
        return 0;
    }
    $z = 1.96;
    $phat = $pona/($pona+$ike);
    return (
        ($pona + $z*$z/2) / ($pona + $ike)
        - $z * sqrt(
            ($pona * $ike) / ($pona + $ike) + $z*$z/4
        ) / ($pona+$ike)
    ) / (1 + $z*$z / ($pona + $ike));
}

$poki->sqliteCreateFunction('mute_ante_nimi', 'levenshtein', 2, PDO::SQLITE_DETERMINISTIC);
$poki->sqliteCreateFunction('mute_pona_pilin', 'mute_pona_pilin', 2, PDO::SQLITE_DETERMINISTIC);
