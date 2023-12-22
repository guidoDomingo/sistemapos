<?php 
//incluir la conexion de base de datos
require "../config/Conexion.php";
class Comprobantes{


	//implementamos nuestro constructor
public function __construct(){

}

//metodo insertar regiustro
public function insertar($nombre,$serie_comprobante,$num_comprobante){
	$sql="INSERT INTO comp_pago (nombre,serie_comprobante,num_comprobante,condicion) VALUES ('$nombre','$serie_comprobante','$num_comprobante','1')";
	return ejecutarConsulta($sql);
}

public function editar($id_comp_pago,$nombre,$serie_comprobante,$num_comprobante){
	$sql="UPDATE comp_pago SET nombre='$nombre',serie_comprobante='$serie_comprobante',num_comprobante='$num_comprobante' 
	WHERE id_comp_pago='$id_comp_pago'";
	return ejecutarConsulta($sql);
}
public function desactivar($id_comp_pago){
	$sql="UPDATE comp_pago SET condicion='0' WHERE id_comp_pago='$id_comp_pago'";
	return ejecutarConsulta($sql);
}
public function activar($id_comp_pago){
	$sql="UPDATE comp_pago SET condicion='1' WHERE id_comp_pago='$id_comp_pago'";
	return ejecutarConsulta($sql);
}

//metodo para mostrar registros
public function mostrar($id_comp_pago){
	$sql="SELECT * FROM comp_pago WHERE id_comp_pago='$id_comp_pago'";
	return ejecutarConsultaSimpleFila($sql);
}

//listar registros
public function listar(){
	$sql="SELECT * FROM comp_pago";
	return ejecutarConsulta($sql);
}
//listar y mostrar en selct
public function select(){
	$sql="SELECT * FROM comp_pago WHERE condicion=1";
	return ejecutarConsulta($sql);
}
public function mostrar_serie($tipo_comprobante){
	$sql="SELECT serie_comprobante, num_comprobante FROM comp_pago WHERE nombre='$tipo_comprobante'";
	return ejecutarConsulta($sql);
}
public function mostrar_numero($tipo_comprobante){
	$sql="SELECT num_comprobante FROM comp_pago WHERE nombre='$tipo_comprobante'";
	return ejecutarConsulta($sql); 
}

}

 ?>
