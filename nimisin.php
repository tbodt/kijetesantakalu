<?php

require_once 'ilo/poki.php';
require_once 'ilo/sijelo.php';
require_once 'ilo/ante.php';

o_sijelo();

$nimi = '';
$sona = '';
$kepeken = '';
$toki_pakala = array();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // jan li pana e nimi. o lukin.
    $nimi = $_POST['nimi'] ?? '';
    $sona = $_POST['sona'] ?? '';
    $kepeken = $_POST['kepeken'] ?? '';

    $nimi = o_weka_e_weka_sitelen($nimi);
    $sona = o_weka_e_weka_sitelen($sona);
    $kepeken = o_weka_e_weka_sitelen($kepeken);

    // ken la pana li suli ike li lili ike
    if (mb_strlen($nimi) == 0) {
        $toki_pakala[] = 'sina o pana e nimi';
    }
    if (mb_strlen($nimi) > 100) {
        $toki_pakala[] = 'nimi ni li suli ike';
    }
    if (mb_strlen($sona) == 0) {
        $toki_pakala[] = 'sina o pana e sona nimi';
    }
    if (mb_strlen($sona) > 500) {
        $toki_pakala[] = 'toki sona nimi li suli ike';
    }
    if (mb_strlen($kepeken) == 0) {
        $toki_pakala[] = 'sina o toki lili kepeken nimi';
    }
    if (mb_strlen($kepeken) > 500) {
        $toki_pakala[] = 'sina toki pi lili ala kepeken nimi, o toki lili taso';
    }

    // ale li pona. o pana.
    if (empty($toki_pakala)) {
        $pana = $poki->prepare('insert into sona_nimi (nimi, sona, kepeken, tan_jan, tenpo) values (?, ?, ?, ?, current_timestamp)');
        $pana->execute(array($nimi, $sona, $kepeken, $_SESSION['nanpa_sijelo']));

        header('location: nimi.php?'.http_build_query(['nimi' => $nimi]));
        exit();
    }
}

?>
<?php $NIMI_SULI = 'nimi sin'; include 'lipu/open.php'; ?>
<style>
.nimi input, .nimi textarea {
    width: 100%;
    max-width: 100%;
}
</style>

<main>
<h1>o pana e nimi</h1>

<ul>
<li>o pana e sona <strong>kepeken toki pona!</strong>
<li>o pakala ala e jan kepeken toki.
</ul>

<p>o sona e ni: sina pana e sona la sina ken ala ante e ona lon tenpo kama. (taso sina ken weka e ona li ken pana sin.)

<?php include 'lipu/tokipakala.php'; ?>

<form method="post">
<section class="nimi">
    <p>
    <h1><input id="nimi" name="nimi" required maxlength="100" placeholder="kijetesantakalu" value="<?= htmlentities($nimi) ?>"></h1>

    <p>
    <label for="sona">nimi li toki e seme?</label><br>
    <textarea id="sona" name="sona" rows="4" required maxlength="500" placeholder="kijetesantakalu li soweli li pona ale"><?= htmlentities($sona) ?></textarea>

    <p>
    <label for="kepeken">jan li kepeken nimi ni sama seme? o toki lili kepeken ona.</label><br>
    <textarea id="kepeken" class="kepeken" name="kepeken" rows="3" required maxlength="500" placeholder="mi en sina en jan ale li olin e kijetesantakalu"><?= htmlentities($kepeken) ?></textarea>
</section>

<p>
<input type="submit" value="o pana">
