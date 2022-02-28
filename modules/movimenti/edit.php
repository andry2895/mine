<?php

include_once __DIR__.'/../../core.php';



echo '
<form action="" method="post">
    <input type="hidden" name="id_plugin" value="'.$id_plugin.'">
    <input type="hidden" name="id_parent" value="'.$id_parent.'">
    <input type="hidden" name="id_record" value="'.$id_record.'">	
	<input type="hidden" name="backto" value="record-edit">
	<input type="hidden" name="op" value="updmovimento">';
?>	
    <div class="row">
          
        <div class="col-md-12">
            {["type":"select", "label":"<?php echo tr('Variante'); ?>", "name":"id_articolo_variante", "values":"query=SELECT id, descrizione FROM mg_articoli_varianti where id_articolo = <?php echo $_GET['id_articolo'] ?>", "value": "<?php echo $_GET['id_articolo_variante'] ?>" ]}
        </div>

    </div>

</form>
