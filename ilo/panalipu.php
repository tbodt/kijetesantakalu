<?php

require_once 'ilo/ilonitaso.php';

function o_pana_e_lipu($tawa, $tokisuli, $tokiinsa) {
    // nasin li sama lipu ni https://gist.github.com/hdogan/8649cd9c25c75d0ab27e140d5eef5ce2
    $lipu = fopen('php://memory', 'r+');
    fwrite($lipu, "Subject: $tokisuli\r\n");
    fwrite($lipu, "\r\n");
    fwrite($lipu, $tokiinsa);
    rewind($lipu);

    $c = curl_init();
    curl_setopt($c, CURLOPT_URL, SMTP_LA_ILO);
    curl_setopt($c, CURLOPT_MAIL_FROM, SMTP_LA_PANA_LI_TAN);
    curl_setopt($c, CURLOPT_MAIL_RCPT, [$tawa]);
    curl_setopt($c, CURLOPT_USERNAME, SMTP_LA_NIMI);
    curl_setopt($c, CURLOPT_PASSWORD, SMTP_LA_NANPA_KEN);
    curl_setopt($c, CURLOPT_UPLOAD, true);
    curl_setopt($c, CURLOPT_INFILE, $lipu);

    if (curl_exec($c) === false) {
        trigger_error('pana lipu li pakala: ' . curl_error($c), E_USER_ERROR);
    }

    curl_close($c);
    fclose($lipu);
}
