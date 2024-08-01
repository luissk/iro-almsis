<?php 
namespace App\Models;

use CodeIgniter\Model;

class ProductoModel extends Model{
    
    //MANTTO CATEGORIAS
    public function saveCategoria($categoria){
        $query = "insert into categoria(categoria) values('".$categoria."')";
        $st = $this->db->query($query);

        return $this->db->insertID();
    }

    public function updateCategoria($categoria, $idcategoria){
        $query = "update categoria set categoria='".$categoria."' where idcategoria=$idcategoria";
        $st = $this->db->query($query);

        return $st;
    }

    public function getCategorias(){
        $query = "select * from categoria";
        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function deleteCategoria($idcategoria){
        $query = "delete from categoria where idcategoria=$idcategoria";
        $st = $this->db->query($query);

        return $st;
    }

    public function existsEnProducto($idcategoria){
        $query = "select count(idcategoria) total from producto where idcategoria=$idcategoria";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }
    
    //MANTTO U MEDIDAS
    public function saveMedida($medida){
        $query = "insert into unidadm(um) values('".$medida."')";
        $st = $this->db->query($query);

        return $this->db->insertID();
    }

    public function updateMedida($medida, $idum){
        $query = "update unidadm set um='".$medida."' where idum=$idum";
        $st = $this->db->query($query);

        return $st;
    }

    public function getMedidas(){
        $query = "select * from unidadm";
        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function deleteMedida($idum){
        $query = "delete from unidadm where idum=$idum";
        $st = $this->db->query($query);

        return $st;
    }

    public function existsUMEnProducto($idum){
        $query = "select count(idum) total from producto where idum=$idum";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }


    //MANTTO PRODUCTOS
    public function getProductos($desc = null,$status = null, $total = null){
        $condicion = "";
        if($desc != null){
            $condicion .= " and p.descripcion like '%$desc%'";
        }
        if($status != null){
            $condicion .= " and p.status=$status";
        }  
        if($total != null){
            $condicion .= " and (
                p.stock + 
                (select ifnull(sum(de.cantidad),0) from detalle_entrada de where de.idproducto=p.idproducto) -
                (select ifnull(sum(ds.cantidad),0) from detalle_salida ds inner join salida s on ds.idsalida=s.idsalida where ds.idproducto=p.idproducto and s.status=1) 
            ) > 0";
        }

        $query = "select p.idproducto,p.codigo, p.nombre, p.descripcion, p.fechareg, p.idcategoria, p.stock, p.ubicacion, p.idum, p.img, p.status, c.categoria, u.um,
        (select ifnull(sum(ds.cantidad),0) from detalle_salida ds inner join salida s on ds.idsalida=s.idsalida where ds.idproducto=p.idproducto and s.status=1) as nrosalidas,
        (select ifnull(sum(de.cantidad),0) from detalle_entrada de where de.idproducto=p.idproducto) as nroentradas
        from producto p inner join categoria c on p.idcategoria=c.idcategoria inner JOIN unidadm u on p.idum=u.idum 
        where p.idproducto is not null $condicion";
        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function getProducto($idproducto){
        $query = "select p.idproducto, p.codigo, p.nombre, p.descripcion, p.fechareg, p.idcategoria, p.stock,p.status,p.auth,
        (select ifnull(sum(ds.cantidad),0) from detalle_salida ds inner join salida s on ds.idsalida=s.idsalida where ds.idproducto=p.idproducto and s.status=1) as nrosalidas,
        (select ifnull(sum(de.cantidad),0) from detalle_entrada de where de.idproducto=p.idproducto) as nroentradas,
        p.min, p.max, p.ubicacion, p.idum, p.img, c.categoria, u.um from producto p inner join categoria c on p.idcategoria=c.idcategoria inner JOIN unidadm u on p.idum=u.idum 
        where p.idproducto=$idproducto";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }

    public function getProductoPorCodigo($codigo){
        $query = "select p.idproducto, p.codigo, p.nombre, p.descripcion, p.fechareg, p.idcategoria, p.stock,
        (select ifnull(sum(ds.cantidad),0) from detalle_salida ds inner join salida s on ds.idsalida=s.idsalida where ds.idproducto=p.idproducto and s.status=1) as nrosalidas,
        (select ifnull(sum(de.cantidad),0) from detalle_entrada de where de.idproducto=p.idproducto) as nroentradas,
        p.min, p.max, p.ubicacion, p.idum, p.img, c.categoria, u.um from producto p inner join categoria c on p.idcategoria=c.idcategoria inner JOIN unidadm u on p.idum=u.idum 
        where p.codigo=$codigo";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }

    public function existeCodigoPro($codigo){
        $query = "select count(idproducto) total from producto where codigo=$codigo";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }

    public function insertProducto($codigo,$nombre,$categoria,$medida,$stock,$min,$max,$ubicacion,$descripcion,$idusuario,$status,$auth){
        $query = "insert into producto(codigo,nombre,descripcion,min,max,stock,ubicacion,idum,idcategoria,idusuario,status,auth) values($codigo,'".$nombre."','".$descripcion."',$min,$max,$stock,'".$ubicacion."',$medida,$categoria,$idusuario,$status,$auth)";
        $st = $this->db->query($query);

        return $this->db->insertID();
    }

    public function updateProducto($codigo,$nombre,$categoria,$medida,$stock,$min,$max,$ubicacion,$descripcion,$idproducto,$status,$auth){
        $query = "update producto set codigo=$codigo,nombre='".$nombre."',descripcion='".$descripcion."',min=$min,max=$max,stock=$stock,ubicacion='".$ubicacion."',idum=$medida,idcategoria=$categoria,status=$status,auth=$auth where idproducto=$idproducto";
        $st = $this->db->query($query);

        return $st;
    }

    public function updateImagen($idproducto, $img){
        $query = "update producto set img='".$img."' where idproducto=$idproducto";
        $st = $this->db->query($query);

        return $st;
    }

    public function existeEnCompra($idproducto){
        $query = "select count(idproducto) as total from detalle_entrada where idproducto=$idproducto";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }

    public function existeEnVenta($idproducto){
        $query = "select count(idproducto) as total from detalle_salida where idproducto=$idproducto";
        $st = $this->db->query($query);

        return $st->getRowArray();
    }

    public function deleteProducto($idproducto){
        $query = "delete from producto where idproducto=$idproducto";
        $st = $this->db->query($query);

        return $st;
    }

    public function detalle_kardex($idproducto, $fecha_ini = '', $fecha_fin = ''){
        $condicion_ent = "";
        $condicion_sal = "";
        if( $fecha_ini != '' && $fecha_fin != '' ){
            $condicion_ent = " and ent.fecha between '".$fecha_ini."' and '".$fecha_fin."'";
            $condicion_sal = " and sal.fecha between '".$fecha_ini."' and '".$fecha_fin."'";
        }

        $query = "select
        ent.identrada id, ent.fecha fecha, ent.documento, ent.comentario, ent.status, '' idarea, '' area, 'entrada' mov,
        (select ifnull(sum(dent.cantidad),0) from detalle_entrada dent where dent.idproducto=de.idproducto and dent.identrada=ent.identrada) as cant
        from entrada ent 
        inner join detalle_entrada de on ent.identrada=de.identrada
        where de.idproducto = $idproducto $condicion_ent
        UNION
        select
        sal.idsalida id, sal.fecha fecha, sal.documento, sal.comentario, sal.status, sal.idarea idarea, ar.area area, 'salida' mov,
        (select ifnull(sum(dsa.cantidad),0) from detalle_salida dsa inner join salida s on dsa.idsalida=s.idsalida where dsa.idproducto=ds.idproducto and dsa.idsalida=sal.idsalida and s.status=1) as cant
        from salida sal 
        inner join detalle_salida ds on sal.idsalida=ds.idsalida
        inner join area ar on sal.idarea=ar.idarea 
        where ds.idproducto = $idproducto $condicion_sal
        order by fecha desc";

        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function kardexAExcel(){
        $query = "select ds.idproducto,pro.codigo,pro.nombre,um.um,cat.categoria,ds.cantidad,sa.fecha fecha,sa.documento,ar.area area,sa.comentario,'salida' mov
        from detalle_salida ds 
        inner join salida sa on ds.idsalida=sa.idsalida
        inner join area ar on sa.idarea=ar.idarea
        inner join producto pro on ds.idproducto=pro.idproducto
        inner join categoria cat on pro.idcategoria=cat.idcategoria
        inner join unidadm um on pro.idum=um.idum 
        UNION
        select de.idproducto,pro.codigo,pro.nombre,um.um,cat.categoria,de.cantidad,en.fecha fecha,en.documento,'' area,en.comentario,'entrada' mov
        from detalle_entrada de 
        inner join entrada en on de.identrada=en.identrada
        inner join producto pro on de.idproducto=pro.idproducto
        inner join categoria cat on pro.idcategoria=cat.idcategoria
        inner join unidadm um on pro.idum=um.idum
        ORDER by fecha desc";

        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function detalle_area($idarea, $fecha_ini = '', $fecha_fin = '', $idcat = ''){
        $condicion_sal = "";
        if( $fecha_ini != '' && $fecha_fin != '' ){
            $condicion_sal .= " and sal.fecha between '".$fecha_ini."' and '".$fecha_fin."'";
        }
        if( $idcat != '' ){
            $condicion_sal .= " and pro.idcategoria=$idcat ";
        }

        $query = "select DISTINCT(sal.idsalida) id, sal.fecha fecha, sal.documento, sal.comentario, sal.status, sal.idarea idarea, ar.area area, 'salida' mov
        from salida sal
        inner join area ar on sal.idarea=ar.idarea 
        inner join detalle_salida ds on sal.idsalida=ds.idsalida
        inner join producto pro on ds.idproducto =pro.idproducto
        where sal.idarea = $idarea $condicion_sal 
        order by sal.fecha desc";

        $st = $this->db->query($query);

        return $st->getResultArray();
    }

    public function detalle_area_excel($idarea, $fecha_ini = '', $fecha_fin = '', $idcat = ''){
        $condicion_sal = "";
        if( $fecha_ini != '' && $fecha_fin != '' ){
            $condicion_sal .= " and sa.fecha between '".$fecha_ini."' and '".$fecha_fin."'";
        }
        if( $idcat != '' ){
            $condicion_sal .= " and pro.idcategoria=$idcat ";
        }

        $query = "select ds.idproducto,pro.codigo,pro.nombre,um.um,cat.categoria,ds.cantidad,ds.nota,sa.fecha fecha,sa.documento,ar.area area,sa.comentario,'salida' mov
        from detalle_salida ds 
        inner join salida sa on ds.idsalida=sa.idsalida
        inner join area ar on sa.idarea=ar.idarea
        inner join producto pro on ds.idproducto=pro.idproducto
        inner join categoria cat on pro.idcategoria=cat.idcategoria
        inner join unidadm um on pro.idum=um.idum 
        where sa.idarea = $idarea $condicion_sal 
        order by sa.fecha desc;";

        $st = $this->db->query($query);

        return $st->getResultArray();
    }

}