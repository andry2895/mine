<?php

include_once __DIR__.'/../../../core.php';

$record['abilita_varianti'] = ($record['variante'] > 0) ? 1 : $record['abilita_varianti'];
if (empty($record['abilita_varianti'])) {
    echo '
<script>$("#link-tab_'.$plugin['id'].'").addClass("disabled");</script>';
}

// Visualizzo, in base alle impostazioni scelte, se il magazzino verrà movimentato
$message = setting("Movimenta il magazzino durante l'inserimento o eliminazione delle varianti") ? tr("L'inserimento e la rimozione delle varianti modificherà la quantità dell'articolo!") : tr("L'inserimento e la rimozione delle varianti non movimenterà la quantità dell'articolo!");
echo '
<div class="alert alert-info">
    '.$message.'
</div>';

// Inserimento seriali
echo '
<div class="nav-tabs-custom">
    <ul class="nav nav-tabs nav-justified">'.
        //<li class="active"><a href="#generazione" data-toggle="tab">'.tr('Generazione').'</a></li>' . 
        '<li class="active"><a href="#inserimento" data-toggle="tab">'.tr('Inserimento').'</a></li>
    </ul>

    <div class="tab-content">'.
       // <form action="" method="post" role="form" class="tab-pane active" id="generazione">
       //     <input type="hidden" name="backto" value="record-edit">
       //     <input type="hidden" name="op" value="generate_serials">

       //     <div class="row">
       //         <div class="col-md-5">
       //             {[ "type": "text", "label": "'.tr('Inizio').'", "name": "serial_start", "extra": "onkeyup=\"$(\'#serial_end\').val( $(this).val()); ricalcola_generazione();\"" ]}
       //         </div>
      
       //         <div class="col-md-2 text-center" style="padding-top: 20px;">
       //             <i class="fa fa-arrow-circle-right fa-2x"></i>
       //         </div>
       //  
       //         <div class="col-md-5">
       //             {[ "type": "text", "label": "'.tr('Fine').'", "name": "serial_end", "extra": "onkeyup=\"ricalcola_generazione();\"" ]}
       //         </div>
       //     </div>

      //      <div class="row">
      //          <div class="col-md-9">
      //              <p class="text-danger">'.tr('Totale prodotti da inserire').': <span id="totale_generazione">0</span></p>
      //          </div>
      //
      //          <div class="col-md-3 text-right">
      //              <button type="button" class="btn btn-primary" onclick="addSerial(\'#generazione\', $(\'#totale_generazione\').text())"><i class="fa fa-plus"></i> '.tr('Aggiungi').'</button>
      //          </div>
      //      </div>
      //  </form>

        '<form action="" method="post" role="form" class="tab-pane active" id="inserimento">
            <input type="hidden" name="backto" value="record-edit">
            <input type="hidden" name="op" value="add_varianti">

            <div class="row">
                <div class="col-md-12">
                    {[ "type": "text", "label": "'.tr('Nuova Variante').'", "name": "varianti[]", "multiple": 1, "values": [] ]}
                </div>
            </div>

            <div class="row">
                <div class="col-md-9" style="display:none">
                    <p class="text-danger">'.tr('Totale Varianti da inserire').': <span id="totale_inserimento">0</span></p>
                </div>

                <div class="col-md-12 text-right">
                    <button type="button" class="btn btn-primary" onclick="addSerial(1)"><i class="fa fa-plus"></i> '.tr('Aggiungi').'</button>
                </div>
            </div>
        </form>
    </div>
</div>';

// Elenco
echo '
<div class="box">
    <div class="box-header with-border">
        <h3 class="box-title">'.tr('Elenco Varianti').'</h3>
    </div>
    <div class="box-body">';

// Conteggio totale prodotti
$rs = $dbo->fetchArray('SELECT COUNT(id) AS tot FROM mg_articoli_varianti WHERE id_articolo='.prepare($id_record));
$tot_prodotti = $rs[0]['tot'];

// Visualizzazione di tutti i prodotti
$search_serial = get('search_serial');
$query = 'SELECT id, descrizione, ean13, qta, attivo, impatto_prezzo, qta_minima FROM mg_articoli_varianti WHERE descrizione IS NOT NULL AND id_articolo='.prepare($id_record). ' GROUP BY descrizione ORDER BY created_at DESC, descrizione DESC';
$rs2 = $dbo->fetchArray($query);

echo '
    <table class="table table-striped table-hover table-condensed table-bordered text-center datatables">
            <tr>
                <th id="th_descrizione">'.tr('Descrizione').'</th>
				<th id="th_ean13">'.tr('Cod.Barre').'</th>                
                <th id="th_quantita">'.tr('Qta').'</th>
				<th id="th_attivo">'.tr('Attivo').'</th>
                <th id="th_impatto">'.tr('Impatto Prezzo').'</th>
				<th id="th_qta_minima">'.tr('Qta minima').'</th>
                <th class="text-center">#</th>
            </tr>';

for ($i = 0; $i < count($rs2); ++$i) {
    echo '
        <tr>

            <td>'.$rs2[$i]['descrizione'].'</td>
			<td>'.$rs2[$i]['ean13'].'</td>
			<td>'.$rs2[$i]['qta'].'</td>
			<td>'.$rs2[$i]['attivo'].'</td>
			<td>'.$rs2[$i]['impatto_prezzo'].'</td>
			<td>'.$rs2[$i]['qta_minima'].'</td>
			<td class="text-center">
                <a class="btn btn-danger btn-sm ask" data-backto="record-edit" data-op="delprodotto" data-idprodotto="'.$rs2[$i]['id'].'">
                    <i class="fa fa-trash"></i>
                </a>
            </td>';
           
    echo '
            </tr>';
}
echo '                  
        </table>
    </div>
</div>';

echo '
<script type="text/javascript">
$(document).ready(function() {
    $("#serials").removeClass("superselect");
    $("#serials").select2().select2("destroy");

    $("#serials").select2({
        theme: "bootstrap",
        language: "it",
        allowClear: true,
        tags: true,
        tokenSeparators: [\',\']
    });
});

function addSerial(form_id) {
    if (form_id == 1){
        swal({
            title: "'.tr('Nuove Varianti').'",
            html: "'.tr("Confermi l'inserimento della nuova variante?").'",
            type: "success",
            showCancelButton: true,
            confirmButtonText: "'.tr('Continua').'"
        }).then(function (result) {
            $(form_id).submit();
        })
    } else {
        swal("'.tr('Errore').'", "'.tr('Nessun seriale inserito').'", "error");
    }
}


/*
	Questa funzione restituisce la parte numerica di una stringa
*/
function get_last_numeric_part(str){
	var matches = str.match(/(.*?)([\d]*$)/);
	return matches[2];
}
</script>';
