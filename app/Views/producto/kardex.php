<style>
/* #myTab .nav-link.active{
    border-top: 3px solid #666;
} */
.dataTables_filter input.form-control-sm[type='search'] { width: 250px }

.pinta-fila{
    background-color: #ffc107;
}
</style>

<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-1">
            <div class="col-sm-6">
                <h4 class="m-0 text-dark">Kardex</h4>
            </div><!-- /.col -->
            <div class="col-sm-6 text-right">
                <!-- <a href="detalle-en-excel" target='_blank' class="btn btn-primary">Detalle en excel</a> -->
            </div>
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>

<section class="content bg-white py-2">
    <div class="card card-gray card-tabs">
        <div class="card-header p-0 pt-1">
            <ul class="nav nav-tabs" id="myTab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="product-tab" data-toggle="tab" href="#product" role="tab" aria-controls="product" aria-selected="true">POR PRODUCTO</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="area-tab" data-toggle="tab" href="#area" role="tab" aria-controls="area" aria-selected="false">POR AREA</a>
                </li>
            </ul>
        </div>
    </div>
    
    <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="product" role="tabpanel" aria-labelledby="product-tab">
            <div class="container-fluid py-4" id="kardex_pro">
                <!-- <div class="row">
                    <div class="col-sm-6 mb-2">
                        <input type="text" name="" id="desc" class="form-control" placeholder="BUSCA POR DESCRIPCION">
                    </div>
                </div> -->

                <div class="row">
                    <div class="col-sm-12">
                        <table class="table table-bordered table-condensed dt-responsive" width="100%" id="tblProductos">
                            <thead>
                            <tr>
                                <th>CODIGO</th>
                                <th>INICIAL</th>
                                <th>ENTRADAS</th>
                                <th>SALIDAS</th>
                                <th>STOCK</th>
                                <th>NOMBRE</th>
                                <th>UM</th>
                            </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>

            <div id="detail_pro" class="overlay-wrapper" style="display:none;">
                <div class="">
                    <div class="overlay"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                </div>
            </div>
        </div>
        <div class="tab-pane fade" id="area" role="tabpanel" aria-labelledby="area-tab">
            <?php 
                /* echo "<pre>";
                echo print_r($areas);
                echo "</pre>"; */
            ?>
            <div class="col-sm-12">
                <div class="card card-default border border-white">
                    <div class="card-header">                        
                        <div class="row">
                            <div class="col-sm-4">
                                <label for="k_area">Area</label>
                                <select name="k_area" id="k_area" class="form-control select2">
                                    <option value="">Seleccione</option>
                                    <?php
                                    if($areas){
                                        foreach($areas as $a){
                                            $idar      = $a['idarea'];
                                            $area      = $a['area'];
                                            $nombres   = $a['nombres'];
                                            $apellidos = $a['apellidos'];
                                            $dni       = $a['dni'];
                                            echo "<option value='$idar' encargado='$nombres $apellidos' dni='$dni'>$area</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2">
                                <label for="f_ini">Fecha Ini</label>
                                <input type="date" name="f_ini" id="f_ini" class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <label for="f_fin">Fecha Fin</label>
                                <input type="date" name="f_fin" id="f_fin" class="form-control">
                            </div>
                            <div class="col-sm-2">
                                <label for="k_categoria">Categoria (en prueba)</label>
                                <select name="k_categoria" id="k_categoria" class="form-control">
                                    <option value="">Seleccione</option>
                                    <?php
                                    if($categorias){
                                        foreach($categorias as $cat){
                                            $idcat     = $cat['idcategoria'];
                                            $categoria = $cat['categoria'];
                                            echo "<option value='$idcat'>$categoria</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-sm-2 d-flex align-items-end">
                                <button class="btn btn-success" id="btnBuscarPorArea">BUSCAR</button>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" id="detail_area">
                        
                    </div>
                </div>
            </div>    
        </div>

    </div>

</section>



<script>
$(function(){
     $(".select2").select2({'theme':'bootstrap4'});

    var $table = $('#tblProductos').dataTable({        
        "ajax":{
            "url": 'producto/listProductosDT',
            "dataSrc":"",
            "type": "POST",
            "data": {"desc": function() { return $('#desc').val() } },
            "complete": function(xhr, responseText){
                //console.log(xhr);
                //console.log(xhr.responseText); //*** responseJSON: Array[0]
            }
        },
        "columns":[
            {"data": "codigo",
                "mRender": function (data, type, row) {
                    return "<a title='ver movimientos' class='btn detalle' data-idpro='"+row.idproducto+"'>"+data+"</a>";
                }
            },
            {"data": "stock"},
            {"data": "nroentradas"},
            {"data": "nrosalidas"},
            {"data": "stock", render: function ( data, type, row, meta ) {
                //console.log(row)
                return Number(data) + Number(row.nroentradas) - Number(row.nrosalidas)
                }
            },
            {"data": "nombre"},
            {"data": "um"},
            /* {"data": "idproducto",
                "mRender": function (data, type, row) {
                    return "<a title='editar' class='btn btn-success btn-sm' role='button' href='edit-producto-"+data+"'><i class='fa fa-edit'></i></a> <a title='imagen' class='btn btn-success btn-sm' role='button' href='imagen-producto-"+data+"'><i class='fa fa-image'></i></a> <a href='' title='eliminar' class='btn btn-danger btn-sm deleteProducto' idpro="+data+" codigo='"+row.codigo+"' role='button'><i class='fa fa-trash-alt'></i></a>";
                }
            } */
        ],
        "pageLength": 10
    });

    $("#desc").on('keyup', function(e){
        //console.log($(this).val());
        $('#tblProductos').DataTable().ajax.reload()
    });

    $("#tblProductos").on('click', '.deleteProducto', function(e){
        e.preventDefault();
        let idproducto = $(this).attr('idpro'),
            codigo = $(this).attr('codigo');

        let objConfirm = {
            title: '¿Estás seguro?',
            text: "Vas a eliminar el producto: "+codigo,
            icon: 'warning',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'No',
            funcion: function(){
                $.post('producto/deleteProducto', {
                    idproducto:idproducto
                }, function(data){
                    //console.log(data);
                    if(data == "eliminado"){
                        location.reload();
                    }else{
                        swal_alert('No puedes eliminar el producto', data, 'info', 'Aceptar');
                    }
                });
            }
        }            
        swal_confirm(objConfirm);
    });
    
    $("#tblProductos").on('click', '.detalle', function(e){
        e.preventDefault();
        $('#kardex_pro').hide();
        let idproducto = $(this).data('idpro');
        $.post('producto/movimientos/'+idproducto, {
            idproducto:idproducto
        }, function(data){
            //console.log(data);
            $('#detail_pro').html(data);
            $('#detail_pro').show();
        });

        //console.log($("#tblProductos tr").removeClass('pinta-fila'));
        //$(this).parent().parent().addClass('pinta-fila');
    });

    
    //AREAAA
    $("#btnBuscarPorArea").on('click', function(e){
        e.preventDefault();

        let idarea = $("#k_area").val(),
            f_ini = $("#f_ini").val(),
            f_fin = $("#f_fin").val(),
            idcat = $("#k_categoria").val();

        if( idarea == '' ){
            swal_alert('', 'Seleccione un área', 'error', 'Aceptar');
            return;
        }

        if( f_ini.valueOf() > f_fin.valueOf() ){
            swal_alert('', 'La fechas deben estar entre un rango', 'error', 'Aceptar');
            return;
        }

        $.post('producto/movimientosArea', {
            idarea, f_ini, f_fin, idcat
        }, function(data){
            $("#detail_area").html(data);
        });        

    });
});
</script>