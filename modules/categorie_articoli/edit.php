	<?php

include_once __DIR__.'/../../core.php';

?><form action="" method="post" id="edit-form" enctype="multipart/form-data">
	<input type="hidden" name="backto" value="record-edit">
	<input type="hidden" name="op" value="update">

	<!-- DATI -->
	<div class="panel panel-primary">
		<div class="panel-heading">
			<h3 class="panel-title"><?php echo tr('Dati'); ?></h3>
		</div>

		<div class="panel-body">
			<div class="row">
			
				<div class="col-md-3">
					{[ "type": "image", "label": "<?php echo tr('Immagine'); ?>", "name": "immagine", "class": "img-thumbnail", "value": "$immagine$" ]}
				</div>
			
				<div class="col-md-6">
					{[ "type": "text", "label": "<?php echo tr('Nome'); ?>", "name": "nome", "required": 1, "value": "$nome$" ]}
				</div>

				<div class="col-md-3">
					{[ "type": "checkbox", "label": "<?php echo tr('web'); ?>", "name": "web", "value": "$web$", "help": "<?php echo tr('Abilita la sincronizzazione della categoria con E-commerce'); ?>", "placeholder": "<?php echo tr('Sincronizza Web'); ?>" ]}
				</div>
				
				<div class="col-md-9">
					{[ "type": "text", "label": "<?php echo tr('Meta Title'); ?>", "name": "meta_title", "required": 0, "value": "$meta_title$" ]}
				</div>
				<div class="col-md-9">
					{[ "type": "text", "label": "<?php echo tr('Meta Keywords'); ?>", "name": "meta_keywords", "required": 0, "value": "$meta_keywords$" ]}
				</div>
				<div class="col-md-9">
					{[ "type": "text", "label": "<?php echo tr('Meta Description'); ?>", "name": "meta_description", "required": 0, "value": "$meta_description$" ]}
				</div>
				
				
				
				<div class="col-md-4" style="display: none">
					{[ "type": "text", "label": "<?php echo tr('Colore'); ?>", "name": "colore", "class": "colorpicker text-center", "value": "$colore$", "extra": "maxlength='7'", "icon-after": "<div class='img-circle square'></div>" ]}
				</div>
			</div>

			<div class="row">
				<div class="col-md-12">
					{[ "type": "textarea", "label": "<?php echo tr('Nota'); ?>", "name": "nota", "value": "$nota$" ]}
				</div>
			</div>
		</div>
	</div>

</form>



<div class="panel panel-primary">
	<div class="panel-heading">
		<h3 class="panel-title"><?php echo tr('Sottocategorie'); ?></h3>
	</div>

	<div class="panel-body">
		<div class="pull-left">
			<a class="btn btn-primary" data-href="<?php echo $rootdir; ?>/add.php?id_module=<?php echo $id_module; ?>&id_original=<?php echo $id_record; ?>" data-toggle="modal" data-title="<?php echo tr('Aggiungi riga'); ?>"><i class="fa fa-plus"></i> <?php echo tr('Sottocategoria'); ?></a><br>
		</div>
		<div class="clearfix"></div>
		<hr>

		<div class="row">
			<div class="col-md-12">
				<table class="table table-striped table-hover table-condensed">
				<tr>
					<th><?php echo tr('Nome'); ?></th>
					<th style="display:none"><?php echo tr('Colore'); ?></th>
					<th><?php echo tr('Nota'); ?></th>
					<th><?php echo tr('Immagine'); ?></th>
					<th width="20%"><?php echo tr('Opzioni'); ?></th>
				</tr>

				<?php include $docroot.'/modules/'.Modules::get($id_module)['directory'].'/row-list.php'; ?>
				</table>
			</div>
		</div>
	</div>
</div>

<script>
	$(document).ready( function(){
		$('.colorpicker').colorpicker().on('changeColor', function(){
			$('#colore').parent().find('.square').css( 'background', $('#colore').val() );
		});

		$('#colore').parent().find('.square').css( 'background', $('#colore').val() );
	});
</script>

<?php

$res = $dbo->fetchNum('SELECT * FROM `mg_articoli` WHERE `id_categoria`='.prepare($id_record).' OR `id_sottocategoria`='.prepare($id_record).'  OR `id_sottocategoria` IN (SELECT id FROM `mg_categorie` WHERE `parent`='.prepare($id_record).')');

if ($res) {
    echo '
    <div class="alert alert-danger">
        <p>'.tr('Ci sono '.count($res).' articoli collegati a questa categoria. Non ?? possibile eliminarla.').'</p>
    </div>';
} else {
    echo '
    <a class="btn btn-danger ask" data-backto="record-list">
        <i class="fa fa-trash"></i> '.tr('Elimina').'
    </a>';
}
