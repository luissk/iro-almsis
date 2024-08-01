<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-sm-12">
                <h4 class="m-0 text-dark">Entradas</h4>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<section class="content">
    <div class="container-fluid bg-white py-2">
        <div class="row">
            <div class="col-sm-6">
                <a class="btn btn-primary" href="nueva-entrada" role="button">
                    Nueva Entrada
                </a>
            </div>
            <div class="col-sm-6">
                <input type="text" name="" id="desc" class="form-control" placeholder="BUSCA POR PRODUCTO">
            </div>
        </div>
    </div>
    <div class="container-fluid bg-white mt-2 py-2">
        <div class="row">
            <div class="col-sm-12">
                <table class="table table-bordered table-condensed dt-responsive" width="100%" id="tblEntradas">
                    <thead>
                        <tr>
                            <th>DOCUMENTO</th>
                            <th>FECHA</th>
                            <th>COMENTARIO</th>
                            <th>OPCION</th>
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
    var $table = $('#tblEntradas').dataTable({        
        "ajax":{
            "url": 'entrada/listEntradasDT',
            "dataSrc":"",
            "type": "POST",
            "data": {"desc": function() { return $('#desc').val() } },
            "complete": function(xhr, responseText){
                //console.log(xhr);
                //console.log(xhr.responseText); //*** responseJSON: Array[0]
            }
        },
        "columns":[
            {"data": "documento"},
            {"data": "fecha"},
            {"data": "comentario"},
            {"data": "identrada",
                "mRender": function (data, type, row) {
                    return "<a title='editar' class='btn btn-success btn-sm' role='button' href='edit-entrada-"+data+"'><i class='fa fa-edit'></i></a> <a href='' title='eliminar' class='btn btn-danger btn-sm deleteEntrada' ident="+data+" doc='"+row.documento+"' role='button'><i class='fa fa-trash-alt'></i></a> <a title='ver' class='btn btn-primary btn-sm verdetalle' role='button' data-ident="+data+" href=''><i class='fa fa-search'></i></a>";
                }
            }
        ],
        "aaSorting": [[ 3, "desc" ]],
        "pageLength": 25
    });

    $("#desc").on('keyup', function(e){
        //console.log($(this).val());
        $('#tblEntradas').DataTable().ajax.reload()
    });

    $("#tblEntradas").on('click', '.verdetalle', function(e){
        e.preventDefault();
        let id = $(this).data('ident'),
            mov = 'entrada';

        $("#detalle_mov").html("<h3>CARGANDO...</h3>");
        $.post('producto/detalleMov', {
            id,mov,idproducto:0
        }, function(data){
            $("#detalle_mov").html(data);
        })
        $("#modalDetalle").modal();
    });

    $("#tblEntradas").on('click', '.deleteEntrada', function(e){
        e.preventDefault();
        let identrada = $(this).attr('ident'),
            doc = $(this).attr('doc');

        let objConfirm = {
            title: '¿Estás seguro?',
            text: "Vas a eliminar la entrada: "+doc,
            icon: 'warning',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'No',
            funcion: function(){
                $.post('entrada/deleteEntrada', {
                    identrada:identrada
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

})
</script>