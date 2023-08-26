<?php

require_once 'ilo/poki.php';

$kama_nasin = [
<<<PINI
create table sona_nimi (
    nanpa integer primary key,
    nimi text,
    sona text,
    kepeken text,
    tan_jan integer references sijelo(nanpa),
    tenpo datetime
);

create table sijelo (
    nanpa integer primary key,
    nimi text unique,
    nimi_email text unique
);

create virtual table sona_nimi_la_alasa using fts5(nimi, content='sona_nimi', content_rowid='nanpa');
create trigger sona_nimi_li_sin after insert on sona_nimi begin
    insert into sona_nimi_la_alasa (rowid, nimi) values (new.nanpa, new.nimi);
end;
create trigger sona_nimi_li_ante after update on sona_nimi begin
    insert into sona_nimi_la_alasa (sona_nimi_la_alasa, rowid, nimi) values ('delete', old.nanpa, old.nimi);
    insert into sona_nimi_la_alasa (rowid, nimi) values (new.nanpa, new.nimi);
end;
create trigger sona_nimi_li_weka after delete on sona_nimi begin
    insert into sona_nimi_la_alasa (sona_nimi_la_alasa, rowid, nimi) values ('delete', old.nanpa, old.nimi);
end;

create table pilin (
    nanpa_ilo string,
    nanpa_jan integer references sijelo(nanpa),
    pilin integer,
    nanpa_nimi integer references sona_nimi(nanpa) on delete cascade
);
create unique index ijo_wan_la_pilin_wan on pilin(coalesce(nanpa_ilo, nanpa_jan), nanpa_nimi);
create index pilin_la_nanpa_nimi on pilin(nanpa_nimi);

PINI, <<<PINI
update sona_nimi set tenpo = strftime('%Y-%m-%dT%H:%M:%fZ', tenpo);
PINI, <<<PINI
alter table pilin add column tenpo datetime;
PINI, <<<PINI
alter table sijelo add column lawa integer default 0;
PINI
];

$poki->beginTransaction();
$nanpa_nasin = $poki->query('pragma user_version')->fetch()['user_version'];
$nanpa_nasin_open = $nanpa_nasin;
while ($nanpa_nasin < count($kama_nasin)) {
    $poki->exec($kama_nasin[$nanpa_nasin]);
    $nanpa_nasin++;
    $poki->exec("pragma user_version = $nanpa_nasin");
}
$poki->commit();
echo "poki li kama nasin #$nanpa_nasin tan nasin #$nanpa_nasin_open";
