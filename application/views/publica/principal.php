<?php
    $this->load->view('inc/bootstrap.php');
?>
<main>
  <h2>Productos</h2>
  <?php echo "Hola ".$username;?>

  <br>
  <nav class="navbar navbar-toggleable-md navbar-light bg-faded">
  <?php echo anchor('login/index','Logueate','title="Login"'); ?>
  <?php echo anchor('home/logout','Logout','title="Logout"'); ?>
  <?php echo anchor('home/registro','Registro','title="Registro"'); ?>
  <?php echo anchor('home/misdatos','Mis datos','title="Mis datos"'); ?>
  <?php echo anchor('home/acercade','Sobre nosotros','title="Acercade"'); ?>
  </nav>

  <div class="container">
	  <ul class="list-group">
	  <form action="home/addCarro">
		<?php foreach ($producto as $prod): ?>

		    <li class="list-group-item">

		        <a href=<?php echo $direccion.$prod->oid; ?>><?php echo $prod->oid."  ".$prod->nombre ."  ".$prod->precio."€" ?></a>
            <br>
            <a href=<?php echo $direccionAdd.$prod->oid; ?>> Añadir al carrito </a>
		    </li>

		<?php endforeach; ?>
	  </ul>
	  </form>
  </div>
<h4>Carrito</h4>
<table>
  <tr>
    <th>
      Producto
    </th>
    <th>
      Cantidad
    </th>
<?php foreach ($carro as $clave => $prod ): ?>
<tr>
    <th>
        <?php echo $prod['nombre'] ?>
    </th>
    <th>
      <?php echo $prod['cantidad'] ?>
    </th>
</tr>
<?php endforeach; ?>

  <a href=<?php echo $direccionComprar; ?>> Comprar! </a>
</form>
</table>
</main>
