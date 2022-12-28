<?php

require_once 'ilo/poki.php';
require_once 'ilo/sijelo.php';
require_once 'ilo/ante.php';

$O_KAMA_E_SONA_WILE = '
    select sona_nimi.nanpa, sona_nimi.nimi, sona, kepeken, tenpo,
        sijelo.nimi as nimi_jan,
        ifnull(pona, 0) as pona, ifnull(ike, 0) as ike,
        (select case pilin when 1 then \'pona\' when -1 then \'ike\' end from pilin where nanpa_ilo is :nanpa_ilo and nanpa_jan is :nanpa_jan and nanpa_nimi = sona_nimi.nanpa) as pilin_mi,
        mute_pona_pilin(pona, ike) as mute_pona,
        mute_pona_pilin(pona, ike) + random() / 9223372036854775808 * 1.0 as pona_pi_nasin_nasa
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
    'pona' => 'mute_pona desc',
    'sin' => 'julianday(tenpo) desc',
    'ike' => 'mute_pona asc',
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
    if (!isset($_GET['nanpawan'])) {
        $nanpa_wan = 'musi';
        $O_NANPA_WAN = 'pona_pi_nasin_nasa desc';
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
<?php require 'lipu/open.php'; ?>

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


nimi
<select name="nanpawan" onchange="this.form.submit()">
<?php if ($nanpa_wan === 'musi') { ?>
<option selected>musi</option>
<?php } ?>
<?php foreach (array_keys($NASIN_NANPA_WAN) as $nasin) { ?>
<option<?php if ($nanpa_wan === $nasin) { ?> selected<?php } ?>><?= $nasin ?></option>
<?php } ?>
</select>
o nanpa wan

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

    <form action="pilin.php" method="post" data-pilin="<?= htmlentities($nimi['pilin_mi']) ?>">
        <p class="pilin">
        <input type="hidden" name="nanpa" value="<?= htmlentities($nimi['nanpa']) ?>">
        <button type="submit" name="pilin" value="pona"
            <?= $nimi['pilin_mi'] == 'pona' ? ' disabled' : '' ?>>
            󱥔 <span class="pona"><?= htmlentities($nimi['pona']) ?></span>
        </button>
        <button type="submit" name="pilin" value="ike"
            <?= $nimi['pilin_mi'] == 'ike' ? ' disabled' : '' ?>>
            󱤍 <span class="ike"><?= htmlentities($nimi['ike']) ?></span>
        </button>
        <?php if (isset($_GET['lukininsa'])) { ?>
        <?= $nimi['mute_pona'] ?>
        <?= $nimi['pona_pi_nasin_nasa'] ?>
        <?php } ?>
    </form>
</section>

<?php
}

if (count($nimi_mute) === 0) {
?>
nimi ala li lon :(
<?php } ?>

<script>
for (let lipuPana of document.querySelectorAll('form[data-pilin]')) {
    lipuPana.addEventListener('submit', (w) => {
        w.preventDefault();
        let pilin = lipuPana.getAttribute('data-pilin');
        let nenaPona = lipuPana.querySelector('.pona');
        let nenaIke = lipuPana.querySelector('.ike');
        let [mutePona, muteIke] = [nenaPona, nenaIke].map(n => +n.innerText);
        if (pilin === 'pona')
            nenaPona.innerText--;
        else if (pilin === 'ike')
            nenaIke.innerText--;
        let pilinSin = w.submitter.value;
        lipuPana.setAttribute('data-pilin', pilinSin);
        if (pilinSin === 'pona')
            nenaPona.innerText++;
        else if (pilinSin === 'ike')
            nenaIke.innerText++;
        nenaPona.parentElement.disabled = nenaIke.parentElement.disabled = false;
        if (pilinSin === 'pona')
            nenaPona.parentElement.disabled = true;
        else if (pilinSin = 'ike')
            nenaIke.parentElement.disabled = true;

        let ijoPana = new FormData(lipuPana);
        ijoPana.append('pilin', pilinSin);
        fetch('/pilin.php', {
            method: 'POST',
            body: ijoPana,
        }).catch(console.error);
    });
}
</script>
