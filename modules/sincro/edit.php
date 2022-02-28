<?php

include_once __DIR__.'/../../core.php';

$backup_dir = Backup::getDirectory();
$backups = Backup::getList();

//Mi Prendo gli orari delle ultime sincro
$resultssincro = $dbo->fetchArray("Select CONCAT (`data_ultima_sincro`, ' ', `ora_ultima_sincro`) as `ultimasincrogest`, CONCAT (`data_ultima_sincroweb`, ' ', `ora_ultima_sincroweb`) as `ultimasincroweb` from `zz_sincro`");	
foreach ($resultssincro as $resultsincro) {
	$ultimasincrogest = $resultsincro['ultimasincrogest']; 
	$ultimasincroweb = $resultsincro['ultimasincroweb']; ; 
}




//echo '<p>'.tr('Il backup è <b>molto importante</b> perché permette di creare una copia della propria installazione e relativi dati per poterla poi ripristinare in seguito a errori, cancellazioni accidentali o guasti hardware').'.</p>';
echo '<p>'.tr('Con la <b>Sincro</b> è possibile sincronizzare i dati del Ecommrce con quelli del gestionale.').'.</p>';




// Operazioni JavaScript
echo '
<script>

// invio websinc
function websinc(){
    swal({
        title: "'.tr('Sincronizzazione').'",
        text: "'.tr('Eseguire la sincronizzazione dal web a gestionale?').'",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-lg btn-success",
        confirmButtonText: "'.tr('Sincronizza').'",
    }).then(
    function(){
        location.href = globals.rootdir + "/editor.php?id_module='.$id_module.'&op=websinc";
    }, function(){});
}


// invio gestsinc
function gestsinc(){
    swal({
        title: "'.tr('Sincronizzazione').'",
        text: "'.tr('Eseguire la sincronizzazione dal gestiionale al web?').'",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-lg btn-success",
        confirmButtonText: "'.tr('Sincronizza').'",
    }).then(
    function(){
        location.href = globals.rootdir + "/editor.php?id_module='.$id_module.'&op=gestsinc";
    }, function(){});
}


// invio svuota web
function svuota(){
    swal({
        title: "'.tr('Svuota Web').'",
        text: "'.tr('Eseguire la completa cancellazione dei dati presenti sul web?').'",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-lg btn-success",
        confirmButtonText: "'.tr('Svuota').'",
    }).then(
    function(){
        location.href = globals.rootdir + "/editor.php?id_module='.$id_module.'&op=svuota";
    }, function(){});
}


// Creazione backup
function backup(){
    swal({
        title: "'.tr('Nuovo backup').'",
        text: "'.tr('Sei sicuro di voler creare un nuovo backup?').'",
        type: "warning",
        showCancelButton: true,
        confirmButtonClass: "btn btn-lg btn-success",
        confirmButtonText: "'.tr('Crea').'",
    }).then(
    function(){
        location.href = globals.rootdir + "/editor.php?id_module='.$id_module.'&op=backup";
    }, function(){});
}


</script>';


        echo '
<div class="row">
    <div class="col-xs-12 col-md-6">
        <h3>'.tr('Sincro verso il web').'</h3>';

                echo '
        <div class="callout callout-info" style="height: 200%;">
            <h4>Verranno sincronizzate le ultime modifiche effettuate sul gestionale</h4>
            <p>Ultima Sincronizzazione Gestionale->Web: '.$ultimasincrogest.'</p>		
            <a class="btn btn-primary" href="" onclick="gestsinc()" target="_blank" style="display:none"><i class="fa fa-download"></i> '.tr('Sincronizza').'</a>
			<button type="button" class="btn btn-primary pull-right" onclick="gestsinc()"><i class="fa fa-database"></i> '.tr('Sincronizza').'</button>


        </div>
	</div>
	
    <div class="col-xs-12 col-md-6">
        <h3>'.tr('Sincro dal web').'</h3>';




                echo '
        <div class="callout callout-info" style="height: 200%;">
            <h4>Verranno sincronizzate gli ultimi ordini eseguiti sul web</h4>
            <p>Ultima Sincronizzazione web->Gestionale: '.$ultimasincroweb.'</p>
            <a class="btn btn-primary" href="" onclick="websinc()" target="_blank" style="display:none"><i class="fa fa-upload"></i> '.tr('Sincronizza').'</a>
			<button type="button" class="btn btn-primary pull-right" onclick="websinc()"><i class="fa fa-database"></i> '.tr('Sincronizza').'</button>


        </div>
	</div>	
            
        


    
</div>';
    

// Creazione backup
if (!empty($backup_dir)) {
    echo '
<br><br><br>	
<button type="button" class="btn btn-primary pull-right" onclick="svuota()"><i class="fa fa-database"></i> '.tr('Svuota Web').'...</button>

<div class="clearfix"></div>';
}
