<?php

include_once __DIR__.'/../../core.php';

//id_record = variante

// Questo file serve per prendere il record che poi sarà visualizzato sull'edit.
// filtrare sempre per l'id univoco = id_record
if (isset($id_record)) {
    $record = $dbo->fetchOne('SELECT * FROM mg_articoli_varianti WHERE id='.prepare($id_record));
//    $record['lat'] = floatval($record['lat']);
//    $record['lng'] = floatval($record['lng']);
}

//id_parent = anagrafica
//if (isset($id_parent)) {
//    $record['tipo_anagrafica'] = $dbo->fetchOne('SELECT tipo FROM an_anagrafiche WHERE an_anagrafiche.idanagrafica ='.prepare($id_parent))['tipo'];
//    $record['iso2'] = $dbo->fetchOne('SELECT iso2 FROM an_nazioni INNER JOIN an_anagrafiche ON an_nazioni.id = an_anagrafiche.id_nazione WHERE an_anagrafiche.idanagrafica ='.prepare($id_parent))['iso2'];
//}
