var tabla;

//funcion que se ejecuta al inicio
function init() {
  mostrarform(false);
  mostrar_impuesto();
  nombre_impuesto();

  listar();

  $("#formulario").on("submit", function (e) {
    guardaryeditar(e);
  });

  //cargamos los items al select cliente
  $.post("../ajax/venta.php?op=selectCliente", function (r) {
    $("#idcliente").html(r);
    $("#idcliente").selectpicker("refresh");
  });

  //cargamos los items al celect comprobantes
  $.post("../ajax/venta.php?op=selectComprobante", function (c) {
    $("#tipo_comprobante").html(c);
    $("#tipo_comprobante").selectpicker("refresh");
  });
}

//funcion limpiar
function limpiar() {
  $("#idventa").val("");
  $("#idcliente").val("");
  $("#cliente").val("");
  $("#serie_comprobante").val("");
  $("#num_comprobante").val("");
  $("#impuesto").val("");

  $("#total_venta").val("");
  $(".filas").remove();
  $("#total").html("0");

  //obtenemos la fecha actual
  var now = new Date();
  var day = ("0" + now.getDate()).slice(-2);
  var month = ("0" + (now.getMonth() + 1)).slice(-2);
  var today = now.getFullYear() + "-" + month + "-" + day;
  $("#fecha_hora").val(today);

  //marcamos el primer tipo_documento
  $("#tipo_comprobante").selectpicker("refresh");
  $("#idcliente").selectpicker("refresh");
}

function ShowComprobante() {
  mostrar_impuesto();
  var tipo_comprobante = $("#tipo_comprobante").val();
  if (tipo_comprobante.length == 0) {
    $("#serie_comprobante").val("");
    $("#num_comprobante").val("");
  } else {
    serie_comp();
    numero_comp();
  }
}

//mostramos la serie del comprobante
function serie_comp() {
  var tipo_comprobante = $("#tipo_comprobante").val();
  //alert(tipo_comprobante);
  $.post(
    "../ajax/venta.php?op=mostrar_serie",
    { tipo_comprobante: tipo_comprobante },
    function (data, status) {
      data = JSON.parse(data);
      //alert(data.letra);
      $("#serie_comprobante").val(data.serie); // "0001"
    }
  );
}

//mostramos el numero de comprobante
function numero_comp() {
  var tipo_comprobante = $("#tipo_comprobante").val();
  $.ajax({
    url: "../ajax/venta.php?op=mostrar_numero",
    data: { tipo_comprobante: tipo_comprobante },
    type: "get",
    dataType: "json",
    success: function (d) {
      num_comp = d;
      $("#num_comprobante").val(("0000000" + num_comp).slice(-7)); // "0001"
      $("#nFacturas").html(("0000000" + num_comp).slice(-7)); // "0001"
    },
  });
}

no_aplica = 0;
function mostrar_impuesto() {
  $.ajax({
    url: "../ajax/negocio.php?op=mostrar_impuesto",
    type: "get",
    dataType: "json",
    success: function (i) {
      impuesto = i;
      sin_imp = 0;
      var checkbox = document.querySelector("#aplicar_impuesto");
      checkbox.addEventListener("change", verificarEstado, false);
      function verificarEstado(e) {
        if (e.target.checked) {
          $("#impuesto").val(impuesto);
          no_aplica = impuesto;
          calcularTotales();
          nombre_impuesto();
        } else {
          $("#impuesto").val(sin_imp);
          no_aplica = 0;
          calcularTotales();
          nombre_impuesto();
        }
      }
    },
  });
}

//declaramos variables necesarias para trabajar con las compras y sus detalles
var cont = 0;
var detalles = 0;
$("#btnGuardar").hide();
//$("#tipo_comprobante").change(marcarImpuesto);
function mostrar_impuesto() {
  $.ajax({
    url: "../ajax/negocio.php?op=mostrar_impuesto",
    type: "get",
    dataType: "json",
    success: function (i) {
      impuesto = i;
      sin_imp = 0;
      var checkbox = document.querySelector("#aplicar_impuesto");
      checkbox.addEventListener("change", verificarEstado, false);
      function verificarEstado(e) {
        if (e.target.checked) {
          $("#impuesto").val(impuesto);
          no_aplica = impuesto;
          calcularTotales();
          nombre_impuesto();
        } else {
          $("#impuesto").val(sin_imp);
          no_aplica = 0;
          calcularTotales();
          nombre_impuesto();
        }
      }
    },
  });
}

//_______________________________________________________________________________________________

//funcion mostrar formulario
function mostrarform(flag) {
  limpiar();
  if (flag) {
    $("#listadoregistros").hide();
    $("#formularioregistros").show();
    //$("#btnGuardar").prop("disabled",false);
    $("#btnagregar").hide();
    listarArticulos();

    $("#btnGuardar").hide();
    $("#btnCancelar").show();
    detalles = 0;
    $("#btnAgregarArt").show();
  } else {
    $("#listadoregistros").show();
    $("#formularioregistros").hide();
    $("#btnagregar").show();
  }
}

//cancelar form
function cancelarform() {
  limpiar();
  mostrarform(false);
  $("#aplicar_impuesto").show();
}

//funcion listar
function listar() {
  tabla = $("#tbllistado")
    .dataTable({
      aProcessing: true, //activamos el procedimiento del datatable
      aServerSide: true, //paginacion y filrado realizados por el server
      dom: "Bfrtip", //definimos los elementos del control de la tabla
      buttons: ["copyHtml5", "excelHtml5", "csvHtml5", "pdf"],
      ajax: {
        url: "../ajax/venta.php?op=listar",
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 10, //paginacion
      order: [[0, "desc"]], //ordenar (columna, orden)
    })
    .DataTable();
}

function listarArticulos() {
  tabla = $("#tblarticulos")
    .dataTable({
      aProcessing: true, //activamos el procedimiento del datatable
      aServerSide: true, //paginacion y filrado realizados por el server
      dom: "Bfrtip", //definimos los elementos del control de la tabla
      buttons: [],
      ajax: {
        url: "../ajax/venta.php?op=listarArticulos",
        type: "get",
        dataType: "json",
        error: function (e) {
          console.log(e.responseText);
        },
      },
      bDestroy: true,
      iDisplayLength: 5, //paginacion
      order: [[0, "desc"]], //ordenar (columna, orden)
    })
    .DataTable();
}
//funcion para guardaryeditar
function guardaryeditar(e) {
  e.preventDefault(); //no se activara la accion predeterminada
  //$("#btnGuardar").prop("disabled",true);
  var formData = new FormData($("#formulario")[0]);

  $.ajax({
    url: "../ajax/venta.php?op=guardaryeditar",
    type: "POST",
    data: formData,
    contentType: false,
    processData: false,

    success: function (datos) {
      bootbox.alert(datos);
      mostrarform(false);
      listar();
    },
  });

  limpiar();
}

function mostrar(idventa) {
  $("#getCodeModal").modal("show");
  $.post(
    "../ajax/venta.php?op=mostrar",
    { idventa: idventa },
    function (data, status) {
      data = JSON.parse(data);
      //mostrarform(true);

      $("#cliente").val(data.cliente);
      $("#tipo_comprobantem").val(data.tipo_comprobante);
      $("#serie_comprobantem").val(data.serie_comprobante);
      $("#num_comprobantem").val(data.num_comprobante);
      $("#fecha_horam").val(data.fecha);
      $("#impuestom").val(data.impuesto);
      $("#idventam").val(data.idventa);

      //ocultar y mostrar los botones
    }
  );
  $.post("../ajax/venta.php?op=listarDetalle&id=" + idventa, function (r) {
    $("#detallesm").html(r);
  });
}

//funcion para desactivar
function anular(idventa) {
  bootbox.confirm("¿Esta seguro de desactivar este dato?", function (result) {
    if (result) {
      $.post("../ajax/venta.php?op=anular", { idventa: idventa }, function (e) {
        bootbox.alert(e);
        tabla.ajax.reload();
      });
    }
  });
}
var numero_cantidad = 1;
function agregarDetalle(idarticulo, articulo, precio_venta, cantidad) {
  numero_cantidad = 1;
  var stock = cantidad;
  var descuento = 0;
  var letras_s = "Stock:";

  $("#numeros").html("000");
  if (idarticulo != "") {
    $(function () {
      $(".click").click(function (e) {
        e.preventDefault();
        var data = $(this).attr("name");
        numero_cantidad = data;
      });
    });
    var subtotal = cantidad * precio_venta;
    var fila =
      '<tr class="filas" id="fila' +
      cont +
      '">' +
      '<td><button type="button" class="btn btn-danger" onclick="eliminarDetalle(' +
      cont +
      ')">X</button></td>' +
      '<td><input type="hidden" name="idarticulo[]" value="' +
      idarticulo +
      '">' +
      articulo +
      '<td><span id="mensaje" name="' +
      stock +
      '" class="click bg-danger">' +
      letras_s +
      " " +
      stock +
      ' <i class="fa fa-arrow-circle-left"></i> ' +
      '<input type="number" onchange="subir(this.value,' +
      stock +
      ')" class="mostrar" name="cantidad[]" id="cantidad[]" value="' +
      numero_cantidad +
      '"></span></td>' +
      '<td><input type="number" step="0.01" onchange="modificarSubtotales()" name="precio_venta[]" id="precio_venta[]" value="' +
      precio_venta +
      '"></td>' +
      '<td><input type="number" step="0.01" onchange="modificarSubtotales()" name="descuento[]" value="' +
      descuento +
      '"></td>' +
      '<td><span id="subtotal' +
      cont +
      '" name="subtotal">' +
      subtotal +
      "</span></td>" +
      "</tr>";
    var product = null;
    var shelf = null;
    var status = null;

    //submit

    cont++;
    detalles++;
    $("#detalles").append(fila);
    modificarSubtotales();
  } else {
    bootbox.alert(
      "error al ingresar el detalle, revisar las datos del articulo "
    );
  }
}
//imprimimos el resultado en el componente con la clase "resultado"
//esta funcion valida la cantidad a vender con el stock
function subir(valor, stock) {
  var msj = "la cantidad supera al stock actual";
  valor = parseInt(valor);
  if (valor > stock) {
    bootbox.alert(valor + " " + msj + " " + stock);
    $("#btnGuardar").hide();
  } else {
    $("#btnGuardar").show();
    modificarSubtotales();
  }
  numero_cantidad = 1;
}

function modificarSubtotales() {
  var cant = document.getElementsByName("cantidad[]");
  var prev = document.getElementsByName("precio_venta[]");
  var desc = document.getElementsByName("descuento[]");
  var sub = document.getElementsByName("subtotal");

  for (var i = 0; i < cant.length; i++) {
    var inpV = cant[i];
    var inpP = prev[i];
    var inpS = sub[i];
    var des = desc[i];

    inpS.value = inpV.value * inpP.value - des.value;
    document.getElementsByName("subtotal")[i].innerHTML = inpS.value.toFixed(2);
  }

  calcularTotales();
}

function calcularTotales() {
  var sub = document.getElementsByName("subtotal");
  var total = 0.0;
  var simbolo = "";

  for (var i = 0; i < sub.length; i++) {
    total += document.getElementsByName("subtotal")[i].value;
    var igv = total * (no_aplica / 100);
    var total_monto = total + igv;
    var igv_dec = igv.toFixed(2);
  }
  $.ajax({
    url: "../ajax/negocio.php?op=mostrar_simbolo",
    type: "get",
    dataType: "json",
    success: function (sim) {
      simbolo = sim;
      $("#total").html(simbolo + " " + total.toFixed(2));
      $("#total_venta").val(total_monto.toFixed(2));
      $("#most_total").html(simbolo + total_monto.toFixed(2));
      $("#most_imp").html(simbolo + igv_dec);

      evaluar();
    },
  });
}
function nombre_impuesto() {
  $.ajax({
    url: "../ajax/negocio.php?op=nombre_impuesto",
    type: "get",
    dataType: "json",
    success: function (n) {
      nomp = n;
      var valor_impuesto = no_aplica;
      $("#valor_impuesto").html(nomp + " " + valor_impuesto + "%");
    },
  });
}

function evaluar() {
  if (detalles > 0) {
    $("#btnGuardar").show();
  } else {
    $("#btnGuardar").hide();
    cont = 0;
  }
}

function eliminarDetalle(indice) {
  $("#fila" + indice).remove();
  calcularTotales();
  detalles = detalles - 1;
}

init();
