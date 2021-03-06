<?php

include_once __DIR__.'/../../core.php';

use Modules\Articoli\Articolo;

switch (post('op')) {
    case 'add':
        $idsede_partenza = post('idsede_partenza');
        $idsede_destinazione = post('idsede_destinazione');
        $qta = (post('direzione') == 'Carico manuale') ? post('qta') : -post('qta');
		//$variantearticolo = post('id_variante_articolo');

        if (post('direzione') == 'Carico manuale') {
            if ($idsede_partenza == 0 && $idsede_destinazione != 0) {
                $qta = -post('qta');
            } elseif ($idsede_partenza != 0 && $idsede_destinazione == 0) {
                $qta = post('qta');
                $idsede_partenza = post('idsede_destinazione');
                $idsede_destinazione = post('idsede_partenza');
            }
        } else {
            if ($idsede_partenza != 0 && $idsede_destinazione == 0) {
                $qta = -post('qta');
                $idsede_partenza = post('idsede_destinazione');
                $idsede_destinazione = post('idsede_partenza');
            } elseif ($idsede_partenza == 0 && $idsede_destinazione != 0) {
                $qta = post('qta');
            }
        }

        $articolo = Articolo::find(post('idarticolo'));
        $idmovimento = $articolo->movimenta($qta, post('movimento'), post('data'), 1);
        $dbo->query('UPDATE mg_movimenti SET idsede_azienda='.prepare($idsede_partenza).', idsede_controparte='.prepare($idsede_destinazione).' WHERE id='.prepare($idmovimento));
        //$dbo->query('UPDATE mg_movimenti INNER JOIN mg_articoli_varianti ON mg_movimenti.id_articolo_variante = mg_articoli_varianti.id SET mg_movimenti.descrizione_variante = mg_articoli_varianti.descrizione');
        break;
		
		case 'updmovimento':
        $idmovimento = post('id_record');


        $query = 'Update mg_movimenti set id_articolo_variante = '.prepare(post('id_articolo_variante')).' WHERE id='.prepare($idmovimento);
		
        if ($dbo->query($query)) {
            flash()->info(tr('Movimento Aggiornato!'));
        }
		
		
		
        break;	
		
}
