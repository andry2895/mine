<?php

include_once __DIR__.'/../../core.php';

?>
<form action="" method="post" id="edit-form">
	<input type="hidden" name="op" value="update">
	<input type="hidden" name="backto" value="record-edit">
    <input type="hidden" name="id_record" value="<?php echo $id_record; ?>">

	<!-- DATI -->
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo tr('Gruppo Attributi'); ?></h3>
		</div>

		<div class="panel-body">
			<div class="row">
				<div class="col-md-12">
					{[ "type": "text", "label": "<?php echo tr('Valore'); ?>", "name": "valore", "value": "$valore$", "required": 1 ]}
                </div>
                
            </div>

		</div>
	</div>
	</form>
	
<form action="" method="post" id="edit-form">
<input type="hidden" name="op" value="insatt">
<input type="hidden" name="id_record" value="<?php echo $id_record; ?>">
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo tr('Attributo'); ?></h3>
		</div>

		<div class="panel-body">
			<div class="data">
<?php


$results = $dbo->fetchArray('SELECT * FROM `mg_attributi` WHERE id_gruppo_attributi='.prepare($id_record).' ORDER BY `id` ASC');
$cont = 1;
foreach ($results as $result) {
    echo '
				<div class="box box-success">
					<div class="box-header with-border">
						<h3 class="box-title">'.tr('Attributo _NUMBER_', [
                            '_NUMBER_' => $cont,
                        ]).'</h3>
						<a class="btn btn-danger pull-right" onclick="';
    echo "if(confirm('".tr('Eliminare questo elemento?')."')){ location.href='".$rootdir.'/editor.php?id_module='.$id_module.'&id_record='.$id_record.'&op=delete_rata&id='.$result['id']."'; }";
    echo '"><i class="fa fa-trash"></i> '.tr('Elimina').'</a>
					</div>
					<div class="box-body">
						<input type="hidden" value="'.$result['id'].'" name="idatt[]">

						<div class="row">
							<div class="col-md-6">
								{[ "type": "label", "label": "'.tr('valore').'", "name": "valore[]", "value": "'.$result['valore'].'" ]}
								
							</div>
							<div class="col-md-6">
								{[ "type": "label", "label": "'.tr('colore').'", "name": "colore[]", "value": "'.$result['colore'].'" ]}
								
							</div>
							
                        </div>


					</div>
				</div>';
    ++$cont;
}
?>
			</div>
			<div class="pull-right">
				<button type="button" class="btn btn-info" id="add"><i class="fa fa-plus"></i> <?php echo tr('Aggiungi'); ?></button>
				<!--   button type="submit" class="btn btn-success"><i class="fa fa-check"></i></button --!>
			</div>
		</div>
	</div>

</form>


<a class="btn btn-danger ask" data-backto="record-list">
    <i class="fa fa-trash"></i> <?php echo tr('Elimina'); ?>
</a>
<?php
echo '
<form class="hide" id="template">
    <div class="box">
        <div class="box-header with-border">
            <h3 class="box-title">'.tr('Nuovo Attributo').'</h3>
        </div>
        <div class="box-body">
            <input type="hidden" value="" name="id[]">

            <div class="row">
                <div class="col-md-6">
                    {[ "type": "text", "label": "'.tr('valore').'", "name": "valore", "required": 1 ]}
                </div>

                <div class="col-md-6">
                    {[ "type": "text", "label": "'.tr('colore').'", "name": "colore", "required": 1 ]}
                </div>
            </div>

        </div>
    </div>
</form>';

?>

<script>
$(document).ready(function(){
	$(document).on('click', '#add', function(){
        cleanup_inputs();

	    $(this).parent().parent().find('.data').append($('#template').html());

        restart_inputs();
	});


});
</script>
