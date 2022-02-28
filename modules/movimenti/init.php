<?php

include_once __DIR__.'/../../core.php';

$dbo->query('UPDATE mg_movimenti INNER JOIN mg_articoli_varianti ON mg_movimenti.id_articolo_variante = mg_articoli_varianti.id SET mg_movimenti.descrizione_variante = mg_articoli_varianti.descrizione');

$query = 'Select * from mg_articoli_varianti';
$tuttelevarianti = $dbo->fetchArray($query);
foreach ($tuttelevarianti as $lavariante) {


$dbo->query('UPDATE mg_articoli_varianti set qta = (SELECT sum(`qta`) FROM `mg_movimenti` WHERE `idarticolo` = '.$lavariante['id_articolo'].' and `id_articolo_variante` = '.$lavariante['id'].') where id = ' . $lavariante['id']);

}

if (isset($id_record)) {	
    $record = $dbo->fetchOne('SELECT * FROM mg_movimenti WHERE id='.prepare($id_record));
}
