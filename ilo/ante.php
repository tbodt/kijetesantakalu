<?php

date_default_timezone_set('UTC');

function sitelen_tenpo($tenpo) {
    $tenpo = new DateTime($tenpo);
    $weka = $tenpo->diff(new DateTime());
    return sitelen_pi_weka_tenpo($weka);
}

function sitelen_pi_weka_tenpo($weka) {
    if ($weka->y > 1)
        return "tenpo sike pini mute";
    elseif ($weka->y == 1)
        return "tenpo sike pini";
    elseif ($weka->m > 6)
        return "tenpo sike ni";
    elseif ($weka->m > 1)
        return "tenpo mun pini mute";
    elseif ($weka->m == 1)
        return "tenpo mun pini";
    elseif ($weka->d > 14)
        return "tenpo mun ni";
    elseif ($weka->d > 1)
        return "tenpo suno pini mute";
    elseif ($weka->d == 1)
        return "tenpo suno pini";
    elseif ($weka->h > 1)
        return "tenpo suno ni";
    else
        return "tenpo poka";
}

function o_weka_e_weka_sitelen($sitelen) {
    // https://stackoverflow.com/a/4167053/1455016
    return preg_replace('/^[\pZ\pC]+|[\pZ\pC]+$/u', '', $sitelen);
}

function nanpa_pi_ilo_lukin() {
    $lupa_la_tan = $_SERVER['HTTP_X_FORWARDED_FOR'] ?? null;
    if (LUPA_LI_PANA_E_X_FORWARDED_FOR && $lupa_la_tan !== null) {
        return explode(', ', $lupa_la_tan)[0];
    }
    return $_SERVER['REMOTE_ADDR'];
}

function nimi_mi_pilin() {
    if (isset($_SESSION['nanpa_sijelo'])) {
        return [null, $_SESSION['nanpa_sijelo']];
    }
    return [nanpa_pi_ilo_lukin(), null];
}
