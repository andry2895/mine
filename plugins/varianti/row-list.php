<?php

include_once __DIR__.'/../../core.php';


$subcategorie = $dbo->fetchArray("SELECT mg_varianti.ID, mg_varianti.id_gruppo_attributi, mg_varianti.id_attributo, mg_attributi.valore as attributo, mg_gruppo_attributi.valore as gruppo FROM `mg_varianti` left join mg_attributi on mg_varianti.id_attributo = mg_attributi.id inner join mg_gruppo_attributi on mg_varianti.id_gruppo_attributi = mg_gruppo_attributi.id WHERE `id_articolo_variante`= '".$id_record."'");


	
foreach ($subcategorie as $sub) {
echo '	
<form action="" method="post" role="form" id="form_attributi'.$sub['ID'].'" enctype="multipart/form-data">
    <input type="hidden" name="id" value="'.$sub['ID'].'">	
    <input type="hidden" name="id_plugin" value="'.$id_plugin.'">
    <input type="hidden" name="id_parent" value="'.$id_parent.'">
    <input type="hidden" name="id_record" value="'.$id_record.'">	
	<input type="hidden" name="backto" value="record-edit">
	<input type="hidden" name="op" value="updateattributo">
	<table class="table table-striped table-hover table-condensed">
	<tr>
		<td width="40%">'.$sub['gruppo'].'</td>
		<td width="40%">			

<select name="attributo"  id="attributo">';

$subatt = $dbo->fetchArray("SELECT * from mg_attributi WHERE `id_gruppo_attributi`= ".$sub['id_gruppo_attributi']);
echo "<option value='Null'></option>";	
	 foreach ($subatt as $suba) {	
	 if ($suba['id'] == $sub['id_attributo'])
	 $selezionato = "selected";
     else $selezionato = '';
  
 echo "<option value=".$suba['id']." ".$selezionato.">".$suba['valore']."</option>";
 }
 echo "</select></td>";
echo '		
		<td width="20%"><button type="submit" class="btn btn-primary pull-right"><i class="fa fa-edit"></i> '.tr('Modifica').'</button></td>
	</tr>
	</table>
</form>';
}

?>
