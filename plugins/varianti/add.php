<?php

include_once __DIR__.'/../../core.php';

echo '
<form action="" method="post" role="form" enctype="multipart/form-data">
    <input type="hidden" name="id_parent" value="'.$id_parent.'">
	<input type="hidden" name="backto" value="record-edit">
	<input type="hidden" name="op" value="addvariante">

<div class="row">
		<div class="col-md-4">
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
            {[ "type": "number", "label": "'.tr('Qta').' (calcolata dai movimenti)", "name": "qta", "required": 0, "value": "$qta$", "decimals": "qta", "min-value": "undefined", "readonly":1 ]}			
        </div>
	</div>

		<div class="row">
		<div class="col-md-4">
			{[ "type": "number", "label": "'.tr('Impatto Prezzo').'", "name": "impatto_prezzo", "required": 1, "value": "$impatto_prezzo$", "decimals": "impatto_prezzo", "min-value": "undefined", "icon-after": "€" ]}
		</div>

		<div class="col-md-4">
			{[ "type": "number", "label": "'.tr('Qta Minima').'", "name": "qta_minima", "required": 1, "value": "$qta_minima$", "decimals": "qta_minima", "min-value": "undefined" ]}
		</div>

		<div class="col-md-4">
			{[ "type": "text", "label": "'.tr('Ean13').'", "name": "ean13", "value": "$ean13$" ]}
		</div>

	</div>

	<!-- PULSANTI -->
	<div class="row">
		<div class="col-md-12 text-right">
			<button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> '.tr('Aggiungi').'</button>
		</div>
	</div>
</form>';
