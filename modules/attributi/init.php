<?php

include_once __DIR__.'/../../core.php';

if (isset($id_record)) {
    $record = $dbo->fetchOne('SELECT * FROM `mg_gruppo_attributi` WHERE id='.prepare($id_record));
}
