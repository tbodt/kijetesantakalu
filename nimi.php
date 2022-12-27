<?php

require_once 'ilo/poki.php';
require_once 'ilo/sijelo.php';
require_once 'ilo/ante.php';

$O_KAMA_E_SONA_WILE = '
    select sona_nimi.nanpa, sona_nimi.nimi, sona, kepeken, tenpo,
        sijelo.nimi as nimi_jan,
        ifnull(pona, 0) as pona, ifnull(ike, 0) as ike,
        (select pilin from pilin where nanpa_ilo is :nanpa_ilo and nanpa_jan is :nanpa_jan and nanpa_nimi = sona_nimi.nanpa) as pilin_mi
';
$TAN_POKI = '
    from sona_nimi
    left join sijelo on tan_jan = sijelo.nanpa
    left join (
        select nanpa_nimi,
            ifnull(sum(max(pilin, 0)), 0) as pona,
            ifnull(sum(abs(min(pilin, 0))), 0) as ike
        from pilin group by nanpa_nimi
    ) on nanpa_nimi = sona_nimi.nanpa
';
$NASIN_NANPA_WAN = [
    'pona' => 'mute_pona_pilin(pona, ike) desc',
    'sin' => 'julianday(tenpo) desc',
    'ike' => 'mute_pona_pilin(pona, ike) asc',
];

$nanpa_wan = $_GET['nanpawan'] ?? 'pona';
$O_NANPA_WAN = $NASIN_NANPA_WAN[$nanpa_wan];

if (isset($_GET['nimi'])) {
    // mi o pana e wile alasa tawa ilo fts5 kepeken nasin nasa
    // ilo o pana e lipu ni: toki alasa la nimi wan ale li lon
    // mi poki e toki alasa tawa ilo la ni li kama ijo alasa wan. ale toki o lon insa nimi. wile ala
    // ni la mi o kipisi e toki tawa nimi o poki e ona ale o wan sin e ona kepeken nimi KIN AND.
    // toki ilo nasa ni li lukin kipisi lon nasin sama pi ilo kipisi unicode. ken suli la nasin ona li ante lili. taso ni li pakala suli ala li musi.
    preg_match_all('/[\pL\pN\p{Co}]+/u', $_GET['nimi'], $nimi_alasa);
    $nimi_alasa = $nimi_alasa[0];
    foreach ($nimi_alasa as &$nimi) {
        $nimi = '"'. str_replace('"', '""', $nimi) .'"';
    }
    $nimi_alasa = implode(" AND ", $nimi_alasa);
    $nimi_mute = $poki->prepare("
        $O_KAMA_E_SONA_WILE
        $TAN_POKI
        join sona_nimi_la_alasa as alasa on alasa.rowid = sona_nimi.nanpa
        where sona_nimi_la_alasa match :alasa
        order by mute_ante_nimi(sona_nimi.nimi, :toki_alasa),
            $O_NANPA_WAN
    ");
    $nimi_mute->bindValue('alasa', $nimi_alasa);
    $nimi_mute->bindValue('toki_alasa', $_GET['nimi']);
} elseif (isset($_GET['tan'])) {
    $nimi_mute = $poki->prepare("
        $O_KAMA_E_SONA_WILE
        $TAN_POKI
        where nimi_jan = :nimi_jan
        order by $O_NANPA_WAN
    ");
    $nimi_mute->bindValue('nimi_jan', $_GET['tan']);
} else {
    if ($nanpa_wan === 'pona') {
        $O_NANPA_WAN = 'mute_pona_pilin(pona, ike) + random() / 9223372036854775808 * 0.3 desc';
    }
    $nimi_mute = $poki->prepare("
        $O_KAMA_E_SONA_WILE
        $TAN_POKI
        order by $O_NANPA_WAN
        limit 10
    ");
}

[$nanpa_ilo, $nanpa_jan] = nimi_mi_pilin();
$nimi_mute->bindValue('nanpa_ilo', $nanpa_ilo);
$nimi_mute->bindValue('nanpa_jan', $nanpa_jan);
$nimi_mute->execute();
$nimi_mute = $nimi_mute->fetchAll();

if (isset($_GET['nimi'])) {
    if (isset($nimi_mute[0])) {
        $NIMI_SULI = $nimi_mute[0]['nimi'];
        $SONA_SULI = $nimi_mute[0]['sona'];
    } else {
        $NIMI_SULI = $_GET['nimi'];
        $SONA_SULI = 'nimi ni li lon ala';
    }
}
if ($nanpa_wan === 'pona') {
    unset($SONA_SULI);
}

?>
<?php include 'lipu/open.php'; ?>

<style>
.tan {
    font-weight: bold;
}
.e {
    text-align: right;
}
</style>

<main>

<form>
<p style="display:flex">
<?php /* nimi <?= count($nimi_mute) ?> li lon */ ?>
<span class="suli-ken"></span>
<span>
<?php
foreach ($_GET as $nimi => $ijo) {
    if ($nimi === 'nanpawan') { continue; }
?>
<input type="hidden" name="<?= htmlentities($nimi) ?>" value="<?= htmlentities($ijo) ?>">
<?php } ?>
nanpa wan o nimi
<select name="nanpawan" onchange="this.form.submit()">
<?php foreach (array_keys($NASIN_NANPA_WAN) as $nasin) { ?>
<option<?= $nanpa_wan === $nasin ? ' selected' : '' ?>><?= $nasin ?></option>
<?php } ?>
</select>
<noscript><button type="submit">󱥄</button></noscript>
</span>
</form>

<?php foreach ($nimi_mute as $nimi) { ?>

<section class="nimi">
    <h1><a href="/nimi.php?nimi=<?= htmlentities($nimi['nimi']) ?>"><?= htmlentities($nimi['nimi']) ?></a></h1>
    <p class="sona"><?= htmlentities($nimi['sona']) ?>
    <p class="kepeken"><?= htmlentities($nimi['kepeken']) ?>
    <p class="tan">tan <a href="/nimi.php?tan=<?= htmlentities($nimi['nimi_jan']) ?>"><?= htmlentities($nimi['nimi_jan']) ?></a>
    tan <?= htmlentities(sitelen_tenpo($nimi['tenpo'])) ?>

    <form action="pilin.php" method="post">
        <p class="pilin">
        <input type="hidden" name="nanpa" value="<?= htmlentities($nimi['nanpa']) ?>">
        <button type="submit" name="pilin" value="pona"
            <?= $nimi['pilin_mi'] == 1 ? ' disabled' : '' ?>>
            󱥔 <?= htmlentities($nimi['pona']) ?>
        </button>
        <button type="submit" name="pilin" value="ike"
            <?= $nimi['pilin_mi'] == -1 ? ' disabled' : '' ?>>
            󱤍 <?= htmlentities($nimi['ike']) ?>
        </button>
    </form>
</section>

<?php
}

if (count($nimi_mute) === 0) {
?>
nimi ala li lon :(
<?php } ?>
