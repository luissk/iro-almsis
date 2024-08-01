<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-sm-3">
                <h4 class="m-0 text-dark">Salidas</h4>
            </div><!-- /.col -->
            <div class="col-sm-9 text-right">
                <a class="btn btn-primary" href="nueva-salida" role="button">
                    NUEVA SALIDA
                </a>
            </div>
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<section class="content">
    <div class="container-fluid bg-white py-2">
        <div class="row">
            <div class="col-sm-3">
                <label for="">Por producto</label>
                <input type="text" name="" id="desc" class="form-control" placeholder="BUSCA POR PRODUCTO">
            </div>
            <div class="col-sm-3">
                <label for="">Areas</label>
                <div id="cboArea">
                    <select class="form-control"><option value="">Areas</option></select>
                </div>
            </div>
            <div class="col-sm-2">
                <label for="fecha_ini">Fecha Ini</label>
                <input type="date" name="fecha_ini" id="fecha_ini" class="form-control" placeholder="fecha ini">
            </div>
            <div class="col-sm-2">
                <label for="fecha_fin">Fecha Fin</label>
                <input type="date" name="fecha_fin" id="fecha_fin" class="form-control">
            </div>
            <div class="col-sm-2 d-flex align-items-end">
                <button class="btn btn-info" id="btnFiltrarFecha">Filtrar Fechas</button>
            </div>
        </div>
    </div>
    <div class="container-fluid bg-white mt-2 py-2">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-condensed dt-responsive" width="100%" id="tblSalidas">
                    <thead>
                    <tr>
                        <th>DOCUMENTO</th>
                        <th>FECHA_SALIDA</th>
                        <th>AREA</th>
                        <th>COMENTARIO</th>
                        <th>OPCION_SALIDA</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

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

<script>
$(function(){
    var $table = $('#tblSalidas').dataTable({
        initComplete: function () {
            //var counter = 0;
            this.api().columns( [2] ).every( function () {
                var column = this;
                //counter++;
                $('#cboArea').html("");
                var select = $('<select class="form-control"><option value="">Areas</option></select>')
                    .appendTo( $('#cboArea') )
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
            "url": 'salida/listSalidasDT',
            "dataSrc":"",
            "type": "POST",
            "data": {
                "desc": function() { return $('#desc').val() },
                "fecha_ini": function() { return $('#fecha_ini').val() }, 
                "fecha_fin": function() { return $('#fecha_fin').val() }
            },
            "complete": function(xhr, responseText){
                //console.log(xhr);
                //console.log(xhr.responseText); //*** responseJSON: Array[0]
            }
        },
        "columns":[
            {"data": "documento"},
            {"data": "fecha"},
            {"data": "area"},
            {"data": "comentario"},
            {"data": "idsalida",
                "mRender": function (data, type, row) {
                    return "<a title='editar' class='btn btn-success btn-sm' role='button' href='edit-salida-"+data+"'><i class='fa fa-edit'></i></a> <a href='' title='eliminar' class='btn btn-danger btn-sm deleteSalida' idsal="+data+" doc='"+row.documento+"' role='button'><i class='fa fa-trash-alt'></i></a> <a title='ver' class='btn btn-primary btn-sm verdetalle' role='button' data-idsal="+data+" href=''><i class='fa fa-search'></i></a>";
                }
            }
        ],
        "aaSorting": [[ 4, "desc" ]],
        "pageLength": 25
    });

    $("#desc").on('keyup', function(e){
        //console.log($(this).val());
        $('#tblSalidas').DataTable().ajax.reload()
    });

    $("#tblSalidas").on('click', '.deleteSalida', function(e){
        e.preventDefault();
        let idsalida = $(this).attr('idsal'),
            doc = $(this).attr('doc');

        let objConfirm = {
            title: '¿Estás seguro?',
            text: "Vas a eliminar la salida: "+doc,
            icon: 'warning',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'No',
            funcion: function(){
                $.post('salida/deleteSalida', {
                    idsalida:idsalida
                }, function(data){
                    //console.log(data);
                    if(data == "eliminado"){
                        location.reload();
                    }else{
                        //swal_alert('No puedes eliminar la entrada', data, 'info', 'Aceptar');
                    }
                });
            }
        }            
        swal_confirm(objConfirm);
    });

    $("#tblSalidas").on('click', '.verdetalle', function(e){
        e.preventDefault();
        let id = $(this).data('idsal'),
            mov = 'salida';

        $("#detalle_mov").html("<h3>CARGANDO...</h3>");
        $.post('producto/detalleMov', {
            id,mov,idproducto:0
        }, function(data){
            $("#detalle_mov").html(data);
        })
        $("#modalDetalle").modal();
    });

    $("#btnFiltrarFecha").on('click', function(){
        let fecha_ini = $('#fecha_ini').val(),
            fecha_fin = $('#fecha_fin').val();

        if( Date.parse(fecha_fin) < Date.parse(fecha_ini) ){
            swal_alert('Atención', 'La fecha final debe ser mayor a la fecha inicial', 'info', 'Aceptar');
            return;
        }            
        $('#tblSalidas').DataTable().ajax.reload()
    });

})  
</script>