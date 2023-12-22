<?php
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
  header("Location: login.html");
}else{

 
require 'header.php';

if ($_SESSION['escritorio']==1) {
  require_once "../modelos/Negocio.php";
  $cnegocio = new Negocio();
  $rsptan = $cnegocio->listar();
  $regn=$rsptan->fetch_object();

  require_once "../modelos/Consultas.php";
  $consulta = new Consultas();
  $rsptac = $consulta->totalcomprahoy();
  $regc=$rsptac->fetch_object();
  $totalc=$regc->total_compra;

  $rsptav = $consulta->totalventahoy();
  $regv=$rsptav->fetch_object();
  $totalv=$regv->total_venta;

  $rsptav = $consulta->cantidadclientes();
  $regv=$rsptav->fetch_object();
  $totalclientes=$regv->totalc;

  $rsptav = $consulta->cantidadproveedores();
  $regv=$rsptav->fetch_object();
  $totalproveedores=$regv->totalp;

  $rsptav = $consulta->cantidadarticulos();
  $regv=$rsptav->fetch_object();
  $totalarticulos=$regv->totalar;

  $rsptav = $consulta->totalstock();
  $regv=$rsptav->fetch_object();
  $totalstock=$regv->totalstock;
  $cap_almacen=3000;

  $rsptav = $consulta->cantidadcategorias();
  $regv=$rsptav->fetch_object();
  $totalcategorias=$regv->totalca;

  //obtener valores para cargar al grafico de barras
  $compras10 = $consulta->comprasultimos_10dias();
  $fechasc='';
  $totalesc='';
  if (!empty($compras10)) {
  while ($regfechac=$compras10->fetch_object()) {
    $fechasc=$fechasc.'"'.$regfechac->fecha.'",';
    $totalesc=$totalesc.$regfechac->total.',';
  }
  //quitamos la ultima coma
  $fechasc=substr($fechasc, 0, -1);
  $totalesc=substr($totalesc, 0,-1);
}

    //obtener valores para cargar al grafico de barras
  $ventas12 = $consulta->ventasultimos_12meses ();
  $fechasv='';
  $totalesv='';
  if (!empty($ventas12)) {
  while ($regfechav=$ventas12->fetch_object()) {
    $fechasv=$fechasv.'"'.$regfechav->fecha.'",';
    $totalesv=$totalesv.$regfechav->total.',';
  }
  //quitamos la ultima coma
  $fechasv=substr($fechasv, 0, -1);
  $totalesv=substr($totalesv, 0,-1);
}
 ?>
    <div class="content-wrapper">
    <!-- Main content -->
    <section class="content">

      <!-- Default box -->
      <div class="row">
        <div class="col-md-12">
      <div class="box">
<div class="panel-body">

<?php 

  if (empty($regn)) {
    $smoneda='M';
?>
          <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="alert alert-danger alert-dismissible text-center small-box">
              <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
               <div class="inner">
                  <h4 style="font-size: 17px;">
                    <i class="fa fa-warning"></i> Alert!
                  </h4>
                  <h4>Configura los datos de tu Empresa</h4>
               </div>
              <a href="negocio.php" class="small-box-footer">Configurar <i class="fa fa-arrow-circle-right"></i></a>
            </div>

          </div>
<?php
  }else{
    $nombrenegocio=$regn->nombre;
    $smoneda=$regn->simbolo;
    $logo=$regn->logo;
?>
  <div class="box-header with-border text-center" id="datos_negocio">
    <?php echo "<img src='../files/negocio/".$logo."' height='50px' width='50px' alt=''>" ; ?>
  <h1 class="box-title"><?php echo $nombrenegocio; ?></h1>
</div>
 <?php         
  };

 ?>

<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
  <div class="small-box bg-yellow">
    <div class="inner">
      <h4 style="font-size: 20px;">
        <strong><?php echo $smoneda.' '.$totalc; ?> </strong>
      </h4>
      <p>Compras</p>
    </div>
    <div class="icon">
      <i class="fa fa-cart-plus" aria-hidden="true"></i>
    </div>
    <a href="ingreso.php" class="small-box-footer">Compras <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
  <div class="small-box bg-green">
    <div class="inner">
      <h4 style="font-size: 20px;">
        <strong><?php echo $smoneda.' '.$totalv; ?> </strong>
      </h4>
      <p>Ventas</p>
    </div>
    <div class="icon">
       <i class="fa fa-shopping-cart" aria-hidden="true"></i>
    </div>
    <a href="venta.php" class="small-box-footer">Ventas <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
  <div class="small-box bg-aqua">
    <div class="inner">
      <h4 style="font-size: 20px;">
        <strong><?php echo $totalclientes; ?> </strong>
      </h4>
      <p>Clientes</p>
    </div>
    <div class="icon">
       <i class="fa fa-user-plus" aria-hidden="true"></i>
    </div>
    <a href="cliente.php" class="small-box-footer">Clientes <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>

<div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
  <div class="small-box bg-red">
    <div class="inner">
      <h4 style="font-size: 20px;">
        <strong><?php echo $totalproveedores; ?> </strong>
      </h4>
      <p>Proveedores</p>
    </div>
    <div class="icon">
       <i class="fa fa-users" aria-hidden="true"></i>
    </div>
    <a href="proveedor.php" class="small-box-footer">Proveedores <i class="fa fa-arrow-circle-right"></i></a>
  </div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="info-box bg-aqua">
            <span class="info-box-icon"><i class="fa fa-files-o"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Categorias</span>
              <span class="info-box-number"><?php echo $totalcategorias; ?></span>

              <div class="progress">
                <?php  $porcentcate=(100*$totalcategorias)/100; ?>
                <?php echo '<div class="progress-bar" style="width: '.$porcentcate.'%"></div>'; ?>
              </div>
              <span class="progress-description">
                    <?php echo $porcentcate; ?>% total de categorias
                  </span>
            </div>
            <!-- /.info-box-content -->
          </div>
</div>

<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
<div class="info-box bg-green">
            <span class="info-box-icon"><i class="fa fa-th"></i></span>

            <div class="info-box-content">
              <span class="info-box-text">Articulos</span>
              <span class="info-box-number"><?php echo $totalarticulos; ?></span>

              <div class="progress">
                <?php  $porcentart=(100*$totalstock)/$cap_almacen; ?>
                <?php echo '<div class="progress-bar" style="width: '.$porcentart.'%"></div>'; ?>
                
              </div>
              <span class="progress-description">
                    <?php echo round($porcentart,2); ?>% de la capacidad de almacen
                  </span>
            </div>
            <!-- /.info-box-content -->
          </div>
</div>

</div>
<div class="panel-body">
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
  <div class="box box-primary">
    <div class="box-header with-border">
      Compras de los ultimos 10 dias
    </div>
    <div class="box-body">
      <canvas id="compras" width="400" height="300"></canvas>
    </div>
  </div>
</div>
<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
  <div class="box box-primary">
    <div class="box-header with-border">
      Ventas de los ultimos 12 meses
    </div>
    <div class="box-body">
      <canvas id="ventas" width="400" height="300"></canvas>
    </div>
  </div>
</div>
</div>
<!--fin centro-->
      </div>
      </div>
      </div>
      <!-- /.box -->

    </section>
    <!-- /.content -->
  </div>
<?php 
}else{
 require 'noacceso.php'; 
}

require 'footer.php';
 ?>
 <script src="../public/js/Chart.bundle.min.js"></script>
 <script src="../public/js/Chart.min.js"></script>
 <script>
var ctx = document.getElementById("compras").getContext('2d');
var compras = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo $fechasc ?>],
        datasets: [{
            label: '# Compras en <?php echo $smoneda ?> de los últimos 10 dias',
            data: [<?php echo $totalesc ?>],
            backgroundColor: [
                '#FF9F33',
                '#FF4242',
                '#50A6F8',
                '#F8EB50',
                '#B8E656',
                '#805BE3 ',
                '#FF9F33',
                '#FF9F33',
                '#FF4242',
                '#50A6F8'
            ],
            borderColor: [
                '#FF9F33',
                '#FF4242',
                '#50A6F8',
                '#F8EB50',
                '#B8E656',
                '#805BE3 ',
                '#FF9F33',
                '#FF9F33',
                '#FF4242',
                '#50A6F8'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
var ctx = document.getElementById("ventas").getContext('2d');
var ventas = new Chart(ctx, {
    type: 'bar',
    data: {
        labels: [<?php echo $fechasv ?>],
        datasets: [{
            label: '# Ventas en <?php echo $smoneda ?> de los últimos 12 meses',
            data: [<?php echo $totalesv ?>],
            backgroundColor: [
                '#FF9F33',
                '#FF4242',
                '#50A6F8',
                '#F8EB50',
                '#B8E656',
                '#805BE3 ',
                '#FF9F33',
                '#FF9F33',
                '#FF4242',
                '#50A6F8',
                '#F8EB50',
                '#B8E656'
            ],
            borderColor: [
                '#FF9F33',
                '#FF4242',
                '#50A6F8',
                '#F8EB50',
                '#B8E656',
                '#805BE3 ',
                '#FF9F33',
                '#FF9F33',
                '#FF4242',
                '#50A6F8',
                '#F8EB50',
                '#B8E656'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
</script>
 <?php 
}

ob_end_flush();
  ?>

