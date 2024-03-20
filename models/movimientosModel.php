<?php

class movimientosModel extends Model
{

    //Método getMovimientos
    //Consulta SELECT sobre la tabla movimientos
    public function getMovimientos()
    {
        try {
            $sql = "SELECT 
                movimientos.id,
                cuentas.num_cuenta as cuenta,
                movimientos.fecha_hora,
                movimientos.concepto,
                movimientos.tipo,
                movimientos.cantidad,
                movimientos.saldo
                FROM movimientos INNER JOIN cuentas ON movimientos.id_cuenta = cuentas.id ORDER BY movimientos.id
            ";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos los datos
            return $pdoSt;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método create
    //Ejecuta INSERT sobre la tabla movimientos
    public function create($movimiento)
    {
        try {
            $sql = "INSERT INTO 
                        movimientos (
                                    id_cuenta,
                                    fecha_hora,
                                    concepto,
                                    tipo,
                                    cantidad,
                                    saldo
                                ) VALUES ( 
                                    :id_cuenta,
                                    :fecha_hora,
                                    :concepto,
                                    :tipo,
                                    :cantidad,
                                    :saldo
                                )";


            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Bindeamos parametros
            $pdoSt->bindParam(":id_cuenta", $movimiento->id_cuenta, PDO::PARAM_INT);
            $pdoSt->bindParam(":fecha_hora", $movimiento->fecha_hora);
            $pdoSt->bindParam(":concepto", $movimiento->concepto, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(":tipo", $movimiento->tipo, PDO::PARAM_STR);
            $pdoSt->bindParam(":cantidad", $movimiento->cantidad, PDO::PARAM_STR);
            $pdoSt->bindParam(":saldo", $movimiento->saldo, PDO::PARAM_STR);

            //Ejecutamos
            $pdoSt->execute();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método getCuentas
    //Consulta SELECT sobre la tabla Cuentas
    public function getCuentas()
    {
        try {
            $sql = "SELECT * from cuentas";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos los datos
            return $pdoSt;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método getSaldoCuentaPorID
    //Permite obtener el saldo de una cuenta a partir del id
    public function getSaldoCuentaPorID($id)
    {
        try {
            $sql = "SELECT saldo FROM cuentas WHERE id = :id";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(":id", $id, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos los datos
            return $pdoSt->fetchColumn();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método actualizarSaldoCuenta
    //Permite actualizar el saldo de una cuenta a partir del id y el nuevo saldo
    public function actualizarSaldoCuenta($id, $nuevoSaldo)
    {
        try {
            $sql = "UPDATE cuentas SET saldo = :nuevoSaldo WHERE id = :id";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(":id", $id, PDO::PARAM_INT);
            $pdoSt->bindParam(":nuevoSaldo", $nuevoSaldo, PDO::PARAM_INT);

            //Ejecutamos
            $pdoSt->execute();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método getMovimiento
    //Permite obtener los detalles de un movimiento a partir del id
    public function getMovimiento($id)
    {
        try {
            $sql = "SELECT 
            movimientos.id,
            cuentas.num_cuenta as cuenta,
            movimientos.fecha_hora,
            movimientos.concepto,
            movimientos.tipo,
            movimientos.cantidad,
            movimientos.saldo
            FROM movimientos INNER JOIN cuentas ON movimientos.id_cuenta = cuentas.id WHeRE movimientos.id = :id
        ";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(':id', $id, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos los datos
            return $pdoSt->fetch();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }


    //Método getCuenta
    //Permite obtener los detalles de una cuenta a partir del id
    public function getCuenta($id)
    {
        try {
            $sql = "SELECT 
                        cuentas.id,
                        cuentas.num_cuenta,
                        cuentas.id_cliente,
                        cuentas.fecha_alta,
                        cuentas.fecha_ul_mov,
                        cuentas.num_movtos,
                        cuentas.saldo
                    FROM 
                        cuentas
                    WHERE
                        id=:id;";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(':id', $id, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos los datos
            return $pdoSt->fetch();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }


    //Método order
    //Permite ordenar los datos
    public function order(int $criterio)
    {
        try {
            $sql = "SELECT 
                movimientos.id,
                cuentas.num_cuenta as cuenta,
                movimientos.fecha_hora,
                movimientos.concepto,
                movimientos.tipo,
                movimientos.cantidad,
                movimientos.saldo
                FROM movimientos INNER JOIN cuentas ON movimientos.id_cuenta = cuentas.id ORDER BY :criterio
            ";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(':criterio', $criterio, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos los datos
            return $pdoSt;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método filter
    //Permite filtrar los datos
    public function filter($expresion)
    {
        try {
            $sql = "SELECT 
            movimientos.id,
            cuentas.num_cuenta as cuenta,
            movimientos.fecha_hora,
            movimientos.concepto,
            movimientos.tipo,
            movimientos.cantidad,
            movimientos.saldo
            FROM movimientos INNER JOIN cuentas ON movimientos.id_cuenta = cuentas.id 
            WHERE concat_ws(' ',             
                movimientos.id,
                cuentas.num_cuenta,
                movimientos.fecha_hora,
                movimientos.concepto,
                movimientos.tipo,
                movimientos.cantidad,
                movimientos.saldo) LIKE :expresion";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $expresion = "%" . $expresion . "%";
            $pdoSt->bindValue(':expresion', $expresion, PDO::PARAM_STR);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos los datos
            return $pdoSt;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    public function getCSV()
    {
        try {
            $sql = "SELECT * from movimientos ORDER BY id";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdost = $conexion->prepare($sql);

            //Establecemos tipo fetch
            $pdost->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos 
            $pdost->execute();

            //Retornamos los datos
            return $pdost;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }
}
    