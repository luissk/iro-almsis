<?php
/* echo "<pre>";
print_r($result);
echo "</pre>"; */
?>
<?php
if( $result ){
?>
<button class="btn btn-primary mb-3" id="reportExcel"><i class="fas fa-file-excel"></i> REPORTE EN EXCEL</button>
<div id="msjReport"></div>
<?php
}
?>

<div class="row">
    <div class="col-sm-12">
        <table class="table table-bordered table-condensed dt-responsive" width="100%" id="tblArea">
            <thead>
            <tr>
                <th>NRO</th>
                <th>FECHA</th>
                <th>DOCUMENTO</th>
                <th>PRODUCTO(s)</th>
                <th>COMENTARIO</th>
                <th>VER</th>
            </tr>
            </thead>
            <tbody>
            <?php
            if( $result ){
                $modeloSal = model('SalidaModel');

                $cont = 0;
                foreach( $result as $r ){
                    $cont++;
                    $id         = $r['id'];
                    $fecha      = $r['fecha'];
                    $documento  = $r['documento'];
                    $comentario = $r['comentario'];

                    $deta = $modeloSal->getDetalle($id);
                    
                    echo "<tr>";
                    echo "<td>$cont</td>";
                    echo "<td>$fecha</td>";
                    echo "<td>$documento</td>";
                    echo "<td>";
                    foreach($deta as $de){
                        $nomp     = $de['nombre'];
                        $nomp     = strlen($nomp) > 50 ? substr($nomp, 0, 60)."..." : $nomp;
                        $idcat_bd = $de['idcategoria'];
                        $nota     = $de['nota'] ? "| ".$de['nota'] : '';

                        if( $idcat != '' && $idcat_bd != $idcat ) continue;
                        echo "- $nomp $nota<br>";
                    }
                    echo "</td>";
                    echo "<td>$comentario</td>";
                    echo "<td>";
                    echo "
                        <a title='Ver' class='btn btn-success btn-xs movArea' role='button' data-id=$id href=''>
                            <i class='fa fa-search'></i>
                        </a> ";
                    echo "</td>";
                    echo "</tr>";
                }
            }
            ?>
            </tbody>
        </table>
    </div>
</div>


<div class="modal fade" id="modalDetalleArea" tabindex="-1" aria-labelledby="titlemodalDetalleArea" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titlemodalDetalleArea">Detalle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle_mov_area">
                
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    var $tablea = $('#tblArea').dataTable({

    });

    $("#tblArea").on('click', '.movArea', function(e){
        e.preventDefault();
        let id = $(this).data('id');

        $("#detalle_mov_area").html("<h3>CARGANDO...</h3>");
        $.post('producto/detalleMov', {
            id,mov:'salida',idproducto: ''
        }, function(data){
            $("#detalle_mov_area").html(data);
        });
        $("#modalDetalleArea").modal();
    });

    $("#reportExcel").on('click', function(e){
        e.preventDefault();

        let btn = document.querySelector('#reportExcel'),
            txtbtn = btn.innerHTML,
            btnHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        btn.setAttribute('disabled', 'disabled');
        btn.innerHTML = `${btnHTML} GENERANDO..`;

        let idarea = $("#k_area").val(),
            f_ini = $("#f_ini").val(),
            f_fin = $("#f_fin").val(),
            idcat = $("#k_categoria").val();

        $.post('producto/reportProductosPorArea', {
            idarea,f_ini,f_fin,idcat
        }, function(data){
            $("#msjReport").html(data);
            swal_alert('', `Se generó...`, 'success', 'Aceptar');
            btn.removeAttribute('disabled');
            btn.innerHTML = txtbtn;
        });
    });
})
</script>