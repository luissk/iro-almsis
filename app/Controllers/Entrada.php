<?php 
namespace App\Controllers;

use Google\Client;
use Google\Service\Drive;

use CodeIgniter\Controller;
use App\Models\UsuarioModel;
use App\Models\ProductoModel;
use App\Models\EntradaModel;
class Entrada extends Controller{

    public function __construct(){
        $this->modeloUsuario = model('UsuarioModel');
        $this->modeloProducto = model('ProductoModel');
        $this->modeloEntrada = model('EntradaModel');
        $this->session = \Config\Services::session();
    }

    public function index(){
        if(session('idtipousu') == 3){
            return redirect()->to('dashboard');
        }

        $data['title']        = 'Entradas';
        $data['contenido']    = 'entradas/index';
        $data['li_entradas']  = true;
        $data['act_entradas'] = true;

        return view('template/layout', $data);
    }

    public function nuevaEntrada(){
        if(session('idtipousu') == 3){
            return redirect()->to('dashboard');
        }

        $data['title']        = 'Nueva entrada';
        $data['contenido']    = 'entradas/nuevo';
        $data['li_entradas']  = true;
        $data['act_entradas'] = true;

        $producto = $this->modeloProducto->getProductos();
        $data['productos'] = $producto;
        return view('template/layout', $data);
    }

    public function saveEntrada(){
        if( $this->request->isAJAX() ){
			/* print_r($_POST);
            print_r(json_decode($_POST['items'], true)); */
            $fechareg   = $_POST['fechareg'];
            $documento  = $_POST['documento'];
            $comentario = $_POST['comentario'];
            $items      = json_decode($_POST['items'], true);

            $ok = FALSE;
            /*** 1. INSERTAR ENTRADA ***/
            $idusuario = session('idusuario');
            $identrada = $this->modeloEntrada->insertEntrada($fechareg, $documento, $comentario, $idusuario);

            if($identrada > 0){
                /*** 2. RECORRER ITEMS ***/
                foreach($items as $item){
                    $idproducto   = $item['idproducto'];
                    $cantidad     = $item['cantidad'];
                    /*** 3. BUSCAR PRODUCTO ***/
                    $producto = $this->modeloProducto->getProducto($idproducto);
                    if($producto){
                        /*** 4. INSERTAR DETALLE COMPRA ***/
                        if($this->modeloEntrada->insertDetalleEntrada($identrada,$idproducto,$cantidad)){
                            $ok = TRUE;
                        }
                    }
                }
                if($ok) echo 1;
            }
        }
    }

    public function listEntradasDT(){
        if( $this->request->isAJAX() ){
            $desc = trim($_POST['desc']);
			//echo json_encode($_POST,JSON_UNESCAPED_UNICODE);
			//print_r($_POST);
			$entrada = $this->modeloEntrada->getEntradas($desc);
			print json_encode($entrada, JSON_UNESCAPED_UNICODE);
        }
    }

    public function editEntrada($identrada){
        if(session('idtipousu') == 3){
            return redirect()->to('dashboard');
        }
        
        $entrada = $this->modeloEntrada->getEntrada($identrada);
        if($entrada){
            $detalle = $this->modeloEntrada->getDetalle($identrada);

            $data['title']        = 'Editar entrada';
            $data['contenido']    = 'entradas/edit';
            $data['li_entradas']  = true;
            $data['act_entradas'] = true;

            $producto = $this->modeloProducto->getProductos();
            $data['productos'] = $producto;
            
            $data['entrada'] = $entrada;
            $data['detalle'] = $detalle;
            return view('template/layout', $data);
        }else
            return redirect()->to( '/entradas' );
    }

    public function updateEntrada(){
        if( $this->request->isAJAX() ){
			/* print_r($_POST);
            print_r(json_decode($_POST['items'], true)); */
            $fechareg   = $_POST['fechareg'];
            $documento  = $_POST['documento'];
            $comentario = $_POST['comentario'];
            $identrada  = $_POST['identrada'];
            $items      = json_decode($_POST['items'], true);

            //SI NO EXISTE ENTRADA NO HACE PASA
            if(!$this->modeloEntrada->getEntrada($identrada)) exit();

            $ok = FALSE;
            /*** 1. UPDATE ENTRADA ***/
            $update = $this->modeloEntrada->updateEntrada($fechareg, $documento, $comentario, $identrada);
            if($update){
                /*** 2. ELIMINAR DETALLE_ENTRADA ***/
                $this->modeloEntrada->deleteDetalle($identrada);
                /*** 3. RECORRER ITEMS ***/
                foreach($items as $item){
                    $idproducto   = $item['idproducto'];
                    $cantidad     = $item['cantidad'];
                    /*** 4. BUSCAR PRODUCTO ***/
                    $producto = $this->modeloProducto->getProducto($idproducto);;
                    if($producto){
                        /*** 5. INSERTAR DETALLE ENTRADA ***/
                        if($this->modeloEntrada->insertDetalleEntrada($identrada,$idproducto,$cantidad)){
                            $ok = TRUE;
                        }
                    }
                }
                if($ok) echo 1;
            }
        }
    }

    public function deleteEntrada(){        
        if( $this->request->isAJAX() ){
            //print_r($_POST);
            $identrada = $_POST['identrada'];

            //SI NO EXISTE ENTRADA NO HACE PASA
            if(!$this->modeloEntrada->getEntrada($identrada)) exit();

            /*** 1. ELIMINAR DETALLE_ENTRADA ***/
            if( $this->modeloEntrada->deleteDetalle($identrada) ){
                /*** 2. ELIMINAR la ENTRADA ***/
                if( $this->modeloEntrada->deleteEntrada($identrada)  ){
                    echo "eliminado";
                }
            }
        }
    }

    public function pruebaDrive(){
        try {
            putenv('GOOGLE_APPLICATION_CREDENTIALS=public/apis/subirarchivos-429815-982bf1f23734.json');
    
            $client = new Client();
            $client->useApplicationDefaultCredentials();
            $client->addScope(Drive::DRIVE);
            $driveService = new Drive($client);
            $fileMetadata = new Drive\DriveFile(array(
                'name' => 'docum.pdf',
                'parents' => array('17M49Kf9kKkYXo-l-Z04CZ-CYfz36RI0M')
            ));
            $content = file_get_contents('public/doc.pdf');
            $file = $driveService->files->create($fileMetadata, array(
                'data' => $content,
                'mimeType' => 'application/pdf',
                'uploadType' => 'multipart',
                'fields' => 'id'));
            printf("File ID: %s\n", $file->id);
            return $file->id;
        } catch (Exception $e) {
            echo "Error Message: " . $e;
        }
    }

    public function procesaPdf(){
        if( $this->request->isAJAX() ){
            /* print_r($_POST);
            print_r($_FILES); */
            
            if( $entrada = $this->modeloEntrada->getEntrada($_POST['id']) ){
                if( $entrada['pdf'] != '' ){
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
                        putenv('GOOGLE_APPLICATION_CREDENTIALS=public/apis/subirarchivos-429815-982bf1f23734.json');
                
                        $client = new Client();
                        $client->useApplicationDefaultCredentials();
                        $client->addScope(Drive::DRIVE);
                        $driveService = new Drive($client);
                        $fileMetadata = new Drive\DriveFile(array(
                            'name' => $nombre,
                            'parents' => array('1fMzILlEcqMkXFCK0rTizBhbs0EXm8_lP')
                        ));
                        
                        $file = $driveService->files->create($fileMetadata, array(
                            'data' => file_get_contents($archivo),
                            'mimeType' => $tipo,
                            'uploadType' => 'multipart',
                            'fields' => 'id'));                      
                        //print_r($file);
                        //$archivo = $driveService->files->get($file->id);
                        //print_r($archivo);

                        if( $this->modeloEntrada->updatePdf($_POST['id'], $file->id) ){
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
            if( $entrada = $this->modeloEntrada->getEntrada($_POST['id']) ){
                if( $entrada['pdf'] != '' ){
                    echo "
                    <a href='https://drive.google.com/file/d/".$entrada['pdf']."/view' target='_blank'>ver pdf</a> 
                    &nbsp;&nbsp;<a onclick='eliminarPdf(\"".$entrada['pdf']."\")' title='Eliminar'><i class='fas fa-trash-alt'></i></a>";

                    //echo '<iframe src="https://drive.google.com/file/d/'.$entrada['pdf'].'/preview" frameborder="0" style="width:100%; height: 500px"></iframe>';
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
            if( $entrada = $this->modeloEntrada->getEntrada($_POST['id']) ){
                try {
                    putenv('GOOGLE_APPLICATION_CREDENTIALS=public/apis/subirarchivos-429815-982bf1f23734.json');
            
                    $client = new Client();
                    $client->useApplicationDefaultCredentials();
                    $client->addScope(Drive::DRIVE);
                    $driveService = new Drive($client);
                    
                    $content = $driveService->files->delete($_POST['fileid']);

                    if( $this->modeloEntrada->updatePdf($_POST['id'], NULL) ){
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