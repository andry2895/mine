<?php

include_once __DIR__.'/core.php';

$pageTitle = tr('Informazioni');

$paths = App::getPaths();

include_once App::filepath('include|custom|', 'top.php');

echo '
<div class="box">
    <div class="box-header">
        <img src="'.$paths['img'].'/logo.png" class="logo-image" alt="'.tr('OSM Logo').'">
        <h3 class="box-title">'.$nomesoftware.'</h3>
        <div class="pull-right">
            <i class="fa fa-info"></i> '.tr('Informazioni').'
        </div>
    </div>

    <div class="box-body">';

if (file_exists($docroot.'/assistenza.php')) {
    include $docroot.'/assistenza.php';
} else {
    echo '
        <div class="row">
            <div class="col-md-8">
                <p>Descrizione generale del Software.</p>
            </div>

            <div class="col-md-4">
                <p><b>'.tr('Sito web').':</b> </p>

                <p><b>'.tr('Versione').':</b> '.$version.' <small class="text-muted">('.(!empty($revision) ? 'R'.$revision : tr('In sviluppo')).')</small></p>

                <p><b>'.tr('Licenza').':</b> </p>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-primary">
                    <div class="box-header">
                        <h3 class="box-title text-uppercase"><i class="fa fa-globe"></i> Descrizione Funzionalità 1</h3>
                    </div>

                    <div class="box-body">

                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="box box-danger">
                    <div class="box-header">
                        <h3 class="box-title text-uppercase"><i class="fa fa-group"></i> Descrizione Funzionalità 2</h3>
                    </div>

                    <div class="box-body">
                        

                    </div>
                </div>

                <div class="box box-default">
                    <div class="box-header">
                        <h3 class="box-title text-uppercase"><i class="fa fa-download"></i> Descrizione Funzionalità 3</h3>
                    </div>


                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6">
                <div class="box box-warning">
                    <div class="box-header">
                        <h3 class="box-title text-uppercase"><i class="fa fa-money"></i> Descrizione Funzionalità 4</h3>
                    </div>

                </div>
            </div>


            <div class="col-md-6">
                <div class="box box-success">
                    <div class="box-header">
                        <h3 class="box-title text-uppercase"><i class="fa fa-euro"></i> Descrizione Funzionalità 5</h3>
                    </div>


                </div>
            </div>
        </div>';
}

echo '

	</div>
</div>';

include_once App::filepath('include|custom|', 'bottom.php');
