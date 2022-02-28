<?php

include_once __DIR__.'/../../core.php';

switch (filter('op')) {
    case 'update':
        $nome = filter('nome');
        $nota = filter('nota');
        $colore = filter('colore');
		$web = filter('web');
		$meta_title = filter('meta_title');
		$meta_keywords = filter('meta_keywords');
		$meta_description = filter('meta_description');

        if (isset($nome) && isset($nota) && isset($colore)) {
            $dbo->query('UPDATE `mg_categorie` SET `nome`='.prepare($nome).', `nota`='.prepare($nota).', `colore`='.prepare($colore).', `web`='.prepare($web).', `meta_title`='.prepare($meta_title).', `meta_keywords`='.prepare($meta_keywords).', `meta_description`='.prepare($meta_description).' WHERE `id`='.prepare($id_record));
            flash()->info(tr('Salvataggio completato!'));
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio!'));
        }
		
		
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
                $dbo->update('mg_categorie', [
                    'immagine' => 'files/categorie_articoli/'.$filename,
                ], [
                    'id' => $id_record,
                ]);
            } else {
                flash()->warning(tr("Errore durante il caricamento dell'immagine!"));
            }
        }

        // Eliminazione file
        if (post('delete_immagine') !== null) {
            Uploads::delete($record['immagine'], [
                'id_module' => $id_module,
                'id_record' => $id_record,
            ]);

            $dbo->update('mg_categorie', [
                'immagine' => null,
            ], [
                'id' => $id_record,
            ]);
        }
		

        break;

    case 'add':
        $nome = filter('nome');
        $nota = filter('nota');
        $colore = filter('colore');
		$web = filter('web');
		$meta_title = filter('meta_title');
		$meta_keywords = filter('meta_keywords');
		$meta_description = filter('meta_description');

        $n = $dbo->fetchNum('SELECT * FROM `mg_categorie` WHERE `nome` LIKE '.prepare($nome));

        if (isset($nome)) {
            if ($n == 0) {
                $dbo->query('INSERT INTO `mg_categorie` (`nome`, `colore`, `nota`, `web`, `meta_title`, `meta_keywords`, `meta_description`) VALUES ('.prepare($nome).', '.prepare($colore).', '.prepare($nota).', '.prepare($web).', '.prepare($meta_title).', '.prepare($meta_keywords).', '.prepare($meta_description).')');

                $id_record = $dbo->lastInsertedID();

                if (isAjaxRequest()) {
                    echo json_encode(['id' => $id_record, 'text' => $nome]);
                }

                flash()->info(tr('Aggiunta nuova tipologia di _TYPE_', [
                    '_TYPE_' => 'categoria',
                ]));
            } else {
                flash()->error(tr('Esiste già una categoria con lo stesso nome!'));
            }
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio!'));
        }

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
                $dbo->update('mg_categorie', [
                    'immagine' => 'files/categorie_articoli/'.$filename,
                ], [
                    'id' => $id_record,
                ]);
            } else {
                flash()->warning(tr("Errore durante il caricamento dell'immagine!"));
            }
        }
		
		
        break;

    case 'delete':
        $id = filter('id');
        if (empty($id)) {
            $id = $id_record;
        }

        if ($dbo->fetchNum('SELECT * FROM `mg_articoli` WHERE `id_categoria`='.prepare($id).' OR `id_sottocategoria`='.prepare($id).'  OR `id_sottocategoria` IN (SELECT id FROM `mg_categorie` WHERE `parent`='.prepare($id).')') == 0) {
            $dbo->query('DELETE FROM `mg_categorie` WHERE `id`='.prepare($id));

            flash()->info(tr('Tipologia di _TYPE_ eliminata con successo!', [
                '_TYPE_' => 'categoria',
            ]));
        } else {
            flash()->error(tr('Esistono ancora alcuni articoli sotto questa categoria!'));
        }

        break;

    case 'row':
        $nome = filter('nome');
        $nota = filter('nota');
        $colore = filter('colore');
        $original = filter('id_original');
		$web = filter('web');
		$meta_title = filter('meta_title');
		$meta_keywords = filter('meta_keywords');
		$meta_description = filter('meta_description');

        if (isset($nome) && isset($nota) && isset($colore)) {
            if (isset($id_record)) {
                $dbo->query('UPDATE `mg_categorie` SET `nome`='.prepare($nome).', `nota`='.prepare($nota).', `colore`='.prepare($colore).', `web`='.prepare($web).', `meta_title`='.prepare($meta_title).', `meta_keywords`='.prepare($meta_keywords).', `meta_description`='.prepare($meta_description).' WHERE `id`='.prepare($id_record));
            } else {
                $dbo->query('INSERT INTO `mg_categorie` (`nome`,`nota`,`colore`, `parent`, `web`, `meta_title`, `meta_keywords`, `meta_description`) VALUES ('.prepare($nome).', '.prepare($nota).', '.prepare($colore).', '.prepare($original).', '.prepare($web).', '.prepare($meta_title).', '.prepare($meta_keywords).', '.prepare($meta_description).')');

                $id_record = $dbo->lastInsertedID();

                if (isAjaxRequest()) {
                    echo json_encode(['id' => $id_record, 'text' => $nome]);
                }
            }
			
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
                $dbo->update('mg_categorie', [
                    'immagine' => 'files/categorie_articoli/'.$filename,
                ], [
                    'id' => $id_record,
                ]);
            } else {
                flash()->warning(tr("Errore durante il caricamento dell'immagine!"));
            }
        }
			
			
			
            flash()->info(tr('Salvataggio completato!'));
            $id_record = $original;
        } else {
            flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio!'));
        }
		

		

        break;
}
