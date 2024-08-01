<?php //print_r($fuas);?>

<div class="row">
    <div class="col-sm-12">
    <table class="table table-bordered" id="tblFuas">
        <thead>
            <tr>
                <th>NRO</th>
                <th>FUA</th>
                <th>FECHA AT</th>
                <th>SERVICIO</th>
                <th>CODIGO</th>
                <th>PACIENTE</th>
                <th>OPT</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            if( $fuas ){
                $cont = 0;
                foreach( $fuas as $f ){
                    $cont++;
                    $idfua = $f['idfua'];
                    $fua = $f['fua'];
                    $fecha_atencion = $f['fecha_atencion'];
                    $servicio = $f['servicio'];
                    $paciente = $f['paciente'];
                    $codigo = $f['cod'];
                    
                    echo "<tr>";
                    echo "<td>$cont</td>";
                    echo "<td>$fua</td>";
                    echo "<td>$fecha_atencion</td>";
                    echo "<td>$servicio</td>";
                    echo "<td>$codigo</td>";
                    echo "<td>$paciente</td>";
                    echo "<td>
                        <a class='btn btn-success btn-sm editar' role='button' data-id=$idfua data-fua='".$fua."' data-fecha='".$fecha_atencion."' data-servicio='".$servicio."' data-cod='".$codigo."' data-paciente='".$paciente."'><i class='fa fa-edit'></i></a> &nbsp;
                        <a class='btn btn-danger btn-sm delete' role='button' data-id=$idfua><i class='fa fa-trash'></i></a>                                           
                    </td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</div>
</div>

<script>
$(function(){
    $("#tblFuas").dataTable({
        'ordering': true,
        'order': [[0, 'DESC']]
    });
    
    $("#tblFuas").on('click', '.editar', function(e){
        let id = $(this).data('id'),
            fua = $(this).data('fua'),
            fecha = $(this).data('fecha'),
            servicio = $(this).data('servicio'),
            cod = $(this).data('cod'),
            paciente = $(this).data('paciente');
        //console.log(id);
        /* let tds = $(this).parent().siblings(),
            cant_tds = tds.length;

        console.log($(this).parent().siblings().attr('contenteditable', true), cant_tds)
        tds[1].focus(); */
        let btn = document.querySelector('#btnSave'),
            txtbtn = btn.textContent;
        $("#fecha").val(fecha);
        $("#fua").val(fua);
        $("#servicio").val(servicio);
        $("#codigo").val(cod);
        $("#paciente").val(paciente);
        $("#idfuaE").val(id);
        btn.textContent = "EDITAR";
        
    });

    $("#tblFuas").on('click', '.delete', function(e){
        let idfua = $(this).data('id');
        let objConfirm = {
            title: 'Mensaje',
            text: "¿VAS A ELIMINAR LA FUA?",
            icon: 'warning',
            confirmButtonText: 'Sí',
            cancelButtonText: 'No',
            funcion: function(){                
                $.post('fua/borrarFua', {
                    idfua
                }, function(data){
                    if (data == 'ok' ){
                        loadFuas();
                    }
                });
            }
        }            
        swal_confirm(objConfirm);
    });

});
</script>