<?php

require_once 'class/class.pdfMovimientos.php';


class Movimientos extends Controller
{
    //Método render
    //Método para generar la vista principal
    public function render($param = [])
    {

        //Inicio o continúo la sesión
        session_start();

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario No Autentificado";
            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['main']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
        } else {

            //Comprobar si existe el mensaje
            if (isset($_SESSION['mensaje'])) {
                $this->view->mensaje = $_SESSION['mensaje'];
                unset($_SESSION['mensaje']);
            }

            //Creo la propiedad title de la vista
            $this->view->title = "Tabla Movimientos";

            //Creo la propiedad movimientos dentro de la vista
            //Del modelo asignado al controlador ejecuto el método getMovimientos();
            $this->view->movimientos = $this->model->getMovimientos();
            $this->view->render("movimientos/main/index");
        }
    }

    //Método nuevo
    //Método para generar la vista del formulario para nuevo Movimiento
    function nuevo($param = [])
    {
        //Iniciamos o continuamos la sesión
        session_start();

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario No Autentificado";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['nuevo']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
        } else {

            //Creamos un objeto vacío
            $this->view->movimiento = new classMovimiento();

            //Comprobamos si existen errores
            if (isset($_SESSION['error'])) {
                //Añadimos a la vista el mensaje de error
                $this->view->error = $_SESSION['error'];

                //Autorellenamos el formulario
                $this->view->movimiento = unserialize($_SESSION['movimiento']);

                // Recuperamos el array con los errores
                $this->view->errores = $_SESSION['errores'];

                //Una vez usadas las variables de sesión, las liberamos
                unset($_SESSION['error']);
                unset($_SESSION['errores']);
                unset($_SESSION['movimientos']);
            }

            //Añadimos a la vista la propiedad title
            $this->view->title = "Añadir - Gestión Movimientos";
            //Para generar la lista select dinámica de cuentas
            $this->view->cuentas = $this->model->getCuentas();

            //Cargamos la vista del formulario para añadir una nueva movimientos
            $this->view->render("movimientos/nuevo/index");
        }
    }

    //Método create
    //Envía los detalles para crear una nuevo movimiento
    function create($param = [])
    {
        //Iniciar sesión
        session_start();

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario No Autentificado";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['nuevo']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
        } else {

            //1. Seguridad. Saneamos los datos del formulario

            //Si se introduce un campo vacío, se le otorga "nulo"
            $cuenta = isset($_POST['cuenta']) ? filter_var($_POST['cuenta'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $fecha_hora = isset($_POST['fecha_hora']) ? filter_var($_POST['fecha_hora'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $concepto = isset($_POST['concepto']) ? filter_var($_POST['concepto'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $tipo = isset($_POST['tipo']) ? filter_var($_POST['tipo'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $cantidad = isset($_POST['cantidad']) ? filter_var($_POST['cantidad'], FILTER_SANITIZE_SPECIAL_CHARS) : '';
            $saldo = isset($_POST['cuenta']) ? $this->model->getSaldoCuentaPorID($cuenta) : '';

            //2. Creamos el cliente con los datos saneados
            //Cargamos los datos del formulario
            $movimiento = new classMovimiento(
                null,
                $cuenta,
                $fecha_hora,
                $concepto,
                $tipo,
                $cantidad,
                $saldo
            );

            //3. Validación
            $errores = [];


            //Fecha_hora - No obligatorio. fecha hora actual en caso de null
            if (!isset($fecha_hora) || $fecha_hora == '0000-00-00 00:00') {
                $fecha_hora = date('Y-m-d\TH:i');
            }

            //Concepto - Valor obligatorio máximo 50 caracteres
            if (empty($concepto)) {
                $errores['concepto'] = 'El campo concepto es obligatorio';
            } else if (strlen($concepto) > 50) {
                $errores['concepto'] = 'El campo concepto debe ser inferior a 50 caracteres';
            }

            //Tipo - I o R - valor obligatorio. ha de tomar uno de estos valores ingreso o reintegro
            if (empty($tipo)) {
                $errores['tipo'] = 'El campo tipo es obligatorio';
            } else if (!in_array($tipo, ['I', 'R'])) {
                $errores['tipo'] = 'El campo tipo debe ser I o R';
            }

            //Cantidad - Ha de ser un valor tipo float. En caso de un reintegro la cantidad no podrá superar el saldo de la cuenta, en caso contrario, mostrará mensaje cantidad no disponible. 
            //Por otro lado la cantidad en caso de ser un reintegro se almacenará con un número negativo, de esta forma sumando todas las cantidades de los movimientos de una misma cuenta podré 
            //obtener el saldo.
            if (empty($cantidad)) {
                $errores['cantidad'] = 'El campo cantidad es obligatorio';
            } else if (!is_numeric($cantidad)) {
                $errores['cantidad'] = 'El campo cantidad debe ser un valor numérico';
            } else if ($tipo == 'R' && $cantidad > $saldo) {
                $errores['cantidad'] = 'Cantidad no disponible, es superior al saldo de la cuenta';
            }

            //4. Comprobar validación
            if (!empty($errores)) {
                //Errores de validación
                $_SESSION['movimiento'] = serialize($movimiento);
                $_SESSION['error'] = 'Formulario no validado';
                $_SESSION['errores'] = $errores;

                //Redireccionamos de nuevo al formulario
                header('location:' . URL . 'movimientos/nuevo/index');
            } else {

                //Actualizamos el saldo
                //Si el tipo fue ingreso, le sumamos la cantidad
                if ($tipo == 'I') {
                    $nuevoSaldo = $saldo + $cantidad;
                }
                //De lo contrario, se lo restamos
                else {
                    $nuevoSaldo = $saldo - $cantidad;
                }

                //Actualizamos el saldo en el objeto movimiento
                $movimiento->saldo = $nuevoSaldo;

                //Añadimos el registro a la tabla
                $this->model->create($movimiento);

                //Actualizamos el saldo de la cuenta
                $this->model->actualizarSaldoCuenta($cuenta, $nuevoSaldo);

                //Crearemos un mensaje, indicando que se ha realizado dicha acción
                $_SESSION['mensaje'] = "Se ha creado el movimiento bancaria correctamente.";

                // Redireccionamos a la vista principal de movimientos
                header("Location:" . URL . "movimientos");
            }
        }
    }

    //Método mostrar
    //Muestra los detalles de un movimiento en un formulario no editable
    function mostrar($param = [])
    {

        //Iniciar o continuar sesión
        session_start();

        //id de la cuenta
        $id = $param[0];

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario No Autentificado";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['mostrar']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
        } else {

            //Creo la propiedad title de la vista
            $this->view->title = "Formulario Mostrar Movimiento";

            //Creo la propiedad movimientos dentro de la vista
            $this->view->movimiento = $this->model->getMovimiento($id);

            //Creo la propiedad cuenta dentro de la vista
            $this->view->cuenta = $this->model->getCuenta($this->view->movimiento->cuenta);

            //Renderizo la vista
            $this->view->render("movimientos/mostrar/index");
        }
    }

    //Método ordenar
    //Permite ordenar la tabla cuenta a partir de alguna de las columnas de la tabla
    function ordenar($param = [])
    {
        //Inicio o continuo sesión
        session_start();

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario No Autentificado";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['ordenar']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
        } else {

            //Criterio de ordenamiento
            $criterio = $param[0];

            //Creo la propiedad title de la vista
            $this->view->title = "Tabla Movimientos";

            //Creo la propiedad movimientos dentro de la vista
            $this->view->movimientos = $this->model->order($criterio);

            //Renderizo la vista
            $this->view->render("movimientos/main/index");
        }
    }

    //Método buscar
    //Permite realizar una búsqueda en la tabla cuentas a partir de una expresión
    function buscar($param = [])
    {
        //Inicio o continuo sesión
        session_start();

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario No Autentificado";

            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['buscar']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
        } else {

            //Expresión de búsqueda
            $expresion = $_GET["expresion"];

            //Creo la propiedad title de la vista
            $this->view->title = "Tabla Movimientos";

            //Creo la propiedad movimientos dentro de la vista
            $this->view->movimientos = $this->model->filter($expresion);

            //Renderizo la vista
            $this->view->render("movimientos/main/index");
        }
    }

    //Método exportar
    //Exporta los datos a un archivo .CSV
    public function exportar()
    {
        //Inicio o continuo la sesión
        session_start();

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario no autentificado";
            header("location:" . URL . "login");
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['exportar']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
        }

        //Obtener todas los movimientos para exportar
        $movimientos = $this->model->getCSV()->fetchAll(PDO::FETCH_ASSOC);

        //Escribir los encabezados
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="movimientos.csv"');

        //Crear el archivo CSV
        $ficheroExport = fopen('php://output', 'w');

        //Iterar sobre los movimientos y escribir los datos en el archivo CSV
        foreach ($movimientos as $movimiento) {

            $fecha = date("Y-m-d H:i:s");

            $movimiento['create_at'] = $fecha;
            $movimiento['update_at'] = $fecha;

            $movimiento = array(
                'id_cuenta' => $movimiento['id_cuenta'],
                'fecha_hora' => $movimiento['fecha_hora'],
                'concepto' => $movimiento['concepto'],
                'tipo' => $movimiento['tipo'],
                'cantidad' => $movimiento['cantidad'],
                'saldo' => $movimiento['saldo'],
                'create_at' => $movimiento['create_at'],
                'update_at' => $movimiento['update_at']
            );

            //Escribir los datos en el archivo CSV
            fputcsv($ficheroExport, $movimiento, ';');
        }

        //Cierre del archivo CSV
        fclose($ficheroExport);
    }

    //Método importar
    //Importa datos a partir de un archivo .CSV
    public function importar()
    {
        //Iniciar o continuar la sesión
        session_start();

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario no autentificado";
            header("location:" . URL . "login");
            exit();
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['importar']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
            exit();
        }

        //Si el formulario ha sido enviado, procesamos los datos
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["archivo_csv"]) && $_FILES["archivo_csv"]["error"] == UPLOAD_ERR_OK) {
            $file = $_FILES["archivo_csv"]["tmp_name"];

            //Abrir el archivo CSV
            $handle = fopen($file, "r");

            //Leer el archivo CSV linea por linea
            if ($handle !== FALSE) {
                while (($data = fgetcsv($handle, 1000, ";")) !== FALSE) {
                    $id_cuenta = $data[0];
                    $fecha_hora = $data[1];
                    $concepto = $data[2];
                    $tipo = $data[3];
                    $cantidad = $data[4];
                    $saldo = $data[5];

                    // Si no existe, crear una nueva cuenta
                    $movimiento = new classMovimiento();
                    $movimiento->id_cuenta = $id_cuenta;
                    $movimiento->fecha_hora = $fecha_hora;
                    $movimiento->concepto = $concepto;
                    $movimiento->tipo = $tipo;
                    $movimiento->cantidad = $cantidad;
                    $movimiento->saldo = $saldo;

                    //Usamos create para meter la movimiento en la base de datos
                    $this->model->create($movimiento);
                }

                //Cierre del archivo CSV
                fclose($handle);

                //Generar mensasje y redireccionar al listado de movimientos
                $_SESSION['mensaje'] = "Importación realizada correctamente";
                header('location:' . URL . 'movimientos');
                exit();
            } else {
                $_SESSION['error'] = "Error con el archivo CSV";
                header('location:' . URL . 'movimientos');
                exit();
            }
        } else {
            $_SESSION['error'] = "Seleccione un archivo CSV";
            header('location:' . URL . 'movimientos');
            exit();
        }
    }

    //Método pdf
    //Genera un pdf con todas las cuentas
    function pdf()
    {
        //Iniciar o continuar la sesión
        session_start();

        //Comprobar si el usuario está identificado
        if (!isset($_SESSION['id'])) {
            $_SESSION['mensaje'] = "Usuario no autentificado";
            header("location:" . URL . "login");
            exit();
        } else if ((!in_array($_SESSION['id_rol'], $GLOBALS['movimientos']['pdf']))) {
            $_SESSION['mensaje'] = "Operación sin privilegios";
            header('location:' . URL . 'movimientos');
            exit();
        }

        //Obtenemos los movimientos con get
        $movimientos = $this->model->getMovimientos();

        //Instanciamos la clase pdfCuentas
        $pdf = new pdfMovimientos();

        //Escribimos en el PDF
        $pdf->contenido($movimientos);

        // Salida del PDF
        $pdf->Output();
    }
}
