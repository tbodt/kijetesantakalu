<?php

require_once 'ilo/poki.php';
require_once 'ilo/sijelo.php';
require_once 'ilo/panalipu.php';
require_once 'ilo/ante.php';

$toki_pakala = array();

if (!isset($_SESSION['nanpa_sijelo'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // NI LA: jan li pana e nimi email
        $nimi_email = $_POST['nimiilo'];
        $nanpa_wawa = bin2hex(random_bytes(64));
        $_SESSION['kama_sijelo'] = [$nanpa_wawa, $nimi_email];
        // o sitelen e lipu
        ob_start();
?>
sina wile kepeken lipu kijetesantakalu la, o kepeken toki wawa ni.

sina wile ala la kama pi lipu ni li nasa la o weka e ona.

<?= NIMI_LIPU.$_SERVER['SCRIPT_NAME'].'?'.http_build_query(['wawa' => $nanpa_wawa]) ?>
<?php
        $lipu = ob_get_clean();
        o_pana_e_lipu($nimi_email, 'lipu kijetesantakalu la sina wile kepeken anu seme?', $lipu);
?>
<?php $NIMI_SULI = 'sina'; include 'lipu/open.php'; ?>
<main>
ilo li pana e ijo wawa tawa sina kepeken nimi <?= htmlentities($nimi_email) ?>. o alasa lon poki lipu sina!
<?php
        exit();
    }

    if (isset($_GET['wawa'])) {
        // NI LA: ilo li pana e lipu la jan li luka e toki wawa
        [$nanpa_wawa, $nimi_email] = $_SESSION['kama_sijelo'] ?? [null, null];
        unset($_SESSION['kama_sijelo']);

        if (hash_equals($nanpa_wawa, $_GET['wawa'])) {
            // NI LA: nanpa wawa li pona la o kama sijelo
            $nimi_lukin_open = 'jan ' . explode('@', $nimi_email)[0];
            $poki->beginTransaction();
            $poki->prepare('insert or ignore into sijelo (nimi_email) values (?)')->execute([$nimi_email]);
            $alasa_sona = $poki->prepare('select nanpa, nimi from sijelo where nimi_email = ?');
            $alasa_sona->execute([$nimi_email]);
            $sona_sijelo = $alasa_sona->fetch();
            if ($sona_sijelo['nimi'] === null) {
                // NI LA: sijelo li sin li wile e nimi. taso nimi o sama sijelo ante ala.
                $nanpa = 1;
                $alasa_nimi = $poki->prepare('select 1 from sijelo where nimi = ?');
                do {
                    $nimi_lukin = $nanpa === 1 ? $nimi_lukin_open : "$nimi_lukin #$nanpa";
                    $alasa_nimi->execute([$nimi_lukin]);
                    $nanpa++;
                } while ($alasa_nimi->fetch() !== false);
                $poki->prepare('update sijelo set nimi = ? where nimi_email = ?')->execute([$nimi_lukin, $nimi_email]);
            }
            $_SESSION['nanpa_sijelo'] = $sona_sijelo['nanpa'];
            $poki->commit();
            header('location: '.$_SERVER['SCRIPT_NAME']);
            exit();
        } else {
            $toki_pakala[] = "toki wawa ni li pakala. o lukin pali sin.";
        }
    }
}

if (isset($_POST['weka'])) {
    unset($_SESSION['nanpa_sijelo']);
}

if (isset($_SESSION['nanpa_sijelo'])) {
    $alasa = $poki->prepare('select nimi, nimi_email from sijelo where nanpa = ?');
    $alasa->execute([$_SESSION['nanpa_sijelo']]);
    $sona = $alasa->fetch();
    if ($sona === false) {
        error_log("sona la nanpa sijelo {$_SESSION['nanpa_sijelo']} li pakala li lon ala");
        unset($_SESSION['nanpa_sijelo']);
    } else {
        $nimi_lukin = $sona['nimi'];
    }
}

if (isset($_SESSION['nanpa_sijelo']) && $_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['nimi'])) {
    $nimi_lukin_sin = $_POST['nimi'];
    $nimi_lukin_sin = o_weka_e_weka_sitelen($nimi_lukin_sin);
    if (empty($nimi_lukin_sin)) {
        $toki_pakala[] = "sina o pana e nimi";
    }
    if (mb_strlen($nimi_lukin_sin) > 50) {
        $toki_pakala[] = "nimi li suli ike";
    }
    if (empty($toki_pakala)) {
        $poki->prepare('update sijelo set nimi = ? where nanpa = ?')->execute([$nimi_lukin_sin, $_SESSION['nanpa_sijelo']]);
        $nimi_lukin = $nimi_lukin_sin;
    }
}
?>
<?php $NIMI_SULI = 'sina'; include 'lipu/open.php'; ?>
<main>

<?php if (!isset($_SESSION['nanpa_sijelo'])) { ?>

<h1>ilo li sona ala e sina</h1>

<?php include 'lipu/tokipakala.php'; ?>

<form method="post">
<p>ilo Email la nimi sina li seme?
<p><input type="email" required name="nimiilo" id="nimiilo" placeholder="jan@kulupu.ilo">
<p><input type="submit" value="o kama sona e mi">
<p>sina pana e nimi la ilo li pana e <strong>ijo wawa</strong> tawa ni. sina kepeken ijo wawa la ilo li kama sona e sina.
<p>ilo li <strong>awen len</strong> e nimi ni. mi lawa e ilo la mi ken lukin, taso jan ante ala li ken.
</form>

<?php exit(); } ?>

<h1><?= htmlentities($nimi_lukin) ?> o, toki!</h1>

<?php include 'lipu/tokipakala.php'; ?>

<form method="post">
<p>nimi sina li
<input type="text" id="nimi" name="nimi" value="<?= htmlentities($nimi_lukin_sin ?? $nimi_lukin) ?>">
<input type="submit" value="o ante">
</form>

<form method="post">
<p><input type="submit" name="weka" value="o weka tan sijelo ilo">
</form>

<hr>
<?php
$nimi_mi = $poki->prepare('select nanpa, nimi from sona_nimi where tan_jan = ?');
$nimi_mi->execute([$_SESSION['nanpa_sijelo']]);
?>
<p>sina pana e nimi ni:
<?php foreach ($nimi_mi as $nimi) { ?>
<form method="post" action="wekanimi.php">
    <input type="hidden" name="nanpa" value="<?= htmlentities($nimi['nanpa']) ?>">
    <p><a href="/nimi.php?nimi=<?= htmlentities($nimi['nimi']) ?>"><?= htmlentities($nimi['nimi']) ?></a>
    <br>
    <input type="submit" value="o weka e nimi ni">
</form>
<?php } ?>
</table>
