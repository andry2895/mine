<?php

include_once __DIR__.'/../../core.php';

switch (filter('op')) {
    case 'update':
        $valore = filter('valore');
				
        if (isset($valore)) {

		    $dbo->query('Update `mg_gruppo_attributi` set `valore` = '.prepare($valore).' WHERE `id`='.prepare($id_record));
				
            flash()->info(tr('Salvataggio completato!'));
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio!'));
        }

        break;

    case 'add':
        $valore = filter('valore');

        

        if (isset($valore)) {
            $dbo->query('INSERT INTO `mg_gruppo_attributi` (`valore`) VALUES ('.prepare($valore).')');
            $id_record = $dbo->lastInsertedID();

            flash()->info(tr('Aggiunto nuovo _TYPE_', [
                '_TYPE_' => 'Gruppo Attributi',
            ]));
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio!'));
        }

        break;
		
		
    case 'insatt':
        $valore = filter('valore');
		$colore = filter('colore');
        

        if (isset($valore)) {

			$dbo->query('INSERT INTO `mg_attributi` (`id_gruppo_attributi`, `valore`, `colore`) VALUES ('.prepare($id_record).','.prepare($valore).','.prepare($colore).')');
            //$id_record = $dbo->lastInsertedID();

            flash()->info(tr('Aggiunto nuovo _TYPE_', [
                '_TYPE_' => 'Attributo',
            ]));
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio!'));
        }

        break;		
		

    case 'delete':
        if (!empty($id_record)) {
            $dbo->query('DELETE FROM `mg_gruppo_attributi` WHERE `id`='.prepare($id_record));

            flash()->info(tr('Tipologia di _TYPE_ eliminata con successo!', [
                '_TYPE_' => 'Attributo',
            ]));
        }

        break;

    case 'delete_rata':
        $id = filter('id');
        if (isset($id)) {
            $dbo->query('DELETE FROM `mg_attributi` WHERE `id`='.prepare($id));
            flash()->info(tr('Elemento eliminato con successo!'));


        }

        break;
}
