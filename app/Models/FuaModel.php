<?php 
namespace App\Models;

use CodeIgniter\Model;

class FuaModel extends Model{

    public function insertFua($fecha, $codigo, $fua, $servicio, $paciente){
        $query = "insert into fua(fua,fecha_atencion,servicio,paciente,cod) 
        values(?,?,?,?,?)";
        $st = $this->db->query($query, [$fua, $fecha, $servicio, $paciente, $codigo]);

        return $st;
    }

    public function listarFuas(){
        $query = "select * from fua";
        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function editFua($fecha, $codigo, $fua, $servicio, $paciente, $idfuaE) {
        $query = "update fua set fua=?,fecha_atencion=?,servicio=?,paciente=?,cod=? where idfua=?";
        $st = $this->db->query($query, [$fua, $fecha, $servicio, $paciente, $codigo, $idfuaE]);

        return $st;
    }   

    public function deleteFua($idfua){
        $query = "delete from fua where idfua=?";
        $st = $this->db->query($query, [$idfua]);

        return $st;
    }

    public function existeFua($fua){
        $query = "select count(idfua) as total from fua where fua=?";
        $st = $this->db->query($query, [$fua]);

        return $st->getRowArray();
    }

}