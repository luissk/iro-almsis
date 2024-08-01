<?php 
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\FuaModel;
use DateTime;

class Fua extends Controller{

    protected $helpers = ['funciones'];

    public function __construct(){
        $this->modeloFua  = model('FuaModel');
        $this->session = \Config\Services::session();
    }

    public function index(){
        $data['title']             = 'Fuas';
        /* $data['li_productos']      = true;
        $data['act_requerimiento'] = true; */

        if(!session('idusuario')) return redirect()->to('dashboard');

        
        $data['contenido']      = 'fua/index';

        return view('template/layout', $data);
    }

    public function guardar(){
        if( $this->request->isAJAX() ){
            $fecha    = trim($_POST['fecha']);
            $codigo   = trim($_POST['codigo']);
            $fua      = trim($_POST['fua']);
            $servicio = trim($_POST['servicio']);
            $paciente = trim($_POST['paciente']);
            $idfuaE   = $_POST['idfuaE'];

            $msj = '';
            if($fecha == '') $msj = 'Ingrese la fecha de atención';
            else if($codigo == '') $msj = 'Ingrese un código';
            else if($fua == '') $msj = 'Ingrese el nro de FUA';
            else if($servicio == '') $msj = 'Ingrese el código de servicio';

            $fecha = date_format(date_create($fecha), "Y-m-d");

            if( $msj != '' ){
                echo "<script>swal_alert('', '".$msj."', 'error', 'Aceptar')</script>";
            }else{
                if( $idfuaE > 0 ){//editar
                    if( $this->modeloFua->editFua($fecha, $codigo, $fua, $servicio, $paciente, $idfuaE) ){
                        echo "e";
                    }
                }else{//insertar
                    //comprobar si existe el fua
                    $fuadb = $this->modeloFua->existeFua($fua);
                    if( $fuadb['total'] > 0 ){
                        echo "<script>swal_alert('', 'YA EXISTE EL FUA: ".$fua."', 'error', 'Aceptar')</script>";
                        exit();
                    }
                    
                    if( $this->modeloFua->insertFua($fecha, $codigo, $fua, $servicio, $paciente) ){
                        echo "i";
                    }
                }
            }
        }
    }

    public function borrarFua(){
        if( $this->request->isAJAX() ){
            $idfua = $_POST['idfua'];
            if( $this->modeloFua->deleteFua($idfua) ){
                echo 'ok';
            }
        }
    }

    public function listar(){
        if( $this->request->isAJAX() ){
            $fuas = $this->modeloFua->listarFuas();
            $data['fuas'] = $fuas;
            
            return view('fua/lista-fuas', $data);
        }
    }
}