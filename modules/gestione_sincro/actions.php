<?php

include_once __DIR__.'/../../core.php';

$backup_dir = Backup::getDirectory();

switch (filter('op')) {
    
    case 'salva':
	
	 //prende i dati dalla form
	 $data_ultima_sincro = filter('data_ultima_sincro');
	 $ora_ultima_sincro = filter('ora_ultima_sincro');
	 $data_ultima_sincroweb = filter('data_ultima_sincroweb');
	 $ora_ultima_sincroweb = filter('ora_ultima_sincroweb');	 
	 $ultimo_ordine = filter('ultimo_ordine');
	 $rigenera_immagini = filter('rigenera_immagini');
	 $search_cron = filter('search_cron');
	 $cartella_web = filter('cartella_web');
	 $cartella_software = filter('cartella_software');
	 

//se il dato è presente...	 
if (isset($data_ultima_sincro)) {
    //Aggiorna la tabella zz_sincro
	$dbo->query("Update `zz_sincro` set `data_ultima_sincro` = '".$data_ultima_sincro."', 
	                                    `ora_ultima_sincro` = '".$ora_ultima_sincro."', 
										`data_ultima_sincroweb` = '".$data_ultima_sincroweb."', 
										`ora_ultima_sincroweb` = '".$ora_ultima_sincroweb."', 
										`ultimo_ordine` = '".$ultimo_ordine."', 
										`rigenera_immagini` = '".$rigenera_immagini."', 
										`cartella_web` = '".$cartella_web."', 
										`cartella_software` = '".$cartella_software."', 
										`search_cron` = '".$search_cron."'");
	//mostra esito a video	
	flash()->info(tr('Impostazioni Sincro aggiornate con successo!'.$data_ultima_sincro));
} else {
	flash()->error(tr('Ci sono stati alcuni errori durante il salvataggio!'));
}

	
    break;   


}