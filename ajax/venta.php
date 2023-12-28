<?php
require_once "../modelos/Venta.php";
if (strlen(session_id()) < 1)
	session_start();

$venta = new Venta();
//nuevo comentario  otra vez 
$idventa = isset($_POST["idventa"]) ? limpiarCadena($_POST["idventa"]) : "";
$idcliente = isset($_POST["idcliente"]) ? limpiarCadena($_POST["idcliente"]) : "";
$idusuario = $_SESSION["idusuario"];
$tipo_comprobante = isset($_POST["tipo_comprobante"]) ? limpiarCadena($_POST["tipo_comprobante"]) : "";
$serie_comprobante = isset($_POST["serie_comprobante"]) ? limpiarCadena($_POST["serie_comprobante"]) : "";
$num_comprobante = isset($_POST["num_comprobante"]) ? limpiarCadena($_POST["num_comprobante"]) : "";
$fecha_hora = isset($_POST["fecha_hora"]) ? limpiarCadena($_POST["fecha_hora"]) : "";
$impuesto = isset($_POST["impuesto"]) ? limpiarCadena($_POST["impuesto"]) : "";
$total_venta = isset($_POST["total_venta"]) ? limpiarCadena($_POST["total_venta"]) : "";





switch ($_GET["op"]) {
	case 'guardaryeditar':
		if (empty($idventa)) {
			$rspta = $venta->insertar($idcliente, $idusuario, $tipo_comprobante, $serie_comprobante, $num_comprobante, $fecha_hora, $impuesto, $total_venta, $_POST["idarticulo"], $_POST["cantidad"], $_POST["precio_venta"], $_POST["descuento"]);
			echo $rspta ? "Datos registrados correctamente" : "No se pudo registrar los datos";
		} else {
		}
		break;


	case 'anular':
		$rspta = $venta->anular($idventa);
		echo $rspta ? "Ingreso anulado correctamente" : "No se pudo anular el ingreso";
		break;

	case 'mostrar':
		$rspta = $venta->mostrar($idventa);
		echo json_encode($rspta);
		break;

		//_______________________________________________________________________________________________________
		//opcion para mostrar la numeracion y la serie_comprobante de la factura
	case 'mostrar_numero':

		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();
		//$tipo_comprobante='Factura';
		$tipo_comprobante = $_REQUEST["tipo_comprobante"];
		$rspta = $comprobantes->mostrar_numero($tipo_comprobante);
		while ($reg = $rspta->fetch_object()) {
			$numero_comp = (int)$reg->num_comprobante;
		}

		$numero_venta = $numero_comp;

		//mostramos el numero de comprobante de la tabla ventas
		$rspta = $venta->numero_venta($tipo_comprobante);
		while ($regv = $rspta->fetch_object()) {
			$numero_venta = (int)$regv->num_comprobante;
		}

		$new_numero = '';

		//validamos si el numero de comprobante de la venta ya llego al limite para ir a la siguiente numeracion
		if ($numero_venta == 9999999 or empty($numero_venta)) {
			(int)$new_numero = '0000001';
			echo json_encode($new_numero);
		} elseif ($numero_venta == 9999999) {
			(int)$new_numero = '0000001';
			echo json_encode($new_numero);
		} else {
			$suma_numero = $numero_venta + 1;
			echo json_encode($suma_numero);
		}

		break;

	case 'mostrar_serie':

		//mostrando el numero de factura de la tabla comprobantes
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();
		//$tipo_comprobante='Factura';
		$tipo_comprobante = $_REQUEST["tipo_comprobante"];
		$rspta = $comprobantes->mostrar_serie($tipo_comprobante);
		while ($reg = $rspta->fetch_object()) {
			$serie_comp = $reg->serie_comprobante;
			$num_comp = $reg->num_comprobante;
			//$letra_s=$reg->letra_serie;
		}
		$serie_com_comp = $serie_comp;
		$num_com_comp = (int)$num_comp;

		$serie = array(
			//"letra"=>$letra_s,
			"serie" => $serie_com_comp
		);
		echo json_encode($serie);

		break;

		//mostramos la serie de comprobante de la tabla ventas
		$rsptav = $venta->numero_serie($tipo_comprobante);
		$numeros = $serie_com_comp;
		$numeroco = $num_com_comp;

		while ($regv = $rsptav->fetch_object()) {
			$numeros = $regv->serie_comprobante;
			$numeroco = $regv->num_comprobante;
		}
		$ns = substr($numeros, -3);
		$nums = (int)$ns;
		$nuew_serie = 0;
		$numc = (int)$numeroco;
		if ($numc == 9999999 or empty($numeroco)) {
			$nuew_serie = $nums + 1;
			$serie = array(
				//"letra"=>$letra_s,
				"serie" => $nuew_serie
			);
			echo json_encode($serie);
		} else {
			$serie = array(
				//"letra"=>$letra_s,
				"serie" => $nums
			);
			echo json_encode($serie);
		}
		break;
		//opcion para mostrar la numeracion y la serie_comprobante de la boleta

		//______________________________________________________________________________________________


	case 'listarDetalle':
		require_once "../modelos/Negocio.php";
		$cnegocio = new Negocio();
		$rsptan = $cnegocio->listar();
		$regn = $rsptan->fetch_object();
		if (empty($regn)) {
			$smoneda = 'Simbolo de moneda';
		} else {
			$smoneda = $regn->simbolo;
			$nom_imp = $regn->nombre_impuesto;
		};
		//recibimos el idventa
		$id = $_GET['id'];

		$rspta = $venta->listarDetalle($id);
		$total = 0;

		echo ' <thead style="background-color:#A9D0F5">
        <th>Opciones</th>
        <th>Articulo</th>
        <th>Cantidad</th>
        <th>Precio Venta</th>
        <th>Descuento</th>
        <th>Subtotal</th>
       </thead>';
		while ($reg = $rspta->fetch_object()) {
			echo '<tr class="filas">
			<td></td>
			<td>' . $reg->nombre . '</td>
			<td>' . $reg->cantidad . '</td> 
			<td>' . $reg->precio_venta . '</td>
			<td>' . $reg->descuento . '</td>
			<td>' . $reg->subtotal . '</td></tr>';
			$total = $total + ($reg->precio_venta * $reg->cantidad - $reg->descuento);
			$t_venta = $reg->total_venta;
			$imp = $reg->impuesto;
			$most_igv = $t_venta - $total;
		}
		echo '<tfoot>
        <th><span>SubTotal</span><br><span id="valor_impuestoc">' . $nom_imp . ' ' . $imp . ' %</span><br><span>TOTAL</span></th>
         <th></th>
         <th></th>
         <th></th>
         <th></th>
         <th><span id="total">' . $smoneda . ' ' . number_format((float)$total, 2, '.', '') . '</span><br><span id="most_imp">' . $smoneda . ' ' . number_format((float)$most_igv, 2, '.', '') . '</span><br><span id="most_total" maxlength="4">' . $smoneda . ' ' . $t_venta . '</span></th>
       </tfoot>';
		break;

	case 'listar':
		$rspta = $venta->listar();
		$data = array();

		while ($reg = $rspta->fetch_object()) {

			$url = '../reportes/exFactura.php?id=';
			$urlticket = '../reportes/exTicket.php?id=';

			$data[] = array(
				"0" => (($reg->estado == 'Aceptado') ? '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>' . ' ' . '<button class="btn btn-danger btn-xs" onclick="anular(' . $reg->idventa . ')"><i class="fa fa-close"></i></button>' : '<button class="btn btn-warning btn-xs" onclick="mostrar(' . $reg->idventa . ')"><i class="fa fa-eye"></i></button>') .
					'<a target="_blank" href="' . $url . $reg->idventa . '"> <button class="btn btn-info btn-xs"><i class="fa fa-file"> FAC</i></button></a>'.
					'<a target="_blank" href="' . $urlticket . $reg->idventa . '"> <button class="btn btn-info btn-xs"><i class="fa fa-file"> TICKET</i></button></a>',
				"1" => $reg->fecha,
				"2" => $reg->cliente,
				"3" => $reg->usuario,
				"4" => $reg->tipo_comprobante,
				"5" => $reg->serie_comprobante . '-' . $reg->num_comprobante,
				"6" => $reg->total_venta,
				"7" => ($reg->estado == 'Aceptado') ? '<span class="label bg-green">Aceptado</span>' : '<span class="label bg-red">Anulado</span>'
			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);
		break;

	case 'selectCliente':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarc();
		echo '<option value="">Seleccione...</option>';
		while ($reg = $rspta->fetch_object()) {

			echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
		}
		break;

	case 'listarArticulos':
		require_once "../modelos/Articulo.php";
		$articulo = new Articulo();

		$rspta = $articulo->listarActivosVenta();
		$data = array();

		while ($reg = $rspta->fetch_object()) {
			$data[] = array(
				"0" => '<button class="btn btn-warning" onclick="agregarDetalle(' . $reg->idarticulo . ',\'' . $reg->nombre . '\',' . $reg->precio_venta . ',' . $reg->stock . ')"><span class="fa fa-plus"></span></button>',
				"1" => $reg->nombre,
				"2" => $reg->categoria,
				"3" => $reg->codigo,
				"4" => $reg->stock,
				"5" => $reg->precio_venta,
				"6" => "<img src='../files/articulos/" . $reg->imagen . "' height='50px' width='50px'>"

			);
		}
		$results = array(
			"sEcho" => 1, //info para datatables
			"iTotalRecords" => count($data), //enviamos el total de registros al datatable
			"iTotalDisplayRecords" => count($data), //enviamos el total de registros a visualizar
			"aaData" => $data
		);
		echo json_encode($results);

		break;
	case 'selectComprobante':
		require_once "../modelos/Comprobantes.php";
		$comprobantes = new Comprobantes();

		$rspta = $comprobantes->select();
		echo '<option value="">Seleccione...</option>';
		while ($reg = $rspta->fetch_object()) {
			echo '<option value=' . $reg->nombre . '>' . $reg->nombre . '</option>';
		}
		break;
}
