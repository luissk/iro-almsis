<?php
/* if(session('idusuario') != 1){
    echo "<h1>ESTAMOS TRABAJANDO AQUI</h1>";
    exit();
} */

/* echo "<pre>";
print_r($usuario);
echo "</pre>"; */

$user      = $usuario['usuario'];
$idusuario = $usuario['idusuario'];
$nombres   = $usuario['nombres'];
$dni       = $usuario['dni'];
$idtipousu = $usuario['idtipousu'];
$celular   = $usuario['celular'];
$tipo      = $usuario['tipo'];

?>

<link rel="stylesheet" href="<?php echo base_url('public/js/libs/croppie/croppie.css')?>">
<script src="<?php echo base_url('public/js/libs/croppie/croppie.min.js')?>"></script>


<section class="content bg-white">
    <div class="container-fluid py-4">
        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Mis Datos</h3>
            </div>
            <form role="form" id="frmUsuario">

                <div class="card-body">
                    <div class="row">
                        <div class="col-sm-2">
                            <label>Usuario</label>
                            <input type='text' disabled name='usuario' id='usuario' value="<?php echo $user?>" class="form-control"/>
                        </div>
                        <div class="col-sm-3">
                            <label>Nombres</label>
                            <input type='text' name='nombres' id='nombres' value="<?php echo $nombres?>" class="form-control" required/>
                        </div>
                        <div class="col-sm-2">
                            <label>DNI</label>
                            <input type='text' name='dni' id='dni' value="<?php echo $dni?>" class="form-control numerosindecimal" maxlength="8" required/>
                        </div>
                        <div class="col-sm-2">
                            <label>Celular</label>
                            <input type='text' name='celular' id='celular' value="<?php echo $celular?>" class="form-control numerosindecimal" maxlength="9" required/>
                        </div>
                        <div class="col-sm-2">
                            <label>Password</label>
                            <input type='password' name='password' id='password' value="" class="form-control"/>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12 mt-4">
                            <input type="hidden" name="idusuario" id="idusuario" value="<?php echo $idusuario?>">
                            <button class="btn btn-danger" id="btnModificar">MODIFICAR DATOS</button>
                        </div>
                    </div>
                </div>

            </form>
        </div>

        <div class="card card-primary">
            <div class="card-header">
                <h3 class="card-title">Mi Avatar</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-3">
                        <div id="uploaded_image" class="border border-success" style="width:160px; height:160px;">
                        <?php
                        if( file_exists('public/avatar/'.session('idusuario').'.png') ){
                            $ruta_avatar = 'public/avatar/'.session('idusuario').'.png?v='.time();
                            echo "<img src='$ruta_avatar' alt='mi-avatar' class='img-fluid'/>";
                        }
                        ?>
                        </div>
                    </div>
                    <div class="col-sm-9 d-flex align-items-center">
                        <label>Selecciona una imagen</label>
                        <input type="file" name="upload_image" id="upload_image" accept="image/*" class="form-control"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal" id="uploadimageModal" tabindex="-1" aria-labelledby="titleuploadimageModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="titleuploadimageModal">RECORTA TU AVATAR</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-sm-8">
                        <div id="image_demo"></div>
                    </div>
                    <div class="col-sm-4 d-flex align-items-center">
                        <button class="btn btn-success crop_image">Cortar y Subir Imagen</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>


<script>
$(function(){
    $(".numerosindecimal").on("keypress keyup blur",function (event) {
        $(this).val($(this).val().replace(/[^\d].+/, ""));
        if ((event.which < 48 || event.which > 57)) {
            event.preventDefault();
        }
    });

    $("#frmUsuario").on("submit", function(e){
        e.preventDefault();
        let btnHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>',
            textBtn = $('#btnModificar').text();
        $("#btnModificar").attr('disabled', 'disabled');
        $("#btnModificar").html(`${btnHTML} Guardando`);
        $.ajax({
            method: 'POST',
            url: 'usuario/modificarUsuario',
            data: $(this).serialize(),
            success: function(datos){
                console.log(datos);
                let data = JSON.parse(datos);
                if(data.err != ""){
                    swal_alert('Atención', data.err, 'info', 'Aceptar');
                    $("#btnModificar").removeAttr('disabled');
                    $("#btnModificar").text(textBtn);
                }else{                    
                    swal_alert('', 'Usuario modificado', 'success', 'Aceptar');
                    $("#btnModificar").removeAttr('disabled');
                    $("#btnModificar").text(textBtn);
                }
            }
        });
    });
});


$image_crop = $('#image_demo').croppie({
    enableExif: true,
    viewport: {
        width:160,
        height:160,
        type:'square' //circle
    },
        boundary:{
        width:300,
        height:300
    }
});

$('#upload_image').on('change', function(){
    let tipos = ['image/jpeg','image/jpg','image/png'];
    let file = this.files[0];
    let tipofile = file.type;
    let sizefile = file.size;

    if(!tipos.includes(tipofile)){
        swal_alert('Atención', 'IMAGEN SOLO EN (JPEG|JPG|PNG|PDF)', 'info', 'Aceptar');
        $(this).val('');
        return false;
    }
    if(sizefile >= 4192256){
        swal_alert('Atención', 'LA IMAGEN NO DEBE SER MAYOR A 4MB', 'info', 'Aceptar');
        $(this).val('');
        return false;
    }

    var reader = new FileReader();
    reader.onload = function (event) {
        $image_crop.croppie('bind', {
            url: event.target.result
        }).then(function(){
            console.log('jQuery bind complete');
        });
    }
    reader.readAsDataURL(this.files[0]);
    $('#uploadimageModal').modal('show');
});

$('.crop_image').click(function(event){
    $image_crop.croppie('result', {
        type: 'canvas',
        size: 'viewport'
    }).then(function(response){
        $.ajax({
            url:"usuario/avatarUsuario",
            type: "POST",
            data:{"image": response},
            success:function(data){
                $('#uploadimageModal').modal('hide');
                $('#uploaded_image').html(data);
            }
        });
    });
});
</script>
