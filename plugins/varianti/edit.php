<?php

include_once __DIR__.'/../../core.php';


echo '
<form action="" method="post" role="form" id="form_sedi" enctype="multipart/form-data">
    <input type="hidden" name="id_plugin" value="'.$id_plugin.'">
    <input type="hidden" name="id_parent" value="'.$id_parent.'">
    <input type="hidden" name="id_record" value="'.$id_record.'">
	<input type="hidden" name="backto" value="record-edit">
	<input type="hidden" name="op" value="updatevariante">



	<div class="row">
		<div class="col-md-4">
			<img src="files/articoli/'.$record['immagine'].'" class="img-thumbnail">
			<br>
<select name="immagine"  id="immagine">';			
$subimg = $dbo->fetchArray("SELECT * from zz_files WHERE `id_module`= 21 and category = 'Immagine' and id_record = ".$id_parent);
echo "<option value='Null'>Scegli Immagine Variante</option>";	
	 foreach ($subimg as $simg) {	
	 if ($simg['filename'] == $record['immagine'])
	 $selezionato = "selected";
     else $selezionato = '';
  
 echo "<option value=".$simg['filename']." ".$selezionato.">".$simg['name']."</option>";
 }
 echo "</select></td>";			
			
		echo '	
		</div>

		<div class="col-md-8">
            			
 		<div class="col-md-6">
			{[ "type": "number", "label": "'.tr('Qta').' (calcolata dai movimenti)", "name": "qta", "required": 0, "value": "$qta$", "min-value": "undefined", "readonly":1]}
		</div>
    
		<div class="col-md-6">
		{[ "type": "number", "label": "'.tr('Qta Minima').'", "name": "qta_minima", "required": 1, "value": "$qta_minima$", "min-value": "undefined" ]}
			
		</div>

		<div class="col-md-12">
			{[ "type": "number", "label": "'.tr('Impatto Prezzo').'", "name": "impatto_prezzo", "required": 1, "value": "$impatto_prezzo$", "decimals": "impatto_prezzo", "min-value": "undefined", "icon-after": "€" ]}
		</div>

		<div class="col-md-12">
			{[ "type": "text", "label": "'.tr('Ean13').'", "name": "ean13", "value": "$ean13$" ]}
		</div>		
		
						
			
        </div>
	</div>

	<div class="row">
		
<br>
	</div>';



echo '
	<!-- PULSANTI -->
	<div class="row">
		<div class="col-md-12">
            <a class="btn btn-danger ask '.$disabled.'" data-backto="record-edit" data-op="deletevarianti" data-id="'.$record['id'].'" data-id_plugin="'.$id_plugin.'" data-id_module="'.$id_module.'" data-id_parent="'.$id_parent.'" '.$disabled.'>
                <i class="fa fa-trash"></i> '.tr('Elimina').'
            </a>

			<button type="submit" class="btn btn-primary pull-right"><i class="fa fa-edit"></i> '.tr('Modifica').'</button>
		</div>
	</div>
</form>';

?>
<br>
<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo tr('Attributi Variante'); ?></h3>
	</div>
	<div class="panel-body">
		<div class="clearfix"></div>
		<br>
			<table class="table table-striped table-hover table-condensed">
				<tr>
					<th width="40%"><?php echo tr('Gruppo Attributi'); ?></th>		
                     <th width="40%"><?php echo tr('Attributo'); ?></th>						
					<th width="20%"><?php echo tr('Modifica'); ?></th>
				</tr>
				</table>
				<?php include dirname(__DIR__).'/varianti/row-list.php'; ?>


	</div>
</div>

