<?php

require_once 'ilo/poki.php';
require_once 'ilo/sijelo.php';

o_sijelo();

$nimi = $poki->prepare('
    select nimi, sona, kepeken, tenpo, nanpa
    from sona_nimi
    where nanpa = ? and tan_jan = ?
    limit 10
');
$nimi->execute([$_POST['nanpa'], $_SESSION['nanpa_sijelo']]);
$nimi = $nimi->fetch();

if (isset($_POST['wekaala'])) {
    header('location: /sijelo.php');
    exit();
}

if ($nimi !== false && isset($_POST['weka'])) {
    $poki->prepare('delete from sona_nimi where nanpa = ? and tan_jan = ?')->execute([$_POST['nanpa'], $_SESSION['nanpa_sijelo']]);
?>
<?php $NIMI_SULI = 'nimi li weka'; include 'lipu/open.php'; ?>
<main>
<p>nimi <strong><?= htmlentities($nimi['nimi']) ?></strong> la sona li weka.
<p><a href="/sijelo.php">o lukin sin e ijo sina</a>
<?php
    exit();
}
?>
<?php $NIMI_SULI = 'nimi o weka ala weka'; include 'lipu/open.php'; ?>
<main>

<form method="post">
<input type="hidden" name="nanpa" value="<?= htmlentities($_POST['nanpa']) ?>">
<p>sina wile ala wile weka e sona nimi ni?
<section class="nimi">
    <h1><?= htmlentities($nimi['nimi']) ?></h1>
    <p class="sona"><?= htmlentities($nimi['sona']) ?>
    <p class="kepeken"><?= htmlentities($nimi['kepeken']) ?>
</section>
<p>
<input type="submit" name="weka" value="o weka">
<input type="submit" name="wekaala" value="o weka ala">
</form>
