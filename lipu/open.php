<!doctype html>
<html lang="tok">
<meta charset="utf-8">
<title><?= htmlentities($NIMI_SULI ?? 'lipu kijetesantakalu') ?><?= isset($NIMI_SULI) ? ' | lipu kijetesantakalu' : '' ?></title>
<meta property="og:site_name" content="lipu kijetesantakalu">
<?php if (isset($NIMI_SULI)) { ?>
<meta property="og:title" content="<?= htmlentities($NIMI_SULI) ?>">
<?php } ?>
<?php if (isset($SONA_SULI)) { ?>
<meta property="og:description" content="<?= htmlentities($SONA_SULI) ?>">
<?php } ?>

<meta name="viewport" content="width=device-width" />
<style>
* {
    box-sizing: border-box;
}

@font-face {
    font-family: "nasin nanpa";
    src: url("https://cdn.jsdelivr.net/gh/ETBCOR/nasin-nanpa@n2.5.1/versions/nasin-nanpa-2.5.1.otf");
    font-weight: 100 900;
}
:root {
    font-family: -apple-system, BlinkMacSystemFont, avenir next, avenir, segoe ui, helvetica neue, helvetica, Cantarell, Ubuntu, roboto, noto, arial, sans-serif, nasin nanpa;
    overflow-wrap: break-word;
    line-height: 1.4;
}

body {
    margin: 0;
}
main, header > div {
    max-width: 50em;
    padding: 0 1em;
    margin: 0 auto;
}

a {
    text-decoration: none;
    color: #0000EE;
}
a:hover {
    text-decoration: underline;
}

input, button, select, textarea {
    font: inherit;
}

blockquote {
    border-left: 4px solid lightgray;
    margin-left: 0;
    padding-left: 1em;
}

header {
    border-bottom: 1px solid;
    margin-bottom: 1em;
    padding: 1em 0;
}
header > div {
    display: flex;
    flex-direction: column;
    gap: 0.5em;
}
header > div > div, header nav {
    display: flex;
    align-items: baseline;
    gap: 0.5em 1em;
    flex-wrap: wrap;
}
header form {
    display: flex;
    align-items: center;
}

.nimi-suli h1 {
    display: inline;
    margin: 0;
    font-size: 2em;
}
.nimi-suli aside {
    display: inline;
}

header .suli-ken {
    flex: 1;
}

.toki-pakala {
    color: red;
}

.nimi {
    overflow: auto;
    padding: 0 1em;
    margin-bottom: 2em;
    background: whitesmoke;
    border-radius: 1em;
}

.nimi .sona, .nimi .kepeken {
    white-space: pre-line;
}

.kepeken {
    font-style: italic;
}
</style>

<header><div>
    <nav>
        <div class="nimi-suli">
            <h1><a href="/">󱤪󱦀</a></h1>
            <aside>... li wile e nimi ale!</aside>
        </div>
        <div class="suli-ken"></div>
        <a href="/lipuni.php">lipu ni li seme?</a>
        <a href="/nimisin.php">o pana e nimi</a>
        <a href="/sijelo.php">ijo mi</a>
    </nav>
    <div>
        <form class="suli-ken" action="nimi.php">
            <input class="suli-ken" type="search" placeholder="o alasa e nimi" name="nimi" value="<?=
                $_SERVER['SCRIPT_NAME'] === '/nimi.php' ? htmlentities($_GET['nimi'] ?? '') : ''
            ?>">
            <button type="submit">󱤃</button>
        </form>
    </div>
</div></header>
