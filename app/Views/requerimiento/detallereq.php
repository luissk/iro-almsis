<?php
//echo "<pre>"; echo print_r($req); echo"</pre>";
$idreq      = $req['idreq'];
$codigo     = $req['codigo'];
$fechareg   = $req['fechareg'];
$fecha      = $req['fecha'];
$comentario = $req['comentario'];
$area       = $req['area'];
$usuario    = $req['usuario'];
$nombres    = $req['nombres'];
$usuario2   = $req['usuario2'];
$nombres2   = $req['nombres2'];
$estado     = $req['estado'];

$authreq    = $req['authreq']; 
$authstatus = $req['authstatus'];
$authfecha  = $req['authfecha'];
$authuser   = $req['authuser'];
$authcoment = $req['authcoment'];

//echo "$authreq - $authstatus - $authfecha - $authuser - $authcoment";
?>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-<?php echo h_estadoReq($estado, 'C')?>">
            <div class="card-header">                        
                <h3 class="card-title">REQUERIMIENTO</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table class="table-striped" width="100%">
                            <tr>
                                <th>Estado</th>
                                <td><span class='text-<?php echo h_estadoReq($estado, 'C')?>'><?php echo h_estadoReq($estado)?></span></td>
                            </tr>
                            <tr>
                                <th>Fecha</th>
                                <td><?php echo $fecha;?></td>
                            </tr>
                            <tr>
                                <th>Código</th>
                                <td><?php echo $codigo?></td>
                            </tr>
                            <tr>
                                <th>Comentario</th>
                                <td><?php echo $comentario?></td>
                            </tr>
                            <tr>
                                <th>Area</th>
                                <td><?php echo $area?></td>
                            </tr>
                            <tr>
                                <th>Requerido por</th>
                                <td><?php echo "$usuario - $nombres"?></td>
                            </tr>
                            <tr>
                                <th>Atendido por</th>
                                <td><?php echo "$usuario2 - $nombres2"?></td>
                            </tr>
                            <?php
                            if( $authreq == 1 ){
                                $reqstatus = ($authstatus == '') ? "EN ESPERA" : ($authstatus == 1 ? "AUTORIZADO" : "NO AUTORIZADO");
                            ?>
                            <tr>
                                <th>Requiere autorizacion</th>
                                <td>
                                    Estado: <?php echo $reqstatus;?> <br />
                                    Observación : <?php echo $authcoment;?>
                                </td>
                            </tr>
                            <?php
                            }
                            ?>
                        </table>
                    </div>
                </div>
            </div>       
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="card card-<?php echo h_estadoReq($estado, 'C')?>">
            <div class="card-header">                        
                <h3 class="card-title">DETALLE</h3>
                <div class="card-tools">
                    <button type="button" class="btn btn-tool" data-card-widget="collapse"><i class="fas fa-minus"></i></button>
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-sm-12 table-responsive">
                        <table class="table table-condensed table-bordered">
                            <thead>
                                <tr>
                                    <th>código</th>
                                    <th>producto</th>
                                    <th>cantidad</th>
                                    <th>um</th>
                                    <th>nota</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                foreach($detalle as $d){
                                    //pintar si el producto requiere auth y si el req requiere auth y su estado es diferente de 1;
                                    $bgcolor = '#fff';
                                    if( $d['auth'] == 1 && $authreq == 1 ){ //&& $authstatus != 1
                                        $bgcolor = '#ededed';
                                    }
                                    echo "<tr bgcolor='$bgcolor'>";
                                    echo "<td>$d[codigo]</td>";
                                    echo "<td>$d[nombre]</td>";
                                    echo "<td>$d[cantidad]</td>";
                                    echo "<td>$d[um]</td>";
                                    echo "<td>$d[nota]</td>";
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>       
        </div>
    </div>
</div>