<?php

include_once __DIR__.'/../../core.php';

echo '<p>'.tr('Con la <b>Gestione Sincro</b> è possibile controllare i parametri della sincronizzazione.').'.</p>';
?>
<form action="" method="post" id="edit-form">
	<input type="hidden" name="op" value="salva">
	<input type="hidden" name="backto" value="record-edit">
    <input type="hidden" name="id_record" value="1">



<div class="row">
    <div class="col-xs-12 col-md-12">
        <h3><?php echo tr('Parametri'); ?></h3>
        <div  style="height: 100%;">
                     <div class="row">
                        <div class="col-md-3">
                            {[ "type": "date", "label": "<?php echo tr('Data Ultima Sincro Gestionale->Web'); ?>", "name": "data_ultima_sincro", "value": "$data_ultima_sincro$", "required": 1 ]}							
                        </div>
						
						 <div class="col-md-3">
                            {[ "type": "time", "label": "<?php echo tr('Ora Ultima Sincro  Gestionale->Web'); ?>", "name": "ora_ultima_sincro", "value": "$ora_ultima_sincro$", "required": 1 ]}							
                        </div>

                        <div class="col-md-3">
						     {[ "type": "text", "label": "<?php echo tr('Cartella Software'); ?>", "name": "cartella_software", "value": "$cartella_software$", "required": 1 ]}
                             
                        </div>
						 
						 <div class="col-md-3">
                            
                        </div>
                    </div>		
					
                     <div class="row">
                        <div class="col-md-3">
                            {[ "type": "date", "label": "<?php echo tr('Data Ultima Sincro  Web->Gestionale'); ?>", "name": "data_ultima_sincroweb", "value": "$data_ultima_sincroweb$", "required": 1 ]}							
                        </div>
						
						 <div class="col-md-3">
                            {[ "type": "time", "label": "<?php echo tr('Ora Ultima Sincro Web->Gestionale'); ?>", "name": "ora_ultima_sincroweb", "value": "$ora_ultima_sincroweb$", "required": 1 ]}							
                        </div>

                        <div class="col-md-3">
						    {[ "type": "text", "label": "<?php echo tr('Cartella Web'); ?>", "name": "cartella_web", "value": "$cartella_web$", "required": 1 ]}
                            
                        </div>
						 
						 <div class="col-md-3">
                            {[ "type": "text", "label": "<?php echo tr('Ultimo Ordine'); ?>", "name": "ultimo_ordine", "value": "$ultimo_ordine$", "required": 1 ]}
                        </div>
                    </div>						
					
                     <div class="row">


                        <div class="col-md-6">
                            {[ "type": "text", "label": "<?php echo tr('Search Cron'); ?>", "name": "search_cron", "value": "$search_cron$", "required": 1 ]}
                        </div>
						 <div class="col-md-6">
                            
							{[ "type": "text", "label": "<?php echo tr('Rigenera Immagini'); ?>", "name": "rigenera_immagini", "value": "$rigenera_immagini$", "required": 1 ]}
                        </div>
                    </div>						

            
         </div>
	<button type="submit" class="btn btn-primary pull-right" ><i class="fa fa-database"></i> <?php echo tr('Salva Configurazione'); ?></button>	   	 
	</div>
 
</div>


</form>

