<?php 
namespace App\Models;

use CodeIgniter\Model;

class RequerimientoModel extends Model{
    
    public function insertReq($fecha, $codigo, $comentario, $idusuario, $idarea, $area, $authreq){
        /* $query = "insert into requerimiento(codigo,fecha,comentario,idusuario,estado,idarea,area) 
        values('".$codigo."', '".$fecha."', '".$comentario."', $idusuario, 3, $idarea, '".$area."')";
        $st = $this->db->query($query); */
        $query = "insert into requerimiento(codigo,fecha,comentario,idusuario,estado,idarea,area,authreq) 
        values(?,?,?,?,?,?,?,?)";
        $this->db->query($query, [$codigo, $fecha, $comentario, $idusuario, 3, $idarea, $area, $authreq]);

        return $this->db->insertID();
    }

    public function insertDetalleReq($idreq,$idproducto,$cantidad,$nota){
        $query = "insert into req_detalle(idreq,idproducto,cantidad,nota) values($idreq,$idproducto,$cantidad,'$nota')";
        $st = $this->db->query($query);

        return $st;
    }

    public function listarRequerimientos($idarea = '', $estado = '', $idusuario = ''){
        $condicion = '';
        if($idarea != ''){
            $condicion .= " and req.idarea = $idarea";
        }
        if($estado != ''){
            $condicion .= " and req.estado = $estado";
        }
        if($idusuario != ''){
            $condicion .= " and req.idusuario = $idusuario ";
        }
        $query = "select req.idreq, req.codigo, req.fechareg, req.fecha, req.comentario, req.idusuario, req.estado, req.idusuario2,
        req.idarea, req.area, req.fechadespacho, req.fechaprocesado, req.authreq,req.authstatus,req.authfecha,req.authuser,req.authcoment,
        usu.usuario, usu.nombres,
        usu2.usuario usuario2, usu2.nombres nombres2
        from requerimiento req
        inner join usuario usu on req.idusuario = usu.idusuario
        left join usuario usu2 on req.idusuario2 = usu2.idusuario where req.idreq is not null 
        $condicion order by req.idreq desc";
        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function getRequerimiento($idreq){
        $query = "select req.idreq, req.codigo, req.fechareg, req.fecha, req.comentario, req.idusuario, req.estado, req.idusuario2,
        req.idarea, req.area, req.fechadespacho, req.fechaprocesado,req.authreq,req.authstatus,req.authfecha,req.authuser,req.authcoment,
        usu.usuario, usu.nombres,
        usu2.usuario usuario2, usu2.nombres nombres2
        from requerimiento req
        inner join usuario usu on req.idusuario = usu.idusuario
        left join usuario usu2 on req.idusuario2 = usu2.idusuario
        where req.idreq=$idreq";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }
    
    public function listarDetalleReq($idreq){
        $query = "select rd.idreq, rd.idproducto, rd.cantidad, rd.nota, pro.codigo, pro.nombre, pro.auth, um.um
        from req_detalle rd 
        inner join producto pro on rd.idproducto=pro.idproducto 
        inner join unidadm um on pro.idum=um.idum
        where rd.idreq=$idreq";
        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function eliminarReq($idreq){
        $query = "delete from requerimiento where idreq=$idreq";
        $st = $this->db->query($query);

        return $st;
    }

    public function eliminarDetalleRe($idreq){
        $query = "delete from req_detalle where idreq=$idreq";
        $st = $this->db->query($query);

        return $st;
    }

    public function editReq($idreq, $comentario, $authreq, $authstatus, $authfecha, $authuser, $authcoment){
        /*$query = "update requerimiento set comentario='".$comentario."',authreq=$authreq,
        authstatus=$authstatus,authfecha=$authfecha, authuser=$authuser, authcoment=$authcoment  
        where idreq=$idreq";
        $st = $this->db->query($query);*/

        $query = "update requerimiento set comentario=?,authreq=?,authstatus=?,authfecha=?,authuser=?, authcoment=? 
        where idreq=?";
        $st = $this->db->query($query, [$comentario, $authreq, $authstatus, $authfecha, $authuser, $authcoment, $idreq]);

        return $st;
    }

    public function countReq($estado = ''){
        $condicion = '';
        if($estado != ''){
            $condicion = "where estado=$estado";
        }
        $query = "select count(idreq) total from requerimiento $condicion";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }

    public function cambiarEstado($idreq, $estado, $idusuario2){
        if( $estado == 2 ){
            $query = "update requerimiento set estado=$estado, idusuario2=$idusuario2, fechadespacho=now()
            where idreq=$idreq";
        }else if( $estado == 1 ){
            $query = "update requerimiento set estado=$estado, idusuario2=$idusuario2, fechaprocesado=now()  
            where idreq=$idreq";
        }else{
            $query = "update requerimiento set estado=$estado, idusuario2=$idusuario2 where idreq=$idreq";
        }

        $st = $this->db->query($query);

        return $st;
    }

    public function autorizaRequerimiento($idreq, $authstatus, $observacion, $idusuario){
        $query = "update requerimiento set authstatus=?,authfecha=now(),authuser=?,authcoment=? 
        where idreq=?";
        $st = $this->db->query($query, [$authstatus, $idusuario, $observacion, $idreq]);

        return $st;
    }



    public function getRequerimientoPorCodigo($codigo){
        $query = "select req.idreq, req.codigo, req.fechareg, req.fecha, req.comentario, req.idusuario, req.estado, req.idusuario2,
        req.idarea, req.area, req.fechadespacho, req.fechaprocesado, usu.usuario, usu.nombres,
        usu2.usuario usuario2, usu2.nombres nombres2
        from requerimiento req
        inner join usuario usu on req.idusuario = usu.idusuario
        left join usuario usu2 on req.idusuario2 = usu2.idusuario
        where req.codigo='".$codigo."'";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }

    public function listarDetalleReqPorCodigo($codigo){
        $query = "select rd.idreq, rd.idproducto, rd.cantidad, rd.nota, pro.codigo, pro.nombre, pro.stock, um.um,
		(select ifnull(sum(dsa.cantidad),0) from detalle_salida dsa inner join salida s on dsa.idsalida=s.idsalida where dsa.idproducto=pro.idproducto) as nrosalidas,
        (select ifnull(sum(de.cantidad),0) from detalle_entrada de where de.idproducto=pro.idproducto) as nroentradas,
        req.codigo codigoreq
        from req_detalle rd 
        inner join requerimiento req on rd.idreq=req.idreq
        inner join producto pro on rd.idproducto=pro.idproducto 
        inner join unidadm um on pro.idum=um.idum
        where req.codigo='".$codigo."' ";
        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function getSalidaPorCodigoReq($cod){
        $query = "select documento from salida where documento=?";
        $st = $this->db->query($query, [$cod]);

        return $st->getRowArray();
    }

}
