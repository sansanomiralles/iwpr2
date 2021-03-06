<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Producto extends CI_Model {

  function listaProductos() {
     $this -> db -> select('oid, nombre, descripcion, precio, stock, subcategoriaoid, marcasoid, ofertaoid');
     $this -> db -> from('producto');
     $query = $this -> db -> get();
     if($query ->num_rows() > -1)
       return $query->result();
     else
       return false;
  }

  function listaProductosSubcategoria($subcategoria){
     $this -> db -> select('oid, nombre, descripcion, precio, stock, subcategoriaoid, marcasoid, ofertaoid');
     $this -> db -> from('producto');
     $this -> db -> where('subcategoriaoid', $subcategoria);
     $query = $this -> db -> get();
     if($query ->num_rows() > -1)
       return $query->result();
     else
       return false;
  }

  function anyadirAlCarro($productooid,$cantidad,$username){
    $identificadorUsuario = $this->Producto->getOidUsuarioByUserName($username);
    if($this->session->userdata('logged_in')){
      $this -> db -> select('oid, userName, carrooid');
      $this -> db -> from('user');
      $this -> db -> where('userName', $username);
      $query = $this -> db -> get();
      $result = $query->result();
      foreach($result as $row){
        $identificadorCarro = $row->carrooid;
      }
    }
    else{
        $identificadorCarro = $this->getOidCarro($identificadorUsuario);
        $identificadorUsuario = 10;
    }
    if($identificadorCarro == null){
      //creamos carrito para el usuario
      $data = array(
        'useroid' => $identificadorUsuario ,
      );
      $this->db->insert('carro',$data);
      $this -> db -> select('oid, useroid');
      $this -> db -> from('carro');
      $this -> db -> where('useroid', $identificadorUsuario);
      $query = $this -> db -> get();
      foreach($result as $row){
        $identificadorCarro = $row->oid;
      }
      //ponemos el carro al usuario
      $dataUsuario = array('carrooid' => $identificadorCarro);
      $this->db->set('carrooid',$identificadorCarro,FALSE);
      $this->db->where('oid',$identificadorUsuario);
      $this->db->update('user');
    }

    $this -> db -> select('oid, cantidad, productooid, carrooid');
    $this -> db -> from('lineapedido');
    $this -> db -> where('productooid', $productooid);
    $this -> db -> where('carrooid', $identificadorCarro);
    $query = $this -> db -> get();
    if($query -> num_rows() > 0){
      //modificar cantidad, solo insertar en lineapedido"
      $data = array(
        'cantidad' => 1 ,
        'precio' => $this->getPrecioProducto($productooid) ,
        'precioTotal' => "-1",
        'productooid' => $productooid,
        'carrooid' => $identificadorCarro
      );
      $this->db->insert('lineapedido',$data);
      return true;
    }
    else{//////////
      $precio;
      $stockExistente;
      $this -> db -> select('oid, nombre, descripcion, precio, stock, subcategoriaoid, marcasoid, ofertaoid');
      $this -> db -> from('producto');
      $this -> db -> where('oid', $productooid);
      $query = $this -> db -> get();
      $result = $query->result();
      foreach($result as $row){
          $precio = $row->precio;
          $stockExistente = $row->precio;
      }
      $data = array(
        'cantidad' => 1 ,
        'precio' => $this->getPrecioProducto($productooid) ,
        'precioTotal' => "-1",
        'productooid' => $productooid,
        'carrooid' => $identificadorCarro
      );
      $this->db->insert('lineapedido',$data);
      return true;
    }
  }

  public function getProductosCarrito($username){
    $identificadorCarro;
    if($this->session->userdata('logged_in')){
      $this -> db -> select('oid, userName, carrooid');
      $this -> db -> from('user');
      $this -> db -> where('userName', $username);
      $query = $this -> db -> get();
      $result = $query->result();
      foreach($result as $row){
        $identificadorCarro = $row->carrooid;
      }
    }
    else{
        $identificadorCarro = $this->getOidCarro($this->getOidUsuarioByUserName("invitado"));
    }
    $data = array();
    $oid;
    $this -> db -> select('oid, cantidad, precioTotal, productooid, carrooid');
    $this -> db -> from('lineapedido');
    $this -> db -> where('carrooid', $identificadorCarro);
    $query = $this -> db -> get();
    $result = $query->result();
    $productosCarrito = array();
    foreach($result as $row){
      $productosCarrito[$row->productooid] = 0;
    }
    foreach($result as $row){
      $productosCarrito[$row->productooid] = $productosCarrito[$row->productooid] +1;
    }
    foreach ($productosCarrito as $value) {
      $old = $value;
      $value = array('cantidad' => $value,
                    'nombre' => $this->getNombreByOid(array_search($value,$productosCarrito)),);
      $productosCarrito[array_search($old,$productosCarrito)] = $value;
    }
    return $productosCarrito;
  }


    public function getNombreByOid($oid){
      $nombre;
      $this -> db -> select('oid, nombre, descripcion, precio, stock, subcategoriaoid, marcasoid, ofertaoid');
      $this -> db -> from('producto');
      $this -> db -> where('oid', $oid);
      $query = $this -> db -> get();
      $result = $query->result();
      foreach($result as $row){
        $nombre = $row->nombre;
      }
    return $nombre;
  }

  public function getProductoByOid($oid){
    $this -> db -> select('oid, nombre, descripcion, precio, stock, subcategoriaoid, marcasoid, ofertaoid');
    $this -> db -> from('producto');
    $this -> db -> where('oid', $oid);
    $query = $this -> db -> get();
    if($query ->num_rows() > -1)
      return $query->result();
    else
      return false;
  }

  public function getOpinionesByProductoOid($oid){
    $this -> db -> select('comentario, useroid,ok');
    $this -> db -> from('opinion');
    $this -> db -> where('productooid', $oid);
    $this -> db -> where('ok', 1);
    $query = $this -> db -> get();
    if($query ->num_rows() > -1)
      return $query->result();
    else
      return false;
  }

  public function getNombreUsuarioByOid($oid){
    $this -> db -> select('oid, userName, nombre');
    $this -> db -> from('user');
    $this -> db -> where('oid', $oid);
    $query = $this -> db -> get();
    if($query ->num_rows() > -1)
      return $query->result();
    else
      return false;
  }
  public function vaciarCarrito($userName){
    $identificadorCarro;
    $identificadorUsuario = $this->Producto->getOidUsuarioByUserName($userName);

    //restar stock
    $carroOid = $this->getOidCarro($identificadorUsuario);
    $this -> db -> select('oid, cantidad, productooid, carrooid');
    $this -> db -> from('lineapedido');
    $this -> db -> where('carrooid', $carroOid);
    $query = $this -> db -> get();
    $result = $query->result();
    foreach($result as $row){
       $this->Producto->restarStock($row->productooid,$row->cantidad);
    }

    $this->db->delete('carro', array('useroid' => $identificadorUsuario));
    $data = array(
      'useroid' => $identificadorUsuario,
    );

    $this->db->insert('carro',$data);
    $this -> db -> select('oid, useroid');
    $this -> db -> from('carro');
    $this -> db -> where('useroid', $identificadorUsuario);
    $query = $this -> db -> get();
    $result = $query->result();
    foreach($result as $row){
      $identificadorCarro = $row->oid;
    }

    //ponemos el nuevo carro al usuario
    $dataUsuario = array('carrooid' => $identificadorCarro);
    $this->db->set('carrooid',$identificadorCarro,FALSE);
    $this->db->where('oid',$identificadorUsuario);
    $this->db->update('user');

    $data['volver'] = "http://localhost:8080/pccomponentes/index.php/home/";
    $this->load->view('privada/compraExito', $data);
  }

  public function restarStock($productooid,$cantidad){
    $nuevoStock = $this->getStock($productooid) - $cantidad;
    $dataProducto = array('stock' => $nuevoStock);
    $this->db->set('stock',$nuevoStock,FALSE);
    $this->db->where('oid',$productooid);
    $this->db->update('producto');
  }

  public function getStock($productooid){
    $stock;
    $this -> db -> select('oid, nombre, stock');
    $this -> db -> from('producto');
    $this -> db -> where('oid', $productooid);
    $query = $this -> db -> get();
    $result = $query->result();
    foreach($result as $row){
      $stock = $row->stock;
    }
    return $stock;
  }

  public function getOidCarro($useroid){
    $carro;
    $this -> db -> select('oid, nombre, carrooid');
    $this -> db -> from('user');
    $this -> db -> where('oid', $useroid);
    $query = $this -> db -> get();
    $result = $query->result();
    foreach($result as $row){
      $carro = $row->carrooid;
    }
    return $carro;
  }

  public function getOidUsuarioByUserName($username){
    $oid;
    $this -> db -> select('oid, userName');
    $this -> db -> from('user');
    $this -> db -> where('userName', $username);
    $query = $this -> db -> get();
    $result = $query->result();
    foreach($result as $row){
      $oid = $row->oid;
    }
    return $oid;
  }
  public function anyadirOpinion($oid, $opinion, $useroid) {
      $data = array(
        'comentario' => $opinion ,
        'useroid' => $useroid,
        'productooid' => $oid
      );
      $this->db->insert('opinion',$data);
      return true;
  }

  public function listaCategorias() {
     $this -> db -> select('oid, nombre');
     $this -> db -> from('categoria');
     $query = $this -> db -> get();
     if($query ->num_rows() > -1)
       return $query->result();
     else
       return false;
  }

  public function getSubcategoriasByOid($oid) {
      $this -> db -> select('oid, nombre, categoriaoid');
      $this -> db -> from('subcategoria');
      $this -> db -> where('categoriaoid', $oid);
      $query = $this -> db -> get();
      if($query ->num_rows() > -1)
        return $query->result();
      else
        return false;
  }

  public function getCategoriaByOid($oid) {
      $this -> db -> select('oid, nombre');
      $this -> db -> from('categoria');
      $this -> db -> where('oid', $oid);
      $query = $this -> db -> get();
      if($query ->num_rows() > -1)
        return $query->result();
      else
        return false;
  }
  public function getSubCategoriaByOid($oid) {
      $this -> db -> select('oid, nombre');
      $this -> db -> from('subcategoria');
      $this -> db -> where('oid', $oid);
      $query = $this -> db -> get();
      if($query ->num_rows() > -1)
        return $query->result();
      else
        return false;
  }
  public function getListaProdSub($oid) {
      $this -> db -> select('oid, nombre, descripcion, precio, stock, subcategoriaoid, marcasoid, ofertaoid');
      $this -> db -> from('producto');
      $this -> db -> where('subcategoriaoid', $oid);
      $query = $this -> db -> get();
      if($query ->num_rows() > -1)
        return $query->result();
      else
        return false;
  }

  public function getPrecioCarrito($userName){
    $precioTotal=0;
    $useroid = $this->Producto->getOidUsuarioByUserName($userName);
    $oidCarro = $this->Producto->getOidCarro($useroid);
    $this -> db -> select('oid, cantidad, precio,carrooid');
    $this -> db -> from('lineapedido');
    $this -> db -> where('carrooid', $oidCarro);
    $query = $this -> db -> get();
    $result = $query->result();
    foreach($result as $row){
      $oid = $row->oid;
      $precioTotal = $precioTotal + ($row->cantidad*$row->precio);
    }
    return $precioTotal;
  }
  public function getPrecioProducto($productooid){
    $precio;
    $this -> db -> select('oid, precio');
    $this -> db -> from('producto');
    $this -> db -> where('oid', $productooid);
    $query = $this -> db -> get();
    $result = $query->result();
    foreach($result as $row){
      $precio = $row->precio;
    }
    return $precio;
  }

  public function vaciarCarritoCesta($username){
    $carrooid = $this->Producto->getOidCarro($this->Producto->getOidUsuarioByUserName($username));
    $this->db->delete('lineapedido', array('carrooid' => $carrooid));
    $data = array(
      'carrooid' => $carrooid,
    );
    return true;
  }
}
?>
