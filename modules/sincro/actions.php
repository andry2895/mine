<?php

include_once __DIR__.'/../../core.php';

$backup_dir = Backup::getDirectory();

class log 
{
   public $nomelog = 'log.txt';
	
   function scrivi($stringa)
   {
      $data = date('Y-m-d, H:i:s');
      $log = $data."\t".$stringa."\n";

      // Apertura file
      if($file = @fopen($this->nomelog, 'a'))
      {
         fwrite($file, $log);
         fclose($file);
      }		
      else
      {
         // Eventuale codice per gestire un errore di scrittura
      }		
   }
}


switch (filter('op')) {
    case 'gestsinc':
	
//cerco le categorie che hanno subito un aggiornamento e che hanno valore web = 1	
$results = $dbo->fetchArray("SELECT * FROM `mg_categorie` WHERE `updated_at` > (Select CONCAT (`data_ultima_sincro`, ' ', `ora_ultima_sincro`) as `orario` from `zz_sincro`) and `web` = 1 order by `id` ASC");


   $log_errore = new log();
   $log_errore->scrivi("Controllo categorie");


//imposto il ciclo 1 - categorie che hanno subito un aggiornamento e che hanno valore web = 1
foreach ($results as $result) {
	
	
   $log_errore = new log();
   $log_errore->scrivi("Categoria da aggiornare sul web:" . $result['nome']);
    
	//se la categoria non è ancora sul web
	if ($result['idweb'] == Null)
	{	
	
	//se è categoria madre
    if ($result['parent'] == Null)
	{
		//inserisco in tabella ps_category
		$dbo->query("INSERT INTO `ps_category` (`id_parent`, `id_shop_default`, `level_depth`, `nleft`, `nright`, `active`, `date_add`, `date_upd`, `position`, `is_root_category`) VALUES ('2', '1', '2', '0', '0', '1', Now(), Now(), '0', '0')");
		$id_categoria = $dbo->lastInsertedID();
		//inserisco il valore idweb nella tabella delle categorie
		$dbo->query("Update `mg_categorie` set `idweb` = ". $id_categoria . " where id = ".$result['id']);
				

	}
    else // se è una sottocategoria
   {
	   //cerco la sua categoria madre
	   $resultsparent = $dbo->fetchArray('SELECT idweb FROM `mg_categorie` WHERE id = '. $result['parent']);
	   //imposto il ciclo
	   foreach ($resultsparent as $resultparent) {
		   		  
			//inserisco in tabella ps_category
			$dbo->query("INSERT INTO `ps_category` (`id_parent`, `id_shop_default`, `level_depth`, `nleft`, `nright`, `active`, `date_add`, `date_upd`, `position`, `is_root_category`) VALUES ('".$resultparent['idweb']."', '1', '2', '0', '0', '1', Now(), Now(), '0', '0')");
			$id_categoria = $dbo->lastInsertedID();
			//inserisco il valore idweb nella tabella delle categorie
            $dbo->query("Update `mg_categorie` set `idweb` = ". $id_categoria . " where id = ".$result['id']); 
		   
	   }
		
   }
	    //inserisco i valori nelle altre tabelle di prestashop per la categoria
		//inserisco in tabella ps_category_group 
	    $dbo->query("INSERT INTO `ps_category_group` (`id_category`, `id_group`) VALUES ('".$id_categoria."','1')");
	    $dbo->query("INSERT INTO `ps_category_group` (`id_category`, `id_group`) VALUES ('".$id_categoria."','2')");
	    $dbo->query("INSERT INTO `ps_category_group` (`id_category`, `id_group`) VALUES ('".$id_categoria."','3')");
		//inserisco in tabella ps_category_lang	
	    $dbo->query("INSERT INTO `ps_category_lang` (`id_category`, `id_shop`, `id_lang`, `name`, `link_rewrite`, `description`, `meta_title`, `meta_keywords`, `meta_description`) VALUES ('".$id_categoria."','1','1','".str_replace("'","\'",$result['nome'])."', REPLACE(LOWER('".str_replace("'","\'",$result['nome'])."'), ' ', '-'), '".str_replace("'","\'",$result['nota'])."', '".str_replace("'","\'",$result['meta_title'])."', '".str_replace("'","\'",$result['meta_keywords'])."', '".str_replace("'","\'",$result['meta_description'])."')");
        //inserisco in tabella ps_category_shop 
		$dbo->query("INSERT INTO `ps_category_shop` (`id_category`, `id_shop`, `position`) VALUES ('".$id_categoria."', '1', '0')");	
		
		
		
		//sposto l'immagine
	   if (file_exists(dirname(__FILE__).'/../../'.$result['immagine'])) {
    
		/* Source File URL */
		$remote_file_url = dirname(__FILE__).'/../../'.$result['immagine'];

		/* New file name and path for this file */
		 $local_file = dirname(__FILE__).'/../../../img/c/'.$id_categoria.'.jpg';

		/* Copy the file from source url to server */
		$copy = copy( $remote_file_url, $local_file );
	
        }
		
		
		
		
	//se la categoria è già sul web 	
	} else {
		
		//Aggiorno sul web la categoria
	    $dbo->query("Update `ps_category_lang` set `name` = '".str_replace("'","\'",$result['nome'])."', `link_rewrite` = REPLACE(LOWER('".str_replace("'","\'",$result['nome'])."'), ' ', '-'), `description` = '".str_replace("'","\'",$result['nota'])."', `meta_title` = '".str_replace("'","\'",$result['meta_title'])."', `meta_keywords` = '".str_replace("'","\'",$result['meta_keywords'])."', `meta_description`='".str_replace("'","\'",$result['meta_description'])."' where `id_category` = ".$result['idweb']);
		
		
		//sposto l'immagine
	   if (file_exists(dirname(__FILE__).'/../../'.$result['immagine'])) {
    
		/* Source File URL */
		$remote_file_url = dirname(__FILE__).'/../../'.$result['immagine'];

		/* New file name and path for this file */
		 $local_file = dirname(__FILE__).'/../../../img/c/'.$result['idweb'].'.jpg';

		/* Copy the file from source url to server */
		$copy = copy( $remote_file_url, $local_file );
	
        }		
		
		
		
	}	
	
	

	
} // Fine ciclo 1 - Categorie che hanno subito una modifica e sono con valore web = 1	
	
	
	
   $log_errore = new log();
   $log_errore->scrivi("Controllo Produttori");	
	

//cerco i produttori da sincronizzare che hanno subito quindi una modifica e sono a valore web = 1	
$resultsproduttori = $dbo->fetchArray("SELECT * FROM `an_anagrafiche` WHERE `idanagrafica` in (Select `idanagrafica` from `an_tipianagrafiche_anagrafiche` where `idtipoanagrafica` = 7) and `updated_at` > (Select CONCAT (`data_ultima_sincro`, ' ', `ora_ultima_sincro`) as `orario` from `zz_sincro`) and `web` = 1 order by `idanagrafica` ASC");	
//imposto il ciclo 2
foreach ($resultsproduttori as $resultproduttori) {
	
	
	   $log_errore = new log();
       $log_errore->scrivi("Produttore da aggiornare sul web:" . $resultproduttori['ragione_sociale']);
	
	
	//se il produttore non è ancora sul web
	if ($resultproduttori['idweb'] == Null)
	{   
        //inserisce nella tabella ps_manufacturer 
		$dbo->query("INSERT INTO `ps_manufacturer` (`name`, `date_add`, `date_upd`, `active`) VALUES ('".str_replace("'","\'",$resultproduttori['ragione_sociale'])."', Now(), Now(), '1')");	
		//prendo l'id dell'ultimo inserimento dalla tabella ps_manufacturer
		$id_produttore = $dbo->lastInsertedID();
		//metto il valore nel campo idweb
		$dbo->query("Update `an_anagrafiche` set `idweb` = ". $id_produttore . " where idanagrafica = ".$resultproduttori['idanagrafica']); 
		//inserisce nella tabella ps_manufacturer_lang
		$dbo->query("INSERT INTO `ps_manufacturer_lang` (`id_manufacturer`, `id_lang`, `description`) VALUES ('".$id_produttore."', '1', '".str_replace("'","\'",$resultproduttori['note'])."')");	
		//inserisce nella tabella ps_manufacturer_shop
		$dbo->query("INSERT INTO `ps_manufacturer_shop` (`id_manufacturer`, `id_shop`) VALUES ('".$id_produttore."', '1')");	
	} else {
		$dbo->query("Update `ps_manufacturer` set `name` = '". str_replace("'","\'",$resultproduttori['ragione_sociale']) . "', `date_upd` = Now(), active = '1' where `id_manufacturer` = ".$resultproduttori['idweb']); 
		$dbo->query("Update `ps_manufacturer_lang` set `description` = '<p>". str_replace("'","\'",$resultproduttori['note']) . "</p>' where `id_manufacturer` = ".$resultproduttori['idweb']); 
	}
	
} // fine ciclo 2 - produttori da sincronizzare che hanno subito quindi una modifica e sono a valore web = 1

//cerco i prodotti da sincronizzare	che hanno subito un aggiornamento e che hanno valore web = 1
$resultsprodotti = $dbo->fetchArray("SELECT * FROM `mg_articoli` WHERE updated_at > (Select CONCAT (`data_ultima_sincro`, ' ', `ora_ultima_sincro`) as `orario` from `zz_sincro`) and web = '1' order by id ASC");	

   $log_errore = new log();
   $log_errore->scrivi("Controllo Prodotti");	


// imposto ciclo 3
foreach ($resultsprodotti as $resultprodotti) {
	
	
	   $log_errore = new log();
       $log_errore->scrivi("Prodotto da aggiornare sul web:" . $resultprodotti['descrizione']);	
	
	
	//se è specificato il produttore mi prendo l'informazione
	if ($resultprodotti['id_produttore'] > 0) 
	{
		//mi prendo l'idweb
		$resultsppp = $dbo->fetchArray('SELECT idweb FROM `an_anagrafiche` WHERE idanagrafica = ' . $resultprodotti['id_produttore']);
		$codiceproduttore = Null;
        foreach ($resultsppp as $resultppp) 
		{
			$codiceproduttore = $resultppp['idweb'];
		}
	}
	
	
	if ($resultprodotti['id_categoria'] > 0) 
	{
		//mi prendo l'idweb
		$resultsCCC = $dbo->fetchArray('SELECT idweb FROM `mg_categorie` WHERE id = ' . $resultprodotti['id_categoria']);
		$codicecategoria = Null;
        foreach ($resultsCCC as $resultCCC) 
		{
			$codicecategoria = $resultCCC['idweb'];
		}
	}
	
	
	if ($resultprodotti['id_sottocategoria'] > 0) 
	{
		//mi prendo l'idweb
		$resultsSSS = $dbo->fetchArray('SELECT idweb FROM `mg_categorie` WHERE id = ' . $resultprodotti['id_sottocategoria']);
		$codicesottocategoria = Null;
        foreach ($resultsSSS as $resultSSS) 
		{
			$codicesottocategoria = $resultSSS['idweb'];
		}
	}
	
	
	//se il prodotto non è ancora sul web
	if ($resultprodotti['idweb'] == Null)
	{   
        //inserisce nella tabella ps_product 
		$dbo->query("INSERT INTO `ps_product` (`id_manufacturer`, `id_category_default`, `id_tax_rules_group`, `ean13`, `reference`, `price`, `wholesale_price`, `width`, `height`, `depth`, `weight`, `active`, `date_add`, `date_upd`) VALUES ('".$codiceproduttore."', '".$codicecategoria."', '1', '".$resultprodotti['barcode']."', '".str_replace("'","\'",$resultprodotti['codice'])."', ".$resultprodotti['prezzo_vendita']/1.22 .", ".$resultprodotti['prezzo_acquisto']/1.22 .", '', '', '', '".$resultprodotti['peso_lordo']."', '1', Now(), Now())");	
		//prendo l'id dell'ultimo inserimento dalla tabella ps_manufacturer
		$id_prodotto = $dbo->lastInsertedID();
		
		//metto il valore nel campo idweb
		$dbo->query("Update `mg_articoli` set `idweb` = ". $id_prodotto . " where id = ".$resultprodotti['id']); 
		
		if ($resultprodotti['qta'] > 0) 
	    {
			
				   $log_errore = new log();
                   $log_errore->scrivi("Inserimento quantità:" . $resultprodotti['qta']);	
			
			
			
		$dbo->query("INSERT INTO `ps_stock_available` (`id_product`, `id_product_attribute`, `id_shop`, `id_shop_group`, `quantity`, `depends_on_stock`, `out_of_stock`) value ('".$id_prodotto."','0','1','0','".$resultprodotti['qta']."','0','0')");	
		
		           $log_errore = new log();
                   $log_errore->scrivi("INSERT INTO `ps_stock_available` (`id_product`, `id_product_attribute`, `id_shop`, `id_shop_group`, `quantity`, `depends_on_stock`, `out_of_stock`) value ('".$id_prodotto."','0','1','0','".$resultprodotti['qta']."','0','0')");
		
		}
		
		
		
		//se c'è la categoria la inserisco
		if ($codicecategoria <> Null) 
	    {
		//inserisce la categoria nella tabella ps_category_product
		$dbo->query("INSERT INTO `ps_category_product` (`id_category`, `id_product`) VALUES ('".$codicecategoria."', '".$id_prodotto."')");	
		}
		
		//se c'è la sottocategoria la inserisco
		if ($codicesottocategoria <> Null) 
	    {
		//inserisce la sottocategoria nella tabella ps_category_product
		$dbo->query("INSERT INTO `ps_category_product` (`id_category`, `id_product`) VALUES ('".$codicesottocategoria."', '".$id_prodotto."')");	
		}
		
		//inserisce nella tabella ps_product_lang
		$dbo->query("INSERT INTO `ps_product_lang` (`id_product`,`id_shop`, `id_lang`, `description`, `link_rewrite`, `meta_description`, `meta_keywords`, `meta_title`, `name`) VALUES ('".$id_prodotto."', '1', '1', '".str_replace("'","\'",$resultprodotti['note'])."', 
		REPLACE(LOWER('".str_replace("'"," ",$resultprodotti['descrizione'])."'), ' ', '-'), 
		'".str_replace("'","\'",$resultprodotti['meta_description'])."', 
		'".str_replace("'","\'",$resultprodotti['meta_keywords'])."', 
		'".str_replace("'","\'",$resultprodotti['meta_title'])."', 
		'".str_replace("'","\'",$resultprodotti['descrizione'])."')");	
		
		//inserisce nella tabella ps_product_shop 
		$dbo->query("INSERT INTO `ps_product_shop` (`id_product`, `id_shop`, `id_category_default`, `id_tax_rules_group`, `price`, `wholesale_price`, `date_add`, `date_upd`, `active`) VALUES ('".$id_prodotto."', '1', '".$codicecategoria."', '1', '".$resultprodotti['prezzo_vendita']/1.22 ."', '".$resultprodotti['prezzo_acquisto']/1.22 ."', Now(), Now(), '1')");	
		
	
		//inserisco le varianti del prodotto mai inserite
	$variantiprodotti = $dbo->fetchArray("SELECT * FROM `mg_articoli_varianti` where id_articolo = ".$id_prodotto." and id_web = 0 and deleted_at is null");	
	$totalecombinazioni = 0;
	$primacomb = 1;
    foreach ($variantiprodotti as $variantiprodotto) {
	$totalecombinazioni = $totalecombinazioni + $variantiprodotto['qta'];	
        //inserisce nella tabella ps_product_attribute 
		$dbo->query("INSERT INTO `ps_product_attribute` (id_product, ean13, quantity, unit_price_impact, minimal_quantity,default_on) 
		VALUES (".$id_prodotto.",'".$variantiprodotto['ean13']."',".$variantiprodotto['qta'].",".$variantiprodotto['impatto_prezzo'].",".$variantiprodotto['qta_minima'].",".$primacomb.")");	
		//prendo l'id dell'ultima variante del prodotto inserita
		$id_articolovariante = $dbo->lastInsertedID();		
		$dbo->query("Update `mg_articoli_varianti` set `id_web` = '".$id_articolovariante."' where `id` = ".$variantiprodotto['id']);

		//inserisce nella tabella ps_product_attribute_shop 
		$dbo->query("INSERT INTO `ps_product_attribute_shop` (id_product, id_product_attribute, id_shop, unit_price_impact, minimal_quantity,default_on) 
		VALUES (".$id_prodotto.",'".$id_articolovariante."',1,".$variantiprodotto['impatto_prezzo'].",".$variantiprodotto['qta_minima'].",".$primacomb.")");
		
		
		
						   $log_errore = new log();
                           $log_errore->scrivi("Aggiornamento quantità:" . $variantiprodotto['qta'] . "per variante:" . $id_articolovariante);	
		//inserisce nella tabella ps_stock_available 
		$dbo->query("INSERT INTO `ps_stock_available` (id_product, id_product_attribute, id_shop, quantity) 
		VALUES (".$id_prodotto.",".$id_articolovariante.",1,".$variantiprodotto['qta'].")");		
		
        $elencoattributivariante = $dbo->fetchArray("SELECT * FROM `mg_varianti` where id_articolo_variante = " . $variantiprodotto['id']);
		
	    foreach ($elencoattributivariante as $attributovariante) {
			if ($attributovariante['id_attributo'] <> '') 
			{	
			$dbo->query("INSERT INTO `ps_product_attribute_combination` (`id_attribute`, `id_product_attribute`) 
			VALUES (".$attributovariante['id_attributo'].",".$id_articolovariante.")");	
			$id_combinazione = $dbo->lastInsertedID();	
			$dbo->query("Update `mg_varianti` set `id_web` = '".$id_combinazione."' where `id` = ".$attributovariante['id']);
			}
		}	
	$primacomb = Null;
    }
	
		//inserisce totale delle combinazioni nella tabella ps_stock_available 
		if ($totalecombinazioni > 0) {
			
			       $log_errore = new log();
                   $log_errore->scrivi("Aggiornamento quantità totali:" . $resultprodotti['qta']);	
			
		$dbo->query("INSERT INTO `ps_stock_available` (id_product, id_product_attribute, id_shop, quantity) 
		VALUES (".$id_prodotto.",0,1,".$totalecombinazioni.")");	
		}
	
		
	
	
	} else { // se il prodotto è già sul web devo modificarlo (e assicurarmi che si accenda se era spento) 
		$dbo->query("Update `ps_product` set `active` = '1', `id_manufacturer` = '".$codiceproduttore."', `id_category_default` = '".$codicecategoria."', `ean13` = '".$resultprodotti['barcode']."', `reference` = '".str_replace("'","\'",$resultprodotti['codice'])."', `price`=".$resultprodotti['prezzo_vendita']/1.22 .", `wholesale_price` = ".$resultprodotti['prezzo_acquisto']/1.22 .", `width` = '', `height` = '', `depth` = '', `weight` = '".$resultprodotti['peso_lordo']."', `date_upd` = Now() where `id_product` = ".$resultprodotti['idweb']); 
		$dbo->query("Update `ps_product_lang` set `description` = '".str_replace("'","\'",$resultprodotti['note'])."', `link_rewrite` = 
		REPLACE(LOWER('".str_replace("'","\'",$resultprodotti['descrizione'])."'), ' ', '-'), 
		`meta_description`='".str_replace("'","\'",$resultprodotti['meta_description'])."', 
		`meta_keywords`='".str_replace("'","\'",$resultprodotti['meta_keywords'])."', 
		`meta_title`='".str_replace("'","\'",$resultprodotti['meta_title'])."', 
		`name`='".str_replace("'","\'",$resultprodotti['descrizione'])."'  
		where `id_product` = ".$resultprodotti['idweb']); 
		$dbo->query("Update `ps_product_shop` set `active` = '1', `id_category_default`='".$codicecategoria."', `price`='".$resultprodotti['prezzo_vendita']/1.22 ."', `wholesale_price`='".$resultprodotti['prezzo_acquisto']/1.22 ."', `date_upd` = Now()  where `id_product` = ".$resultprodotti['idweb']); 
        
		
		           $log_errore = new log();
                   $log_errore->scrivi("Aggiornamento quantità:" . $resultprodotti['qta']);	
		
		
		$dbo->query("Update `ps_stock_available` set `quantity` = '".$resultprodotti['qta']."' where `id_product` = ".$resultprodotti['idweb']);
	
	
	
	
	    $log_errore = new log();
        $log_errore->scrivi("Update `ps_stock_available` set `quantity` = '".$resultprodotti['qta']."' where `id_product` = ".$resultprodotti['idweb']);	
	
	//elimino sul web le varianti eliminate dal gestionale
	$dbo->query("delete from `ps_product_attribute` where id_product_attribute in (select id_web from mg_articoli_varianti where deleted_at is not null)");
	$dbo->query("delete from `ps_product_attribute_shop` where id_product_attribute in (select id_web from mg_articoli_varianti where deleted_at is not null)");	
    //$dbo->query("delete from `ps_stock_available` where id_product_attribute in (select id_web from mg_articoli_varianti where deleted_at is not null)");	

	//inserisco le varianti del prodotto mai inserite
	$variantiprodotti = $dbo->fetchArray("SELECT * FROM `mg_articoli_varianti` where id_articolo = ".$resultprodotti['idweb']." and id_web = 0 and deleted_at is null");	
	$totalecombinazioni = 0;
	$primacomb = 1;
    foreach ($variantiprodotti as $variantiprodotto) {
	$totalecombinazioni = $totalecombinazioni + $variantiprodotto['qta'];	
        //inserisce nella tabella ps_product_attribute 
		$dbo->query("INSERT INTO `ps_product_attribute` (id_product, ean13, quantity, unit_price_impact, minimal_quantity,default_on) 
		VALUES (".$resultprodotti['idweb'].",'".$variantiprodotto['ean13']."',".$variantiprodotto['qta'].",".$variantiprodotto['impatto_prezzo'].",".$variantiprodotto['qta_minima'].",".$primacomb.")");	
		//prendo l'id dell'ultima variante del prodotto inserita
		$id_articolovariante = $dbo->lastInsertedID();		
		$dbo->query("Update `mg_articoli_varianti` set `id_web` = '".$id_articolovariante."' where `id` = ".$variantiprodotto['id']);
    
		//inserisce nella tabella ps_product_attribute_shop 
		$dbo->query("INSERT INTO `ps_product_attribute_shop` (id_product, id_product_attribute, id_shop, unit_price_impact, minimal_quantity,default_on) 
		VALUES (".$resultprodotti['idweb'].",'".$id_articolovariante."',1,".$variantiprodotto['impatto_prezzo'].",".$variantiprodotto['qta_minima'].",".$primacomb.")");	
		
		
						   $log_errore = new log();
                   $log_errore->scrivi("Inserimento quantità:" . $variantiprodotto['qta'] . " su variante: " .$id_articolovariante);
		//inserisce nella tabella ps_stock_available 
		$dbo->query("INSERT INTO `ps_stock_available` (id_product, id_product_attribute, id_shop, quantity) 
		VALUES (".$resultprodotti['idweb'].",".$id_articolovariante.",1,".$variantiprodotto['qta'].")");			
	
	    $elencoattributivariante = $dbo->fetchArray("SELECT * FROM `mg_varianti` where id_articolo_variante = " . $variantiprodotto['id']);
	    foreach ($elencoattributivariante as $attributovariante) {
			if ($attributovariante['id_attributo'] <> '') 
			{
			$dbo->query("INSERT INTO `ps_product_attribute_combination` (`id_attribute`, `id_product_attribute`) 
			VALUES (".$attributovariante['id_attributo'].", ".$id_articolovariante.")");	
			$id_combinazione = $dbo->lastInsertedID();	
			$dbo->query("Update `mg_varianti` set `id_web` = '".$id_combinazione."' where `id` = ".$attributovariante['id']);
			}
		}	
	$primacomb = Null;
    }
	
	

	//modifico le varianti del prodotto aggiornate
	$variantiprodotti = $dbo->fetchArray("SELECT * FROM `mg_articoli_varianti` where id_articolo = ".$resultprodotti['idweb']." and id_web > 0 AND deleted_at is null and updated_at > (Select CONCAT (`data_ultima_sincro`, ' ', `ora_ultima_sincro`) as `orario` from `zz_sincro`)");	
    foreach ($variantiprodotti as $variantiprodotto) {
    $dbo->query("Update `ps_product_attribute` set `ean13` = '".$variantiprodotto['ean13']."', unit_price_impact = ".$variantiprodotto['impatto_prezzo'].", minimal_quantity = ".$variantiprodotto['qta_minima'].", quantity = ".$variantiprodotto['qta']." where `id_product_attribute` = ".$variantiprodotto['id_web']);
	$dbo->query("Update `ps_product_attribute_shop` set unit_price_impact = ".$variantiprodotto['impatto_prezzo'].", minimal_quantity = ".$variantiprodotto['qta_minima']." where `id_product_attribute` = ".$variantiprodotto['id_web']);
    
	               $log_errore = new log();
                   $log_errore->scrivi("Modifico quantità:" . $variantiprodotto['qta'] . " su variante: " .$variantiprodotto['id_web']);
	
	
	$dbo->query("Update `ps_stock_available` set quantity = ".$variantiprodotto['qta']." where `id_product_attribute` = ".$variantiprodotto['id_web']);
	$totalecombinazioni = $totalecombinazioni + $variantiprodotto['qta'];
	}		
	
		//inserisce totale delle combinazioni nella tabella ps_stock_available 
		
		           $log_errore = new log();
                   $log_errore->scrivi("Aggiornamento quantità totali:" . $resultprodotti['qta']);	
		
		
		$dbo->query("update `ps_stock_available` set quantity = ".$resultprodotti['qta']." where id_product = ".$resultprodotti['idweb']." and id_product_attribute = 0"); 	
		
		//$dbo->query("INSERT INTO `ps_stock_available` (id_product, id_product_attribute, id_shop, quantity) 
		//VALUES (".$resultprodotti['idweb'].",0,1,".$resultprodotti['qta'].")");
		
		
	}
	
	
	
	
	
} // fine ciclo 3 - prodotti da sincronizzare	che hanno subito un aggiornamento e che hanno valore web = 1	


//Mi Prendo i valori delle cartelle su sito e software
$resultssincro = $dbo->fetchArray("SELECT * FROM `zz_sincro`");	
foreach ($resultssincro as $resultsincro) {
	$cartella_web = $resultsincro['cartella_web'];
	$cartella_software = $resultsincro['cartella_software'];
}


//cerco su tutti i prodotti se ci sono immagini nuove da sincronizzare.	
$resultsprodotti = $dbo->fetchArray("SELECT * FROM `mg_articoli` order by id ASC");	
   
   $log_errore = new log();
   $log_errore->scrivi("Controllo Immagini");	

// imposto ciclo 4
foreach ($resultsprodotti as $resultprodotti) {
	
	
		   $log_errore = new log();
           $log_errore->scrivi("Prodotto con immagini da aggiornare sul web:" . $resultprodotti['descrizione']);	
	
	
        //controllo quante immagini del prodotto ci sono già caricate
        $resultsimmpresenti = $dbo->fetchArray("SELECT * FROM `ps_image` where `id_product` = '".$resultprodotti['idweb']."'");
		$conteggioimmagini = 1;

		foreach ($resultsimmpresenti as $resultimmpresenti) {
		$conteggioimmagini = $conteggioimmagini + 1;	
		}

	    //controllo se il prodotto ha delle immagini nuove che sono state inserite
        $resultsimmagini = $dbo->fetchArray("SELECT * FROM `zz_files` WHERE idweb is Null and `id_module` = '21' and `id_record` = '".$resultprodotti['id']."' order by `id` ASC");	
        $destinazione = dirname(__FILE__).'/../../../'. $cartella_web .'/img/p/';
	

		foreach ($resultsimmagini as $resultimmagini) {
		if ($conteggioimmagini > 1) 
		{	
        //inserisce l'immmagine		
		$dbo->query("INSERT INTO `ps_image` (`id_product`, `position`, `cover`) VALUES ('".$resultprodotti['idweb']."', '".$conteggioimmagini."', Null)");		
		} else {
		$dbo->query("INSERT INTO `ps_image` (`id_product`, `position`, `cover`) VALUES ('".$resultprodotti['idweb']."', '".$conteggioimmagini."', '1')");		
		}
		$id_immagine = $dbo->lastInsertedID();
		
		$dbo->query("INSERT INTO `ps_image_lang` (`id_image`, `id_lang`, `legend`) VALUES ('".$id_immagine."', '1', '".str_replace("'","\'",$resultprodotti['descrizione'])."')");		
		
		if ($conteggioimmagini > 1) 
		{	
		$dbo->query("INSERT INTO `ps_image_shop` (`id_product`, `id_image`, `id_shop`, `cover`) VALUES ('".$resultprodotti['idweb']."', '".$id_immagine."', '1', Null)");		
		} else {
		$dbo->query("INSERT INTO `ps_image_shop` (`id_product`, `id_image`, `id_shop`, `cover`) VALUES ('".$resultprodotti['idweb']."', '".$id_immagine."', '1', '1')");			
		}
	
	    
	    if(!file_exists($destinazione.implode('/',str_split($id_immagine))."/")) {
        mkdir($destinazione.implode('/',str_split($id_immagine))."/", 0777); 
	    }
	    copy(dirname(__FILE__).'/../../files/articoli/'.$resultimmagini['filename'], $destinazione.implode('/',str_split($id_immagine))."/".$id_immagine.".jpg");	
		
		$conteggioimmagini = $conteggioimmagini + 1;
		$dbo->query("Update `zz_files` set `idweb` = ". $id_immagine . " where id = ".$resultimmagini['id']); 
	    }	
	
	
} // fine ciclo 4 - su tutti i prodotti se ci sono immagini nuove da sincronizzare


//cerco i prodotti spenti da sincronizzare spegnendoli sul web	
$resultsprodottispenti = $dbo->fetchArray("SELECT * FROM `mg_articoli` WHERE updated_at > (Select CONCAT (`data_ultima_sincro`, ' ', `ora_ultima_sincro`) as `orario` from `zz_sincro`) and web = '0' and idweb is not null order by id ASC");	

   $log_errore = new log();
   $log_errore->scrivi("Controllo prodotti da spegnere sul web");	

foreach ($resultsprodottispenti as $resultprodottispenti) {
	
			$log_errore = new log();
           $log_errore->scrivi("Prodotto da spegnere sul web:" . $resultprodottispenti['descrizione']);	
	
	
	$dbo->query("Update `ps_product` set `active` = '0' where `id_product` = ".$resultprodottispenti['idweb']); 
	$dbo->query("Update `ps_product_shop` set `active` = '0' where `id_product` = ".$resultprodottispenti['idweb']); 
}


//da zz_sincro prendo le pagine cron da inviare
$resultsmandacron = $dbo->fetchArray("Select * from `zz_sincro`");	
foreach ($resultsmandacron as $resultmandacron) {

 include_once $resultmandacron['rigenera_immagini'];
 include_once $resultmandacron['search_cron'];

}

//salvo la nuova data e ora della sincronizzazione
$dbo->query("Update `zz_sincro` set `data_ultima_sincro` = CURDATE(), `ora_ultima_sincro` = CURTIME()"); 
	
	

flash()->info(tr('Sincronizzazione del gestionale sul web eseguita!'));
	
        break;

    case 'websinc':
	
//cerco i clienti registrati sul web non ancora inseriti sul gestionale
$resultsclientiweb = $dbo->fetchArray("SELECT * FROM `ps_customer` where `deleted` = 0 and `id_customer` not in (Select `idwebcustomer` from `an_anagrafiche`)");
foreach ($resultsclientiweb as $resultclientiweb) {

	//cerco gli indirizzi dei clienti registrati non ancora inseriti nel gestionale
	$resultsindirizzi = $dbo->fetchArray("SELECT * FROM `ps_address` where `id_customer` = '" . $resultclientiweb['id_customer']. "'");
	//inserisaco il cliente quando sono alla prima sede, e poi le altre sedi se ci sono 
	$ConteggioSedi = 1;
	foreach ($resultsindirizzi as $resultindirizzi) {
		//verifico che sono alla prima sede
		if ($ConteggioSedi == 1)
	    {
		//inserisco la nuova anagrafica	
		$dbo->query("INSERT INTO `an_anagrafiche` (`piva`, `indirizzo`, `citta`, `cap`, `nome`, `cognome`, `telefono`, `cellulare`, `data_nascita`, `email`, `idwebcustomer`) 
		                                   VALUES ('".$resultindirizzi['vat_number']."',
                                                   '".$resultindirizzi['address1']."',
												   '".$resultindirizzi['city']."',
												   '".$resultindirizzi['postcode']."',
												   '".str_replace("'","\'",$resultindirizzi['firstname'])."',
												   '".str_replace("'","\'",$resultindirizzi['lastname'])."',
												   '".$resultindirizzi['phone']."',
												   '".$resultindirizzi['phone_mobile']."',	
                                                   '".$resultclientiweb['birthday']."',	
                                                   '".$resultclientiweb['email']."',	 												   
										           '".$resultclientiweb['id_customer']."')");	
        
		$id_anagrafica = $dbo->lastInsertedID();
		//se ha una ragione sociale la inserisco come azienda... 
		if 	($resultindirizzi['company'] <> Null) {		
		$dbo->query("Update `an_anagrafiche` set `ragione_sociale` = '".str_replace("'","\'",$resultindirizzi['company'])."', `tipo` = 'Azienda' where `idanagrafica` = '". $id_anagrafica ."'");  		
		} else {
		// ...altrimenti la inserisco come privato	
		$dbo->query("Update `an_anagrafiche` set `ragione_sociale` = '".str_replace("'","\'",$resultindirizzi['lastname']). " " .str_replace("'","\'",$resultindirizzi['firstname']). "', `tipo` = 'Privato' where `idanagrafica` = '". $id_anagrafica ."'");  	
		}
		
		//inserisco questa nuova anagrafica come cliente
		$dbo->query("INSERT INTO `an_tipianagrafiche_anagrafiche` (`idtipoanagrafica`, `idanagrafica`) VALUES ('1', '".$id_anagrafica."')");

		//inserisco la sede
		$dbo->query("INSERT INTO `an_sedi` (`nomesede`, `piva`, `indirizzo`, `citta`, `cap`, `telefono`, `cellulare`, `email`, `idwebsede`, `idanagrafica`) 
		                            VALUES ('".str_replace("'","\'",$resultindirizzi['alias'])."',
       									    '".$resultindirizzi['vat_number']."',
											'".str_replace("'","\'",$resultindirizzi['address1'])."',
											'".str_replace("'","\'",$resultindirizzi['city'])."',
											'".$resultindirizzi['postcode']."',
											'".$resultindirizzi['phone']."',
											'".$resultindirizzi['phone_mobile']."',
											'".$resultindirizzi['email']."',
											'".$resultindirizzi['id_address']."',
		                                    '".$id_anagrafica."')");
		
		} else {
			
		//inserisco le altre sedi
		$dbo->query("INSERT INTO `an_sedi` (`nomesede`, `piva`, `indirizzo`, `citta`, `cap`, `telefono`, `cellulare`, `email`, `idwebsede`, `idanagrafica`) 
		                            VALUES ('".str_replace("'","\'",$resultindirizzi['alias'])."',
       									    '".$resultindirizzi['vat_number']."',
											'".str_replace("'","\'",$resultindirizzi['address1'])."',
											'".str_replace("'","\'",$resultindirizzi['city'])."',
											'".$resultindirizzi['postcode']."',
											'".$resultindirizzi['phone']."',
											'".$resultindirizzi['phone_mobile']."',
											'".$resultindirizzi['email']."',
											'".$resultindirizzi['id_address']."',
		                                    '".$id_anagrafica."')");			
			
		}

		$ConteggioSedi = $ConteggioSedi + 1;
	}

	
}	



//cerco i clienti registrati sul web che hanno modificato i loro dati
$resultsclientiweb = $dbo->fetchArray("SELECT * FROM `ps_customer` where `deleted` = 0 and `id_customer` in (Select `idwebcustomer` from `an_anagrafiche`)");
foreach ($resultsclientiweb as $resultclientiweb) {

	//cerco gli indirizzi dei clienti registrati non ancora inseriti nel gestionale
	$resultsindirizzi = $dbo->fetchArray("SELECT * FROM `ps_address` where `id_customer` = '" . $resultclientiweb['id_customer']. "' and `date_upd` > (Select CONCAT (`data_ultima_sincroweb`, ' ', `ora_ultima_sincroweb`) as `orario` from `zz_sincro`)");
	//ciclo sulle sedi	
	foreach ($resultsindirizzi as $resultindirizzi) {

		        
		//se ha una ragione sociale la modifico come azienda... 
		if 	($resultindirizzi['company'] <> Null) {		
		$dbo->query("Update `an_anagrafiche` set `ragione_sociale` = '".$resultindirizzi['company']."', 
		                                         `tipo` = 'Azienda',
		                                           `piva` = '".$resultindirizzi['vat_number']."',
                                                   `indirizzo` = '".str_replace("'","\'",$resultindirizzi['address1'])."',
												   `citta` = '".str_replace("'","\'",$resultindirizzi['city'])."',
												   `cap` = '".$resultindirizzi['postcode']."',
												   `nome` = '".str_replace("'","\'",$resultindirizzi['firstname'])."',
												   `cognome` = '".str_replace("'","\'",$resultindirizzi['lastname'])."',
												   `telefono` = '".$resultindirizzi['phone']."',
												   `cellulare` = '".$resultindirizzi['phone_mobile']."',	
                                                   `data_nascita` = '".$resultclientiweb['birthday']."',	
                                                   `email` = '".$resultclientiweb['email']."' 												 
												  where `idwebcustomer` = '". $resultclientiweb['id_customer'] ."'");  		
		} else {
		// ...altrimenti la modifico come privato	
		$dbo->query("Update `an_anagrafiche` set `tipo` = 'Privato',
		                                           `piva` = '".$resultindirizzi['vat_number']."',
                                                   `indirizzo` = '".str_replace("'","\'",$resultindirizzi['address1'])."',
												   `citta` = '".str_replace("'","\'",$resultindirizzi['city'])."',
												   `cap` = '".$resultindirizzi['postcode']."',
												   `nome` = '".str_replace("'","\'",$resultindirizzi['firstname'])."',
												   `cognome` = '".str_replace("'","\'",$resultindirizzi['lastname'])."',
												   `telefono` = '".$resultindirizzi['phone']."',
												   `cellulare` = '".$resultindirizzi['phone_mobile']."',	
                                                   `data_nascita` = '".$resultclientiweb['birthday']."',	
                                                   `email` = '".$resultclientiweb['email']."' 		
		                                          where `idwebcustomer` = '". $resultclientiweb['id_customer'] ."'");  	
		}
		

		//Modifico la sede
		$dbo->query("Update `an_sedi` set `nomesede` = '".str_replace("'","\'",$resultindirizzi['alias'])."',
       									    `indirizzo` = '".str_replace("'","\'",$resultindirizzi['address1'])."',
											`piva` = '".$resultindirizzi['vat_number']."',
											`citta` = '".str_replace("'","\'",$resultindirizzi['city'])."',
											`cap` = '".$resultindirizzi['postcode']."',
											`telefono` = '".$resultindirizzi['phone']."',
											`cellulare` = '".$resultindirizzi['phone_mobile']."',
											`email` = '".$resultindirizzi['email']."' 
		                                    where `idwebsede` = '".$resultindirizzi['id_address']."'");
		
		 
	}

	
}	
	
	
	
//sincronizzo gli ordini del web

//cerco gli ordini creati o modificati e non presenti dopo l'ultima sincro
$resultsordininonpresenti = $dbo->fetchArray("SELECT *, (`total_shipping_tax_incl` - `total_shipping_tax_excl`) as `valoreivasped` FROM `ps_orders` where `date_upd` >  (Select CONCAT (`data_ultima_sincroweb`, ' ', `ora_ultima_sincroweb`) as `orario` from `zz_sincro`) 
                                   and `reference` not in (SELECT `numero_cliente` FROM `or_ordini`)");
foreach ($resultsordininonpresenti as $resultordininonpresenti) {
        
        //cerca il metodo di pagamento		
		$resultspagamento = $dbo->fetchArray("SELECT * FROM `co_pagamenti` where `ModuloWeb` = '". $resultordininonpresenti['module'] ."'");
		foreach ($resultspagamento as $resultpagamento) {
			$come_pagamento = $resultpagamento['id']; 
		}
		
		//cerca la sede		
		$resultssedi = $dbo->fetchArray("SELECT * FROM `an_sedi` where `idwebsede` = '". $resultordininonpresenti['id_address_delivery'] ."'");
		foreach ($resultssedi as $resultsedi) {
			$quale_sede = $resultsedi['id']; 
		}
		
		//cerca il cliente		
		$resultscliente = $dbo->fetchArray("SELECT * FROM `an_anagrafiche` where `idwebcustomer` = '". $resultordininonpresenti['id_customer'] ."'");
		foreach ($resultscliente as $resultcliente) {
			$quale_cliente = $resultcliente['idanagrafica']; 
		}
		
		
//inserisco l'ordine
		$dbo->query("INSERT INTO `or_ordini` (`numero`,`data`, `idanagrafica`, `idsede`,`idtipoordine`,`idstatoordine`,`idpagamento`, `numero_cliente`) VALUES ('W".$resultordininonpresenti['id_order']."','".$resultordininonpresenti['date_add']."','".$quale_cliente."','".$quale_sede."','2','1','".$come_pagamento."','".str_replace("'","\'",$resultordininonpresenti['reference'])."')");	
		//prendo l'id dell'ultimo inserimento dalla tabella ps_manufacturer
		$id_ordine = $dbo->lastInsertedID();
		
		       //inserisco i dettagli dell'ordine
			   $resultsdettagliordine = $dbo->fetchArray("SELECT *, (`total_price_tax_incl` - `total_price_tax_excl`) AS `valoreiva` FROM `ps_order_detail` where `id_order` = '". $resultordininonpresenti['id_order'] ."'");
				foreach ($resultsdettagliordine as $resultdettagliordine) {
					
				if ($resultdettagliordine['reduction_amount'] > 0) 
				{$tiporiduzione = 'UNT';} 
			    else {$tiporiduzione = 'PRC';}
				
						//cerca l'articolo	
						$resultsarticolo = $dbo->fetchArray("SELECT * FROM `mg_articoli` where `idweb` = '". $resultdettagliordine['product_id'] ."'");
						foreach ($resultsarticolo as $resultarticolo) {
							$quale_articolo = $resultarticolo['id']; 
						}
					
					
					
                 $dbo->query("INSERT INTO `or_righe_ordini` (`idordine`, `idarticolo`, `idiva`, `desc_iva`, `iva`, `descrizione`, `prezzo_unitario_acquisto`, `subtotale`, `tipo_sconto`, `qta`, `id_articolo_variante`) 
				     VALUES ('".$id_ordine."','".$quale_articolo."','171','Aliq. Iva 22%','".$resultdettagliordine['valoreiva']."','".$resultdettagliordine['product_name']."','".$resultdettagliordine['origina_wholesale_price']."','".$resultdettagliordine['total_price_tax_excl']."','".$tiporiduzione."','".$resultdettagliordine['product_quantity']."','".$resultdettagliordine['product_attribute_id']."')");	
				}		

$dbo->query("INSERT INTO `or_righe_ordini` (`idordine`, `idarticolo`, `idiva`, `desc_iva`, `iva`, `descrizione`, `prezzo_unitario_acquisto`, `subtotale`, `tipo_sconto`, `qta`) 
VALUES ('".$id_ordine."','0','171','Aliq. Iva 22%','".$resultordininonpresenti['valoreivasped']."','Spese di spedizione','0','".$resultordininonpresenti['total_shipping_tax_excl']."','PRC','1')");	
		
	
//salvo il nuovo codice ordine nel zz_sincro
$dbo->query("Update `zz_sincro` set `ultimo_ordine` = '".$resultordininonpresenti['id_order']."'"); 	
	
}


	
		
//salvo la nuova data e ora della sincronizzazione
$dbo->query("Update `zz_sincro` set `data_ultima_sincroweb` = CURDATE(), `ora_ultima_sincroweb` = CURTIME()"); 

    flash()->info(tr('Sincronizzazione del web sul gestionale eseguita!'));
	
    break;
		
		
    case 'svuota':
	

# cancella gli ordini
# -----------------------------------------
$dbo->query('TRUNCATE `ps_order_carrier`');
$dbo->query('TRUNCATE `ps_order_cart_rule`');
$dbo->query('TRUNCATE `ps_order_detail`');
$dbo->query('TRUNCATE `ps_orders`');
$dbo->query('TRUNCATE `ps_order_detail_tax`');
$dbo->query('TRUNCATE `ps_order_history`');
$dbo->query('TRUNCATE `ps_order_invoice`');
$dbo->query('TRUNCATE `ps_order_invoice_payment`');
$dbo->query('TRUNCATE `ps_order_invoice_tax`');
$dbo->query('TRUNCATE `ps_order_message`');
$dbo->query('TRUNCATE `ps_order_message_lang`');
$dbo->query('TRUNCATE `ps_order_payment`');
$dbo->query('TRUNCATE `ps_order_return`');
$dbo->query('TRUNCATE `ps_order_return_detail`');
$dbo->query('TRUNCATE `ps_order_slip`');
$dbo->query('TRUNCATE `ps_order_slip_detail`');

#cancella le categorie
# -----------------------------------------


$dbo->query('DELETE FROM ps_category WHERE ps_category.id_category > 2');
$dbo->query('DELETE FROM ps_category_group WHERE ps_category_group.id_category > 2');
$dbo->query('DELETE FROM ps_category_lang WHERE ps_category_lang.id_category > 2');
$dbo->query('TRUNCATE ps_category_product');
$dbo->query('DELETE FROM ps_category_shop WHERE ps_category_shop.id_category > 2');


# cancella tutti i dati sulle tbl prodotti
# -----------------------------------------
$dbo->query('TRUNCATE `ps_product`');
$dbo->query('TRUNCATE `ps_product_attachment`');
$dbo->query('TRUNCATE `ps_product_attribute`');
$dbo->query('TRUNCATE `ps_product_attribute_combination`');
$dbo->query('TRUNCATE `ps_product_attribute_image`');
$dbo->query('TRUNCATE `ps_product_attribute_shop`');
$dbo->query('TRUNCATE `ps_product_carrier`');
$dbo->query('TRUNCATE `ps_product_country_tax`');
$dbo->query('TRUNCATE `ps_product_download`');
$dbo->query('TRUNCATE `ps_product_group_reduction_cache`');
$dbo->query('TRUNCATE `ps_product_lang`');
$dbo->query('TRUNCATE `ps_product_sale`');
$dbo->query('TRUNCATE `ps_product_shop`');
$dbo->query('TRUNCATE `ps_product_supplier`');
$dbo->query('TRUNCATE `ps_product_tag`');

$dbo->query('TRUNCATE `ps_cart_product`');
$dbo->query('TRUNCATE `ps_compare_product`');
$dbo->query('TRUNCATE `ps_layered_product_attribute`');
$dbo->query('TRUNCATE `ps_stock_available`');


# cancella tutti gli sconti
# -----------------------------------------
$dbo->query('TRUNCATE `ps_layered_price_index`');
$dbo->query('TRUNCATE `ps_specific_price`');



# cancella tutti i dati sulle tbl clienti
# -----------------------------------------
$dbo->query('TRUNCATE `ps_customer`');
$dbo->query('TRUNCATE `ps_customer_group`');
$dbo->query('TRUNCATE `ps_customer_message`');
$dbo->query('TRUNCATE `ps_customer_message_sync_imap`');
$dbo->query('TRUNCATE `ps_customer_thread`');

# cancella tutti i dati sulle tbl fornitori
# -----------------------------------------
$dbo->query('TRUNCATE `ps_supplier`');
$dbo->query('TRUNCATE `ps_supplier_lang`');
$dbo->query('TRUNCATE `ps_supplier_shop`');

# cancella tutti i dati sulla tbl indirizzi
# -----------------------------------------
$dbo->query('TRUNCATE `ps_address`');

# cancella tutti i dati sulla tbl newsletter
# -----------------------------------------
$dbo->query('TRUNCATE `ps_newsletter`');


# cancella tutte le immagini
# -----------------------------------------
$dbo->query('TRUNCATE `ps_image`');
$dbo->query('TRUNCATE `ps_image_lang`');
$dbo->query('TRUNCATE `ps_image_shop`');

# cancella tutti produttore
# -----------------------------------------

$dbo->query('TRUNCATE TABLE `ps_manufacturer`');
$dbo->query('TRUNCATE TABLE `ps_manufacturer_lang`');
$dbo->query('TRUNCATE TABLE `ps_manufacturer_shop`');	

//toglie tutti dli IDweb dalla tabella delle categorie
$dbo->query("Update `mg_categorie` set `idweb` = Null");
$dbo->query("Update `mg_articoli` set `idweb` = Null");
$dbo->query("Update `an_anagrafiche` set `idweb` = Null");
$dbo->query("Update `zz_files` set `idweb` = Null");
$dbo->query("Update `mg_articoli_varianti` set `id_web` = 0");
$dbo->query("Update `mg_varianti` set `id_web` = 0");



flash()->info(tr('Svuota web eseguito correttamente.'));


	
        break;		


}