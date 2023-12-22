<?php 
session_start();
require_once "../modelos/Negocio.php";

$negocio=new Negocio();

$id_negocio=isset($_POST["id_negocio"])? limpiarCadena($_POST["id_negocio"]):"";
$nombre=isset($_POST["nombre"])? limpiarCadena($_POST["nombre"]):"";
$ndocumento=isset($_POST["ndocumento"])? limpiarCadena($_POST["ndocumento"]):"";
$documento=isset($_POST["documento"])? limpiarCadena($_POST["documento"]):"";
$timbrado=isset($_POST["timbrado"])? limpiarCadena($_POST["timbrado"]):"";
$fecha_inicio=isset($_POST["fecha_inicio"])? limpiarCadena($_POST["fecha_inicio"]):"";
$fecha_fin=isset($_POST["fecha_fin"])? limpiarCadena($_POST["fecha_fin"]):"";
$direccion=isset($_POST["direccion"])? limpiarCadena($_POST["direccion"]):"";
$telefono=isset($_POST["telefono"])? limpiarCadena($_POST["telefono"]):"";
$email=isset($_POST["email"])? limpiarCadena($_POST["email"]):"";
$pais=isset($_POST["pais"])? limpiarCadena($_POST["pais"]):"";
$ciudad=isset($_POST["ciudad"])? limpiarCadena($_POST["ciudad"]):"";
$nombre_impuesto=isset($_POST["nombre_impuesto"])? limpiarCadena($_POST["nombre_impuesto"]):"";
$monto_impuesto=isset($_POST["monto_impuesto"])? limpiarCadena($_POST["monto_impuesto"]):"";
$moneda=isset($_POST["moneda"])? limpiarCadena($_POST["moneda"]):"";
$simbolo=isset($_POST["simbolo"])? limpiarCadena($_POST["simbolo"]):"";
$logo=isset($_POST["logo"])? limpiarCadena($_POST["logo"]):"";

switch ($_GET["op"]) {
	case 'guardaryeditar':

	if (!file_exists($_FILES['logo']['tmp_name'])|| !is_uploaded_file($_FILES['logo']['tmp_name'])) {
		$logo=$_POST["logoactual"];
	}else{
		$ext=explode(".", $_FILES["logo"]["name"]);
		if ($_FILES['logo']['type']=="image/jpg" || $_FILES['logo']['type']=="image/jpeg" || $_FILES['logo']['type']=="image/png") {
			$logo=round(microtime(true)).'.'. end($ext);
			move_uploaded_file($_FILES["logo"]["tmp_name"], "../files/negocio/".$logo);
		}
	}

	if (empty($id_negocio)) {
		$rspta=$negocio->insertar($nombre,$ndocumento,$documento,$timbrado,$fecha_inicio,$fecha_fin,$direccion,$telefono,$email,$logo,$pais,$ciudad,$nombre_impuesto,$monto_impuesto,$moneda,$simbolo);
		echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar todos los datos del negocio";
	}else{
		$rspta=$negocio->editar($id_negocio,$nombre,$ndocumento,$documento,$timbrado,$fecha_inicio,$fecha_fin,$direccion,$telefono,$email,$logo,$pais,$ciudad,$nombre_impuesto,$monto_impuesto,$moneda,$simbolo);
		echo $rspta ? "Datos actualizados correctamente" : "No se pudo actualizar los datos";
	}
	break;
	

	case 'desactivar':
	$rspta=$negocio->desactivar($id_negocio);
	echo $rspta ? "Datos desactivados correctamente" : "No se pudo desactivar los datos";
	break;

	case 'activar':
	$rspta=$negocio->activar($id_negocio);
	echo $rspta ? "Datos activados correctamente" : "No se pudo activar los datos";
	break;
	
	case 'mostrar':
	$rspta=$negocio->mostrar($id_negocio);
	echo json_encode($rspta);
	break;
case 'mostrar_registros':
		$rspta=$negocio->mostrar_registros();
		$data=Array();

		while ($reg=$rspta->fetch_object()) {
			$data[]=array(
            $numeroid=$reg->id_negocio
              );
		}
		$numeroid_negocio = (int)$numeroid;
		echo json_encode($numeroid_negocio);
		break;
	case 'mostrar_impuesto':
		$rspta=$negocio->mostrar_impuesto();
		$data=Array();

		while ($reg=$rspta->fetch_object()) {
			$data[]=array(
            $numeroimp=$reg->monto_impuesto

              );
		}
		$impuesto = (floatval($numeroimp));
		echo json_encode($impuesto);
		break;
		case 'nombre_impuesto':
		$rspta=$negocio->nombre_impuesto();
		$data=Array();

		while ($reg=$rspta->fetch_object()) {
			$data[]=array(
            $nombreimp=$reg->nombre_impuesto

              );
		}
		echo json_encode($nombreimp);
		break;
case 'mostrar_simbolo':
		$rspta=$negocio->mostrar_simbolo();
		$data=Array();

		while ($reg=$rspta->fetch_object()) {
			$data[]=array(
            $simbolo=$reg->simbolo
              );
		}
		echo json_encode($simbolo);
		break;
	case 'listar':
	$rspta=$negocio->listar();
	$data=Array();

	while ($reg=$rspta->fetch_object()) {
		$data[]=array(
			"0"=>($reg->condicion)?'<button class="btn btn-warning btn-xs" id="btn_lista" onclick="mostrar('.$reg->id_negocio.')"><i class="fa fa-pencil"></i></button>'.' '.'<button class="btn btn-danger btn-xs" onclick="desactivar('.$reg->id_negocio.')"><i class="fa fa-close"></i></button>':'<button class="btn btn-warning btn-xs" onclick="mostrar('.$reg->id_negocio.')"><i class="fa fa-pencil"></i></button>'.' '.'<button class="btn btn-primary btn-xs" onclick="activar('.$reg->id_negocio.')"><i class="fa fa-check"></i></button>',
			"1"=>"<img src='../files/negocio/".$reg->logo."' height='50px' width='50px'>",
			"2"=>$reg->nombre,
			"3"=>$reg->ndocumento.'-'.$reg->documento,
			"4"=>$reg->direccion,
			"5"=>$reg->telefono,
			"6"=>$reg->email,
			"7"=>$reg->ciudad.'-'.$reg->pais,
			"8"=>$reg->nombre_impuesto.' '.$reg->monto_impuesto.' %',
			"9"=>$reg->simbolo.'- '.$reg->moneda,
			"10"=>($reg->condicion)?'<span class="label bg-green">Activado</span>':'<span class="label bg-red">Desactivado</span>'
		);
	}

	$results=array(
             "sEcho"=>1,//info para datatables
             "iTotalRecords"=>count($data),//enviamos el total de registros al datatable
             "iTotalDisplayRecords"=>count($data),//enviamos el total de registros a visualizar
             "aaData"=>$data); 
	echo json_encode($results);
	break;
	
}
?>

