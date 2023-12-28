<?php
//activamos almacenamiento en el buffer
ob_start();
session_start();
if (!isset($_SESSION['nombre'])) {
    header("Location: login.html");
} else {


    require 'header.php';

    if ($_SESSION['ventas'] == 1) {
        require_once "../modelos/Negocio.php";
        $cnegocio = new Negocio();
        $rsptan = $cnegocio->listar();
        $regn = $rsptan->fetch_object();
        if (empty($regn)) {
            $smoneda = '';
            $tipo_impuesto = '';
            $nombrenegocio = 'Configurar datos de su Empresa';
        } else {
            $smoneda = $regn->simbolo;
            $tipo_impuesto = $regn->nombre_impuesto;
            $nombrenegocio = $regn->nombre;
        };
?>
        <div class="content-wrapper">
            <!-- Main content -->
            <section class="content">

                <!-- Default box -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="box">
                            <div class="box-header with-border">
                                <h1 class="box-title">Ventas <button class="btn btn-success" id="btnagregar" onclick="mostrarform(true)"><i class="fa fa-plus-circle"></i>Agregar</button></h1>
                                <div class="box-tools pull-right">

                                </div>
                            </div>
                            <!--box-header-->
                            <!--centro-->
                            <div class="panel-body table-responsive" id="listadoregistros">
                                <table id="tbllistado" class="table table-striped table-bordered table-condensed table-hover">
                                    <thead>
                                        <th>Opciones</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Usuario</th>
                                        <th>Documento</th>
                                        <th>Número</th>
                                        <th>Total Venta</th>
                                        <th>Estado</th>
                                    </thead>
                                    <tbody>
                                    </tbody>
                                    <tfoot>
                                        <th>Opciones</th>
                                        <th>Fecha</th>
                                        <th>Cliente</th>
                                        <th>Usuario</th>
                                        <th>Documento</th>
                                        <th>Número</th>
                                        <th>Total Venta</th>
                                        <th>Estado</th>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="panel-body" id="formularioregistros">
                                <form action="" name="formulario" id="formulario" method="POST">
                                    <div class="form-group col-lg-8 col-md-8 col-xs-12">
                                        <label for="">Cliente(*):</label>
                                        <input class="form-control" type="hidden" name="idventa" id="idventa">
                                        <select name="idcliente" id="idcliente" class="form-control selectpicker" data-live-search="true" required>


                                        </select>
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4 col-xs-12">
                                        <label>Fecha(*):</label>
                                        <div class="input-group date">
                                            <div class="input-group-addon">
                                                <i class="fa fa-calendar"></i>
                                            </div>
                                            <input class="form-control pull-right" type="date" name="fecha_hora" id="fecha_hora" required>
                                        </div>
                                        <!-- /.input group -->
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4 col-xs-12">
                                        <label for="">Comprobante(*):</label>
                                        <select onchange="ShowComprobante()" name="tipo_comprobante" id="tipo_comprobante" class="form-control selectpicker" data-Live-search="true" required></select>
                                    </div>
                                    <div class="form-group col-lg-2 col-md-2 col-xs-12">
                                        <label for="">Serie: </label>
                                        <input class="form-control" type="text" name="serie_comprobante" id="serie_comprobante" maxlength="7" readonly>
                                    </div>
                                    <div class="form-group col-lg-2 col-md-2 col-xs-12">
                                        <label for="">Número: </label>
                                        <input class="form-control" type="text" name="num_comprobante" id="num_comprobante" maxlength="10">
                                    </div>
                                    <div class="form-group col-lg-4 col-md-4 col-xs-12">
                                        <label for="">Aplicar Impuesto: </label>
                                        <div class="input-group">
                                            <span class="input-group-addon bg-aqua">
                                                <input class="flat-red" type="checkbox" name="aplicar_impuesto" id="aplicar_impuesto">
                                            </span>
                                            <input class="form-control" type="text" name="impuesto" id="impuesto" readonly>
                                        </div>
                                        <!-- /input-group -->
                                    </div>
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <a data-toggle="modal" href="#myModal">
                                            <button id="btnAgregarArt" type="button" class="btn btn-primary"><span class="fa fa-plus"></span>Agregar Articulos</button>
                                        </a>

                                        <!-- Botón para abrir el modal -->
                                        <button class="btn btn-success" data-toggle="modal" data-target="#modalAgregarCliente">
                                            <i class="fa fa-plus"></i> Agregar Cliente
                                        </button>
                                    </div>

                                    <div class="form-group table-responsive col-lg-12 col-md-12 col-xs-12">
                                        <table id="detalles" class="table table-striped table-bordered table-condensed table-hover text-center">
                                            <thead class="bg-aqua">
                                                <th>Opciones</th>
                                                <th>Articulo</th>
                                                <th>Stock || Cantidad</th>
                                                <th>Precio Venta</th>
                                                <th>Descuento</th>
                                                <th>Subtotal</th>
                                            </thead>
                                            <tfoot style="background-color:#A9D0F5">
                                                <th><span>SubTotal</span><br><span id="valor_impuesto"><?php echo $tipo_impuesto; ?>
                                                        0.00</span><br><span>TOTAL</span></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th></th>
                                                <th><span id="total"><?php echo $smoneda; ?> 0.00</span><br><span id="most_imp" maxlength="4">0.00</span><br><span id="most_total">0.00</span><input type="hidden" step="0.01" name="total_venta" id="total_venta"></th>
                                            </tfoot>
                                            <tbody>

                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                        <button class="btn btn-primary" type="submit" id="btnGuardar"><i class="fa fa-save"></i>
                                            Guardar</button>
                                        <button class="btn btn-danger" onclick="cancelarform()" type="button" id="btnCancelar"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                                    </div>
                                </form>
                            </div>
                            <!--fin centro-->
                        </div>
                    </div>
                </div>
                <!-- /.box -->

            </section>
            <!-- /.content -->
        </div>

        <!--Modal-->
        <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Seleccione un Articulo</h4>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive">
                            <table id="tblarticulos" class="table table-striped table-bordered table-condensed table-hover">
                                <thead>
                                    <th>Opciones</th>
                                    <th>Nombre</th>
                                    <th>Categoria</th>
                                    <th>Código</th>
                                    <th>Stock</th>
                                    <th>Precio Venta</th>
                                    <th>Imagen</th>
                                </thead>
                                <tbody>

                                </tbody>
                                <tfoot>
                                    <th>Opciones</th>
                                    <th>Nombre</th>
                                    <th>Categoria</th>
                                    <th>Código</th>
                                    <th>Stock</th>
                                    <th>Precio Venta</th>
                                    <th>Imagen</th>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!--modal para ver la venta-->
        <div class="modal fade" id="getCodeModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                        <h4 class="modal-title">Vista de la venta</h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group col-lg-12 col-md-12 col-xs-12">
                            <label for="">Cliente(*):</label>
                            <input class="form-control" type="hidden" name="idventam" id="idventam">
                            <input class="form-control" type="text" name="cliente" id="cliente" maxlength="7" readonly>
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                            <label>Fecha(*):</label>
                            <div class="input-group date">
                                <div class="input-group-addon">
                                    <i class="fa fa-calendar"></i>
                                </div>
                                <input class="form-control pull-right" type="text" name="fecha_horam" id="fecha_horam" readonly>
                            </div>
                            <!-- /.input group -->
                        </div>
                        <div class="form-group col-lg-6 col-md-6 col-xs-12">
                            <label for="">Comprobante(*):</label>
                            <input class="form-control" type="text" name="tipo_comprobantem" id="tipo_comprobantem" maxlength="7" readonly>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-xs-12">
                            <label for="">Serie: </label>
                            <input class="form-control" type="text" name="serie_comprobantem" id="serie_comprobantem" maxlength="7" readonly>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-xs-12">
                            <label for="">Número: </label>
                            <input class="form-control" type="text" name="num_comprobantem" id="num_comprobantem" maxlength="10" readonly>
                        </div>
                        <div class="form-group col-lg-4 col-md-4 col-xs-12">
                            <label for="">Impuesto: </label>
                            <div class="input-group">
                                <input class="form-control" type="text" name="impuestom" id="impuestom" readonly>
                                <span class="input-group-addon">%</span>
                            </div>
                            <!-- /input-group -->
                        </div>
                        <div class="form-group table-responsive col-lg-12 col-md-12 col-xs-12">
                            <table id="detallesm" class="table table-striped table-bordered table-condensed table-hover">
                                <tbody>

                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="form-group col-lg-12 col-md-12 col-xs-12">

                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-default" type="button" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- agregar cliente -->

        <div class="modal fade" id="modalAgregarCliente" tabindex="-1" role="dialog" aria-labelledby="modalAgregarClienteLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="modalAgregarClienteLabel">Agregar Cliente</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <!-- Aquí va tu formulario -->
                        <form action="" name="formulariocliente" id="formulariocliente" method="POST">
                            <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                <label for="">Nombre(*)</label>
                                <input class="form-control" type="hidden" name="idpersona" id="idpersona">
                                <input class="form-control" type="hidden" name="tipo_persona" id="tipo_persona" value="Cliente">
                                <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Nombre del cliente" required>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                <label for="">Tipo Dcumento(*)</label>
                                <select class="form-control select-picker" name="tipo_documento" id="tipo_documento" required>
                                    <option value="DNI">DNI</option>
                                    <option value="RUC">RUC</option>
                                    <option value="CEDULA">CEDULA</option>
                                </select>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                <label for="">Número Documento(*)</label>
                                <input class="form-control" type="text" name="num_documento" id="num_documento" maxlength="20" placeholder="Número de Documento" required>
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                <label for="">Direccion</label>
                                <input class="form-control" type="text" name="direccion" id="direccion" maxlength="70" placeholder="Direccion">
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                <label for="">Telefono</label>
                                <input class="form-control" type="text" name="telefono" id="telefono" maxlength="20" placeholder="Número de Telefono">
                            </div>
                            <div class="form-group col-lg-6 col-md-6 col-xs-12">
                                <label for="">Email</label>
                                <input class="form-control" type="email" name="email" id="email" maxlength="50" placeholder="Email">
                            </div>
                            <div class="form-group col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <button class="btn btn-primary" type="submit" id="btnGuardarCliente"><i class="fa fa-save"></i> Guardar</button>

                                <button class="btn btn-danger" onclick="cancelarform()" type="button"><i class="fa fa-arrow-circle-left"></i> Cancelar</button>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                        <!-- Puedes agregar un botón de guardar aquí si lo deseas -->
                    </div>
                </div>
            </div>
        </div>


        <!-- fin Modal-->
    <?php
    } else {
        require 'noacceso.php';
    }

    require 'footer.php';
    ?>
    <script src="scripts/venta.js"></script>
<?php
}

ob_end_flush();
?>