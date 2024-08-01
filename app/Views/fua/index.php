<?php
//print_r($usuario);
$codigoAleatorio = stringAleatorio(4);
?>

<section class="content">
    <div class="container-fluid">

        <div class="row pt-3">
            <div class="col-sm-12">
                <div class="card card-default">
                    <div class="card-header">                        
                        <h3 class="card-title">FUAS</h3>
                    </div>
                    <div class="card-body">
                        <form id="frmRegisterFUA" method="POST">
                        <div class="row">
                            <div class="col-sm-2">
                                <label for="fecha">Fecha Atención</label>
                                <input type="text" name="fecha" id="fecha" class="form-control" required>
                            </div>
                            <div class="col-sm-2">
                                <label for="codigo">Código</label>
                                <input type="text" name="codigo" id="codigo" class="form-control" required maxlength="4" value="<?php echo $codigoAleatorio?>">
                            </div>
                            <div class="col-sm-2">
                                <label for="fua">FUA</label>
                                <input type="text" name="fua" id="fua" class="form-control noespacios" maxlength="20" required>
                            </div>
                            <div class="col-sm-1">
                                <label for="servicio">Servicio</label>
                                <input type="text" name="servicio" id="servicio" class="form-control" maxlength="3" required>
                            </div>
                            <div class="col-sm-3">
                                <label for="paciente">Paciente</label>
                                <input type="text" name="paciente" id="paciente" class="form-control" maxlength="60">
                            </div>
                            <div class="col-sm-2 d-flex align-items-end">
                                <button class="btn btn-secondary" id="btnSave">GUARDAR</button>
                                <input type="hidden" name="idfuaE" id="idfuaE">
                            </div>
                        </div>
                        </form>
                        
                    </div>
                </div>

                <div class="card card-default">
                    <div class="card-header">                        
                        <h3 class="card-title">FUAS GUARDADOS</h3>
                    </div>
                    <div class="card-body" id="listaFuas">
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>

<div id="msjFua"></div>


<script>
    $(".noespacios").on("keypress keyup blur",function (event) {    
        $(this).val($(this).val().replace(/[^0-9-]/g, ""));
        //console.log(event.which)
        if ((event.which < 45 || event.which > 57)) {
            event.preventDefault();
        }
    });


    var search_input = document.querySelectorAll('input');
    //console.log(search_input);
    document.addEventListener('DOMContentLoaded', function(){

        search_input.forEach( (v,index) => {

            var recognition = new webkitSpeechRecognition();
            recognition.continuous = true;
            recognition.lang = "es";

            recognition.stop();
            v.addEventListener('click', function(event){
                recognition.start();
            });

            recognition.onresult = function (event) {
                finalResult = '';
                for (var i = event.resultIndex; i < event.results.length; ++i) {
                    if (event.results[i].isFinal) {
                        //console.log(event.results);
                        finalResult = event.results[i][0].transcript;
                        if(finalResult == 'guardar'){
                            $("#frmRegisterFUA").submit();
                            return;
                        }else{
                            v.value = finalResult.toUpperCase().trim().replaceAll(" guion ", "-").replaceAll(" - ", "-");
                        }
                    }
                }
            };
        });
    
    });

$(function(){
    loadFuas();

    $("#frmRegisterFUA").on('submit', function(e){
        e.preventDefault();
        let btn = document.querySelector('#btnSave'),
            //txtbtn = btn.textContent,
            btnHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
        btn.setAttribute('disabled', 'disabled');
        btn.innerHTML = `${btnHTML} PROCESANDO...`;

        $.post('fua/guardar', 
            $(this).serialize()
        , function(data){
            $("#msjFua").html(data);
            //console.log(data);
            if( data == 'e' ){
                swal_alert('', 'FUA MODIFICADO', 'success', 'Aceptar')
            }else if(data == 'i'){
                swal_alert('', 'FUA GUARDADO', 'success', 'Aceptar')
            }
            btn.removeAttribute('disabled');
            btn.innerHTML = 'GUARDAR';
            limpiaCajas();
            loadFuas();
        });
    })
});


function limpiaCajas(){
    $("input:not(#codigo)").val('');
    //$("#fecha").focus();
}

function loadFuas(){
    $.post('fua/listar', {
        1:1
    }, function(data){
        $("#listaFuas").html(data);
        //console.log(data);
    });
}
</script>