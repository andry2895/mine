<?php

include_once __DIR__.'/../../core.php';

$operazione = filter('op');

switch ($operazione) {
    case 'addvariante':

         if (post('qta')==0) {
             $dbo->insert('mg_articoli_varianti', [
			'id_articolo' => $id_parent,
            'immagine' => post('immagine'),
            'qta' => post('qta'),
            'impatto_prezzo' => post('impatto_prezzo'),
            'qta_minima' => post('qta_minima'),
            'ean13' => post('ean13'),
            ]);
             $id_record = $dbo->lastInsertedID();
			 
			 
			 $gruppoatt = $dbo->fetchArray("SELECT * from mg_gruppo_attributi order by id");
			 foreach ($gruppoatt as $subga) {
			$dbo->query('INSERT INTO `mg_varianti` (`id_articolo_variante`, `id_gruppo_attributi`) VALUES ('.$id_record.','.$subga['id'].')');	 
             }

             flash()->info(tr('Aggiunta una nuova variante!'));
         } else {
             flash()->warning(tr('Errore durante aggiunta variante'));
         }
		 
		 

		        // Upload file
        if (!empty($_FILES) && !empty($_FILES['immagine']['name'])) {
            $filename = Uploads::upload($_FILES['immagine'], [
                'name' => 'Immagine',
                'id_plugin' => $id_plugin,
                'id_record' => $id_record,
            ], [
                'thumbnails' => true,
            ]);

            if (!empty($filename)) {
                $dbo->update('mg_articoli_varianti', [
                    'immagine' => 'files/varianti/'.$filename,
                ], [
                    'id' => $id_record,
                ]);
            } else {
                flash()->warning(tr("Errore durante il caricamento dell'immagine!"));
            }
        }		
		
	 
		 

        break;

    case 'updatevariante':
        $id = filter('id_record');
        $array = [
            'immagine' => post('immagine'),
            'qta' => post('qta'),
            'impatto_prezzo' => post('impatto_prezzo'),
            'qta_minima' => post('qta_minima'),
            'ean13' => post('ean13'),
        ];

        $dbo->update('mg_articoli_varianti', $array, ['id' => $id_record]);

        flash()->info(tr('Salvataggio completato!'));
		
		
        // Upload file
        if (!empty($_FILES) && !empty($_FILES['immagine']['name'])) {
            $filename = Uploads::upload($_FILES['immagine'], [
                'name' => 'Immagine',
                'id_module' => $id_module,
                'id_record' => $id_record,
            ], [
                'thumbnails' => true,
            ]);

            if (!empty($filename)) {
                $dbo->update('mg_articoli_varianti', [
                    'immagine' => 'files/articoli/' . $filename,
                ], [
                    'id' => $id_record,
                ]);
            } else {
                flash()->warning(tr("Errore durante il caricamento dell'immagine!"));
            }
        }

        // Eliminazione file
        if (post('delete_immagine') !== null) {


            $dbo->update('mg_articoli_varianti', [
                'immagine' => null,
            ], [
                'id' => $id_record,
            ]);
        }		
		
		

        break;

    case 'insatt':
        $id_gruppo_attributi = filter('id_gruppo_attributi');
		$id_attributo = filter('id_attributo');
		

        if (isset($id_gruppo_attributi)) {

			$dbo->query('INSERT INTO `mg_varianti` (`id_variante`, `id_gruppo_attributi`, `id_attributo`) VALUES ('.prepare($id_record).','.prepare($id_gruppo_attributi).','.prepare($id_attributo).')');
            //$id_record = $dbo->lastInsertedID();

            flash()->info(tr('Aggiunto nuovo _TYPE_', [
                '_TYPE_' => 'Attributo',
            ]));
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio!'));
        }

        break;		
		
		
		
    case 'deletevarianti':
        $id = filter('id');
        $dbo->query('Update `mg_articoli_varianti` Set deleted_at = Now() WHERE `id` = '.prepare($id).'');
        $dbo->query('DELETE FROM `mg_varianti` WHERE `id_articolo_variante` = '.prepare($id).'');
        flash()->info(tr('Variante eliminata!'));

        break;
		
	case 'updateattributo':
        $id = filter('id');
		$id_record = filter('id_record');
        $array = [
            'id_attributo' => post('attributo'),
        ];

        $dbo->update('mg_varianti', $array, ['id' => $id]);
		
		$atrvar = $dbo->fetchArray("SELECT `mg_varianti`.`id`,`mg_varianti`.`id_articolo_variante`,`mg_varianti`.`id_gruppo_attributi`,`mg_varianti`.`id_attributo`,`mg_attributi`.`valore` FROM `mg_varianti` inner join `mg_attributi` on `mg_varianti`.`id_attributo` = `mg_attributi`.`id` WHERE `id_articolo_variante`= ".$id_record);
		
		$elencoattributi = '';
		foreach ($atrvar as $atr) {
		$elencoattributi = $elencoattributi . $atr['valore'] . ' ';	
		}
		$array = [
            'descrizione' => $elencoattributi,
        ];
		$dbo->update('mg_articoli_varianti', $array, ['id' => $id_record]);
		

        flash()->info(tr('Salvataggio completato!'));
		
		 break;
}
