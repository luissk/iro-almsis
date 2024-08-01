<?php
/* echo "<pre>";
print_r($producto);
echo "</pre>"; */
$idproducto  = $producto['idproducto'];
$nombre      = $producto['nombre'];
$descripcion = $producto['descripcion'];
$categoria   = $producto['categoria'];
$um          = $producto['um'];
$nrosalidas  = $producto['nrosalidas'];
$nroentradas = $producto['nroentradas'];
$stock       = $producto['stock'];
$stock_act   = $stock + $nroentradas - $nrosalidas;
$img         = $producto['img'];
?>
<br>
<div class="content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-success">
                    <div class="card-header">                        
                        <h3 class="card-title"><a class='return-kardex' href=''><i class="fas fa-arrow-left"></i></a> <?php echo $nombre?></h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <table>
                                <tr>
                                    <th>Descripción</th>
                                    <td><?php echo $descripcion?></td>
                                </tr>
                                <tr>
                                    <th>Categoría</th>
                                    <td><?php echo $categoria?></td>
                                </tr>
                                <tr>
                                    <th>UM</th>
                                    <td><?php echo $um?></td>
                                </tr>
                                <tr>
                                    <th>Stock Inicial</th>
                                    <td><?php echo $stock?></td>
                                </tr>
                                <tr>
                                    <th>Stock Total</th>
                                    <td><?php echo $stock_act?></td>
                                </tr>
                                <tr>
                                    <th colspan = '2'>
                                        <a href='' data-toggle="modal" data-target="#modalImagen">Ver Imagen</a>
                                    </th>
                                </tr>
                            </table>
                        </div>
                    </div>       
                </div>
            </div>
        </div>
        
        <div class="row">
            <div class="col-sm-12">
                <div class="card card-info">
                    <div class="card-header">                        
                        <h3 class="card-title">MOVIMIENTOS</h3>
                        <div class="card-tools">
                            <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-sm-3 col-xl-2">
                                <label for="fecha_ini">Fecha Ini</label>
                                <input type="date" name="fecha_ini" id="fecha_ini" class="form-control">
                            </div>
                            <div class="col-sm-3 col-xl-2">
                                <label for="fecha_fin">Fecha Fin</label>
                                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control">
                            </div>
                            <div class="col-sm-4 col-xl-2 d-flex align-items-end">
                                <button class="btn btn-primary" id="btnFiltrarFecha">Filtrar Fechas</button>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-sm-3">
                                <label for="">Movimiento</label>
                                <div id="search1"></div>
                            </div>
                            <div class="col-sm-3">
                                <label for="">Area</label>
                                <div id="search2"></div>
                            </div>
                        </div>

                        <div class="row mt-3">                            
                            <div class="col-sm-12 table-responsive">                                
                                <table class="table table-bordered table-condensed"  id="tblMovimientos">
                                    <thead>
                                        <tr>
                                            <th>FECHA</th>
                                            <th>DOCUMENTO</th>
                                            <th>MOV</th>
                                            <th>AREA</th>
                                            <th>CANTIDAD</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" style="text-align:right">Total:</th>
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>       
                </div>
            </div>
        </div>
    </diV>
</div>


<div class="modal fade" id="modalDetalle" tabindex="-1" aria-labelledby="titleModalDetalle" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalDetalle">Detalle</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="detalle_mov">
                
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modalImagen" tabindex="-1" aria-labelledby="titleModalImagen" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleModalImagen">Imagen</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <?php
                if($img!= null){
                    $ruta = "public/images/products/".$idproducto."/".$img."";
                    //https://docs.google.com/gview?url=

                    $ext = explode(".", $ruta);
                    $ext = $ext[count($ext) - 1];
                ?>
                <section class="content bg-white">
                    <div class="container-fluid mt-3">
                        <div class="row py-3">
                            <div class="col-sm-12">
                                <?php
                                if($ext == 'pdf' || $ext == 'PDF'){
                                    echo '<iframe src="'.$ruta.'" frameborder="0" style="width:100%; height: 500px"></iframe>';
                                }else{
                                    echo '
                                    <div class="imagep">
                                        <div class="image-wrap" data-src="'.$ruta.'" id="image-wrap">
                                            <img src="'.$ruta.'" alt="" id="img" class="img-fluid"/>
                                        </div>
                                    </div>
                                    ';
                                }
                                ?>                
                            </div>          
                        </div>
                    </div>
                </section>
                <?php
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
$(function(){
    var $table = $('#tblMovimientos').dataTable({
        initComplete: function () {
            var counter = 0;
            this.api().columns( [2,3] ).every( function () {
                var column = this;
                counter++;
                var select = $('<select class="form-control"><option value="">Seleccione</option></select>')
                    .appendTo( $('#search' + counter) )
                    .on( 'change', function () {
                        var val = $.fn.dataTable.util.escapeRegex(
                            $(this).val()
                        );
 
                        column
                            .search( val ? '^'+val+'$' : '', true, false )
                            .draw();
                    } );
 
                column.data().unique().sort().each( function ( d, j ) {
                    select.append( '<option value="'+d+'">'+d+'</option>' );
                } );
            } );
        },    
        "ajax":{
            "url": 'producto/movimientosDT/<?php echo $idproducto?>',
            "dataSrc":"",
            "type": "POST",
            "data": {
                "fecha_ini": function() { return $('#fecha_ini').val() }, 
                "fecha_fin": function() { return $('#fecha_fin').val() }
            },
            "complete": function(xhr, responseText){
                //console.log(xhr);
                //console.log(xhr.responseText); //*** responseJSON: Array[0]
            }
        },
        "columns":[
            {"data": "fecha",
                "mRender": function (data, type, row) {
                    return "<a title='detalle' class='btn detalle' data-id='"+row.id+"' data-mov='"+row.mov+"'>"+data+"</a>";
                }
            },
            {"data": "documento"},
            {"data": "mov"},
            {"data": "area"},
            {"data": "cant"}
        ],
        "aaSorting": [[ 0, "desc" ]],
        "pageLength": 25,

        "footerCallback": function ( row, data, start, end, display ) {
            var api = this.api(), data;
            //console.log(this.api(), data);
 
            // Remove the formatting to get integer data for summation
            var intVal = function ( i ) {
                return typeof i === 'string' ?
                    i.replace(/[\$,]/g, '')*1 :
                    typeof i === 'number' ?
                        i : 0;
            };
 
            // Total over all pages
            total = api
                .column( 4 )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
 
            // Total over this page
            pageTotal = api
                .column( 4, { page: 'current'} )
                .data()
                .reduce( function (a, b) {
                    return intVal(a) + intVal(b);
                }, 0 );
          

            // Total filtered rows on the selected column (code part added)
            var sumCol4Filtered = display.map(el => data[el][4]).reduce((a, b) => intVal(a) + intVal(b), 0 );
          
            // Update footer
            $( api.column( 4 ).footer() ).html(
                //'$'+pageTotal +' ( $'+ total +' total) ($' + sumCol4Filtered +' filtered)'
                `${pageTotal} de ${total}`
            );
        }
    });

    $("#btnFiltrarFecha").on('click', function(){
        let fecha_ini = $('#fecha_ini').val(),
            fecha_fin = $('#fecha_fin').val();

        if( Date.parse(fecha_fin) < Date.parse(fecha_ini) ){
            swal_alert('Atención', 'La fecha final debe ser mayor a la fecha inicial', 'info', 'Aceptar');
            return;
        }            
        $('#tblMovimientos').DataTable().ajax.reload()
    });

    $("#tblMovimientos").on('click', '.detalle', function(){
        let id = $(this).data('id'),
            mov = $(this).data('mov');

        $("#detalle_mov").html("<h3>CARGANDO...</h3>");
        $.post('producto/detalleMov', {
            id,mov,idproducto:<?php echo $idproducto?>
        }, function(data){
            $("#detalle_mov").html(data);
        })
        $("#modalDetalle").modal();
    });

    $('.return-kardex').on('click', function(e){
        e.preventDefault();
        $('#detail_pro').hide();
        $('#kardex_pro').show();
    })
});
</script>