<?php 
namespace App\Controllers;

use Google\Client;
use Google\Service\Drive;

use CodeIgniter\Controller;
use App\Models\UsuarioModel;
use App\Models\ProductoModel;
use App\Models\SalidaModel;
class Salida extends Controller{

    public function __construct(){
        $this->modeloUsuario = model('UsuarioModel');
        $this->modeloProducto = model('ProductoModel');
        $this->modeloSalida = model('SalidaModel');
        $this->session = \Config\Services::session();
    }

    public function index(){
        if(session('idtipousu') == 3){
            return redirect()->to('dashboard');
        }

        $data['title']       = 'Salidas';
        $data['contenido']   = 'salidas/index';
        $data['li_salidas']  = true;
        $data['act_salidas'] = true;

        return view('template/layout', $data);
    }

    public function nuevaSalida(){
        if(session('idtipousu') == 3){
            return redirect()->to('dashboard');
        }

        $data['title']        = 'Nueva salida';
        $data['contenido']    = 'salidas/nuevo';
        $data['li_salidas']  = true;
        $data['act_salidas'] = true;

        $producto = $this->modeloProducto->getProductos();
        $data['productos'] = $producto;

        $areas = $this->modeloSalida->getAreas();
        $data['areas'] = $areas;
        return view('template/layout', $data);
    }

    public function saveSalida(){
        if( $this->request->isAJAX() ){
            /* echo "<pre>";
			print_r($_POST);
            print_r(json_decode($_POST['items'], true));
            echo "</pre>";
            exit(); */
            $fechareg   = $_POST['fechareg'];
            $documento  = $_POST['documento'];
            $comentario = $_POST['comentario'];
            $area       = $_POST['area'];
            $items      = json_decode($_POST['items'], true);

            /*** 0. VERIFICAR STOCK ***/
            foreach($items as $item){
                $idproducto   = $item['idproducto'];
                $cantidad     = $item['cantidad'];
                
                $producto = $this->modeloProducto->getProducto($idproducto);
                $stockact = $producto['stock'] + $producto['nroentradas'] - $producto['nrosalidas'];

                if( $cantidad > $stockact ){
                    echo "La cantidad sobrepasa al stock del producto ".$producto['codigo'];
                    exit();
                }
            }

            $ok = FALSE;
            /*** 1. INSERTAR SALIDA ***/
            $idusuario = session('idusuario');
            $idsalida = $this->modeloSalida->insertSalida($fechareg, $documento, $comentario, $area, $idusuario);

            if($idsalida > 0){
                /*** 2. RECORRER ITEMS ***/
                foreach($items as $item){
                    $idproducto = $item['idproducto'];
                    $cantidad   = $item['cantidad'];
                    $nota       = $item['nota'] == NULL || $item['nota'] == '' ? '' : $item['nota'];
                    /*** 3. BUSCAR PRODUCTO ***/
                    $producto = $this->modeloProducto->getProducto($idproducto);
                    if($producto){
                        /*** 4. INSERTAR DETALLE SALIDA ***/
                        if($this->modeloSalida->insertDetalleSalida($idsalida,$idproducto,$cantidad,$nota)){
                            $ok = TRUE;
                        }
                    }
                }
                if($ok) echo 1;
            }
        }
    }

    public function listSalidasDT(){
        if( $this->request->isAJAX() ){
            $desc = trim($_POST['desc']);
			//echo json_encode($_POST,JSON_UNESCAPED_UNICODE);
			//print_r($_POST);
            $fecha_ini = trim($_POST['fecha_ini']);
            $fecha_fin = trim($_POST['fecha_fin']);

			$salidas = $this->modeloSalida->getSalidas($desc, $fecha_ini, $fecha_fin);
			print json_encode($salidas, JSON_UNESCAPED_UNICODE);
        }
    }

    public function editSalida($idsalida){
        if(session('idtipousu') == 3){
            return redirect()->to('dashboard');
        }

        $salida = $this->modeloSalida->getSalida($idsalida);
        if($salida){
            $detalle = $this->modeloSalida->getDetalle($idsalida);

            $data['title']        = 'Editar salida';
            $data['contenido']    = 'salidas/edit';
            $data['li_salidas']  = true;
            $data['act_salidas'] = true;

            $producto = $this->modeloProducto->getProductos();
            $data['productos'] = $producto;
            
            $data['salida'] = $salida;
            $data['detalle'] = $detalle;

            $areas = $this->modeloSalida->getAreas();
            $data['areas'] = $areas;
            return view('template/layout', $data);
        }else
            return redirect()->to( '/salidas' );
    }

    public function updateSalida(){
        if( $this->request->isAJAX() ){
			/* print_r($_POST);
            print_r(json_decode($_POST['items'], true));exit(); */
            $fechareg   = $_POST['fechareg'];
            $documento  = $_POST['documento'];
            $comentario = $_POST['comentario'];
            $area       = $_POST['area'];
            $idsalida  = $_POST['idsalida'];
            $items      = json_decode($_POST['items'], true);

            //SI NO EXISTE SALIDA NO HACE PASA
            if(!$this->modeloSalida->getSalida($idsalida)) exit();

            /*** 0. VERIFICAR STOCK ***/
            foreach($items as $item){
                $idproducto   = $item['idproducto'];
                $cantidad     = $item['cantidad'];
                
                $producto = $this->modeloProducto->getProducto($idproducto);
                $stockact = $producto['stock'] + $producto['nroentradas'] - $producto['nrosalidas'];

                $stock_adi = 0;//PARA VALIDAR EL STOCK YA AGREGADO EN DETALLE DE LA BD
                $ds = $this->modeloSalida->getDetalleSalida($idsalida, $idproducto);
                if( $ds ) $stock_adi =  $ds['cantidad'];

                if( $cantidad > ($stockact + $stock_adi) ){
                    echo "La cantidad sobrepasa al stock del producto ".$producto['codigo'];
                    exit();
                }
            }

            $ok = FALSE;
            /*** 1. UPDATE SALIDA ***/
            $update = $this->modeloSalida->updateSalida($fechareg, $documento, $comentario, $idsalida, $area);
            if($update){
                /*** 2. ELIMINAR DETALLE_ENTRADA ***/
                $this->modeloSalida->deleteDetalle($idsalida);
                /*** 3. RECORRER ITEMS ***/
                foreach($items as $item){
                    $idproducto = $item['idproducto'];
                    $cantidad   = $item['cantidad'];
                    $nota       = $item['nota'] == NULL || $item['nota'] == '' ? '' : $item['nota'];
                    /*** 4. BUSCAR PRODUCTO ***/
                    $producto = $this->modeloProducto->getProducto($idproducto);
                    if($producto){
                        /*** 5. INSERTAR DETALLE SALIDA ***/
                        if($this->modeloSalida->insertDetalleSalida($idsalida,$idproducto,$cantidad,$nota)){
                            $ok = TRUE;
                        }
                    }
                }
                if($ok) echo 1;
            }
        }
    }

    public function deleteSalida(){
        if( $this->request->isAJAX() ){
            //print_r($_POST);
            $idsalida = $_POST['idsalida'];

            //SI NO EXISTE ENTRADA NO HACE PASA
            if(!$this->modeloSalida->getSalida($idsalida)) exit();

            /*** 1. STATUS 0 A LA SALIDA ***/
            if( $this->modeloSalida->deleteDetalle($idsalida) ){
                /*** 2. ELIMINAR la ENTRADA ***/
                if( $this->modeloSalida->deleteSalida($idsalida)  ){
                    echo "eliminado";
                }
            }
        }
    }



    public function areas(){
        if(session('idtipousu') == 3){
            return redirect()->to('dashboard');
        }

        $data['title']      = 'Areas';
        $data['contenido']  = 'salidas/areas';
        $data['li_salidas'] = true;
        $data['act_areas']  = true;

        $data['areas'] = $this->modeloSalida->getAreas();

        return view('template/layout', $data);
    }

    function saveUpdateArea(){
        if( $this->request->isAJAX() ){
            //print_r($_POST);
            $area      = trim($_POST['area']);
            $nombres   = trim($_POST['nombres']);
            $apellidos = trim($_POST['apellidos']);
            $dni       = trim($_POST['dni']);
            $idarea    = $_POST['idarea'];

            $existeArea = $this->modeloSalida->existeArea($area); // para validar si el area existe al insertar o actualizar

            if($idarea != '' && $rowArea = $this->modeloSalida->getArea($idarea) ){
                $area_ant = $rowArea['area'];
                if($area_ant != $area){
                    if($existeArea['total'] > 0){
                        echo "existe";
                        exit();
                    }
                }
                //UPDATE
                if( $this->modeloSalida->updateArea($area, $nombres, $apellidos, $dni, $idarea) ){
                    echo "update";
                }
            }else{
                //INSERT
                if($existeArea['total'] > 0){
                    echo "existe";
                }else{
                    if( $this->modeloSalida->insertArea($area, $nombres, $apellidos, $dni) ){
                        echo "insert";
                    }
                }                
            }
        }
    }

    function eliminarArea(){
        if( $this->request->isAJAX() ){
            //print_r($_POST);
            $idarea = $_POST['idarea'];

            $area = $this->modeloSalida->existsAreaInSalida($idarea);

            if( $area['total'] > 0 ){
                echo $area['total'];
            }else{
                if( $this->modeloSalida->deleteArea($idarea) ){
                    echo "delete";
                }
            }

        }
    }

    public function procesaPdf(){
        if( $this->request->isAJAX() ){
            /* print_r($_POST);
            print_r($_FILES); */
            
            if( $salida = $this->modeloSalida->getSalida($_POST['id']) ){
                if( $salida['pdf'] != '' ){
                    echo "<script>swal_alert('Atención', 'YA HAY UN DOCUMENTO PDF', 'info', 'Aceptar')</script>";
                    exit();
                }
            }else{
                echo "<script>swal_alert('Error', 'ALGO SALIO MAL', 'error', 'Aceptar')</script>";
                exit();
            }

            if ( isset($_FILES['pdf']) && $_FILES['pdf']['error'] === UPLOAD_ERR_OK) {
                $archivo = $_FILES['pdf']['tmp_name'];
                $nombre  = $_FILES['pdf']['name'];
                $tamanio = $_FILES['pdf']['size'];
                $tipo    = $_FILES['pdf']['type'];
                //$carpeta   = $_POST["carpeta"];

                if( $tipo != 'application/pdf' ){
                    echo "<script>swal_alert('Atención', 'SOLO DOCUMENTOS PDF', 'info', 'Aceptar')</script>";
                }else if( $tamanio > 2097152 ){
                    echo "<script>swal_alert('Atención', 'EL DOCUMENTO NO DEBE SER MAYOR A 2MB', 'info', 'Aceptar')</script>";
                }else{
                    try {
                        putenv('GOOGLE_APPLICATION_CREDENTIALS=public/apis/subirarchivos-429815-061baebbc2cd.json');
                
                        $client = new Client();
                        $client->useApplicationDefaultCredentials();
                        $client->addScope(Drive::DRIVE);
                        $driveService = new Drive($client);
                        $fileMetadata = new Drive\DriveFile(array(
                            'name' => $nombre,
                            'parents' => array('1NA3Hmnlqqfdq7Af2Sd_i1u60syncWZ99')
                        ));
                        
                        $file = $driveService->files->create($fileMetadata, array(
                            'data' => file_get_contents($archivo),
                            'mimeType' => $tipo,
                            'uploadType' => 'multipart',
                            'fields' => 'id'));                      
                        //print_r($file);
                        //$archivo = $driveService->files->get($file->id);
                        //print_r($archivo);

                        if( $this->modeloSalida->updatePdf($_POST['id'], $file->id) ){
                            echo "<script>
                            swal_alert('Atención', 'SE SUBIO EL PDF', 'success', 'Aceptar');
                            cargarPdf();
                            </script>";
                        }
                    } catch (Exception $e) {
                        echo "Error Message: " . $e;
                    }
                }
            }else{
                echo "<script>swal_alert('Atención', 'SELECCIONE UN PDF', 'info', 'Aceptar')</script>";
            }
        }
    }

    public function cargarPdf(){
        if( $this->request->isAJAX() ){
            if( $salida = $this->modeloSalida->getSalida($_POST['id']) ){
                if( $salida['pdf'] != '' ){
                    echo "
                    <a href='https://drive.google.com/file/d/".$salida['pdf']."/view' target='_blank'>ver pdf</a> 
                    &nbsp;&nbsp;<a onclick='eliminarPdf(\"".$salida['pdf']."\")' title='Eliminar'><i class='fas fa-trash-alt'></i></a>";
                }else{
                    echo "No se ha cargado algún pdf.";
                }
            }else{
                echo "<script>swal_alert('Error', 'ALGO SALIO MAL', 'error', 'Aceptar')</script>";
                exit();
            }
        }
    }

    public function eliminarPdf(){
        if( $this->request->isAJAX() ){
            if( $entrada = $this->modeloSalida->getSalida($_POST['id']) ){
                try {
                    putenv('GOOGLE_APPLICATION_CREDENTIALS=public/apis/subirarchivos-429815-061baebbc2cd.json');
            
                    $client = new Client();
                    $client->useApplicationDefaultCredentials();
                    $client->addScope(Drive::DRIVE);
                    $driveService = new Drive($client);
                    
                    $content = $driveService->files->delete($_POST['fileid']);

                    if( $this->modeloSalida->updatePdf($_POST['id'], NULL) ){
                        echo "<script>
                        swal_alert('Atención', 'SE ELIMINÓ EL PDF', 'success', 'Aceptar');
                        cargarPdf();
                        </script>";
                    }
                } catch (Exception $e) {
                    echo "Error Message: " . $e;
                }
            }else{
                echo "<script>swal_alert('Error', 'ALGO SALIO MAL', 'error', 'Aceptar')</script>";
                exit();
            }
        }
    }

}