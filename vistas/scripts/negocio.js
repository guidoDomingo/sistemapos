var tabla;

//funcion que se ejecuta al inicio
function init(){
   mostrarform(false);
   listar();
   mostrar_registros();

   $("#formulario").on("submit",function(e){
   	guardaryeditar(e);
   })

   //cargamos los items al celect categoria
   $("#logomuestra").hide();
}

function mostrar_registros(){
$.ajax({
url: '../ajax/negocio.php?op=mostrar_registros',
type:'get',
dataType:'json',
success: function(i){
	 registros=i;
	 if (registros==0) {
	$("#btnagregar").show();
	 }else{
	 	$("#btnagregar").hide();
	 }
}

	});}
//funcion limpiar
function limpiar(){
	$("#codigo").val("");
	$("#nombre").val("");
	$("#ndocumento").val("");
	$("#documento").val("");
	$("#direccion").val("");
	$("#direccion").val("");
	$("#telefono").val("");
	$("#email").val("");
	$("#logomuestra").attr("src","");
	$("#logoactual").val("");
	$("#pais").val("");
	$("#ciudad").val("");
	$("#nombre_impuesto").val("");
	$("#monto_impuesto").val("");
	$("#moneda").val("");
	$("#simbolo").val("");
	$("#id_negocio").val("");
	mostrar_registros();
}

//funcion mostrar formulario
function mostrarform(flag){
	limpiar();
	if(flag){
		$("#listadoregistros").hide();
		$("#formularioregistros").show();
		$("#btnGuardar").prop("disabled",false);
		$("#btnagregar").hide();
	}else{
		$("#listadoregistros").show();
		$("#formularioregistros").hide();
		$("#btnagregar").show();
	}
}
//cancelar form
function cancelarform(){
	limpiar();
	mostrarform(false);
}

//funcion listar
function listar(){
	tabla=$('#tbllistado').dataTable({
		"aProcessing": true,//activamos el procedimiento del datatable
		"aServerSide": true,//paginacion y filrado realizados por el server
		dom: 'Bfrtip',//definimos los elementos del control de la tabla
		buttons: [
                  'copyHtml5',
                  'excelHtml5',
                  'csvHtml5',
                  'pdf'
		],
		"ajax":
		{
			url:'../ajax/negocio.php?op=listar',
			type: "get",
			dataType : "json",
			error:function(e){
				console.log(e.responseText);
			}
		},
		"bDestroy":true,
		"iDisplayLength":5,//paginacion
		"order":[[0,"desc"]]//ordenar (columna, orden)
	}).DataTable();
}
//funcion para guardaryeditar
function guardaryeditar(e){
     e.preventDefault();//no se activara la accion predeterminada 
     $("#btnGuardar").prop("disabled",true);
     var formData=new FormData($("#formulario")[0]);

     $.ajax({
     	url: "../ajax/negocio.php?op=guardaryeditar",
     	type: "POST",
     	data: formData,
     	contentType: false,
     	processData: false,

     	success: function(datos){
     		bootbox.alert(datos);
     		mostrarform(false);
     		tabla.ajax.reload();
     	}
     });

     limpiar();
}

function mostrar(id_negocio){
	$.post("../ajax/negocio.php?op=mostrar",{id_negocio : id_negocio},
		function(data,status)
		{
			data=JSON.parse(data);
			mostrarform(true);
			$("#codigo").val(data.codigo);
			$("#nombre").val(data.nombre);
			$("#ndocumento").val(data.ndocumento);
			$("#documento").val(data.documento);
			$("#fecha_inicio").val(data.fecha_inicio);
			$("#fecha_fin").val(data.fecha_fin);
			$("#timbrado").val(data.timbrado);
			$("#direccion").val(data.direccion);
			$("#telefono").val(data.telefono);
			$("#email").val(data.email);
			$("#logomuestra").show();
			$("#logomuestra").attr("src","../files/negocio/"+data.logo);
			$("#logoactual").val(data.logo);
			$("#pais").val(data.pais);
			$("#ciudad").val(data.ciudad);
			$("#nombre_impuesto").val(data.nombre_impuesto);
			$("#monto_impuesto").val(data.monto_impuesto);
			$("#moneda").val(data.moneda);
			$("#simbolo").val(data.simbolo);
			$("#id_negocio").val(data.id_negocio);
		})
}


//funcion para desactivar
function desactivar(id_negocio){
	bootbox.confirm("¿Esta seguro de desactivar este dato?", function(result){
		if (result) {
			$.post("../ajax/negocio.php?op=desactivar", {id_negocio : id_negocio}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}

function activar(id_negocio){
	bootbox.confirm("¿Esta seguro de activar este dato?" , function(result){
		if (result) {
			$.post("../ajax/negocio.php?op=activar" , {id_negocio : id_negocio}, function(e){
				bootbox.alert(e);
				tabla.ajax.reload();
			});
		}
	})
}


init();