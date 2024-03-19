<?php


class clientesModel extends Model
{

    //Método get
    //Consulta SELECT a la tabla clientes
    public function get()
    {
        try {
            $sql = "SELECT 
                id,
                concat_ws(', ', apellidos, nombre) cliente,
                telefono,
                ciudad,
                dni,
                email
            FROM 
                clientes
            ORDER BY id;";

            //Conectamos con la base de datos
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
    //Permite ejecutar INSERT en la tabla clientes
    public function create(classCliente $cliente)
    {
        try {
            $sql = "INSERT INTO 
                        clientes 
                        (
                            nombre, 
                            apellidos, 
                            email, 
                            telefono, 
                            ciudad, 
                            dni
                        ) 
                        VALUES 
                        ( 
                            :nombre,
                            :apellidos,
                            :email,
                            :telefono,
                            :ciudad,
                            :dni )";

            //Conectamos con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(":nombre", $cliente->nombre, PDO::PARAM_STR, 30);
            $pdoSt->bindParam(":apellidos", $cliente->apellidos, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(":email", $cliente->email, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(":telefono", $cliente->telefono, PDO::PARAM_STR, 9);
            $pdoSt->bindParam(":ciudad", $cliente->ciudad, PDO::PARAM_STR, 30);
            $pdoSt->bindParam(":dni", $cliente->dni, PDO::PARAM_STR, 9);

            //Ejecutamos 
            $pdoSt->execute();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método delete
    //Permite ejecutar comando DELETE en la tabla clientes
    public function delete($id)
    {
        try {
            $sql = "DELETE FROM clientes WHERE id = :id; ";

            //Conectamos con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(":id", $id, PDO::PARAM_INT);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos los datos
            return $pdoSt;
        } catch (PDOException $error) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método getCliente
    //Obtiene los detalles de un cliente a partir del id
    public function getCliente($id)
    {
        try {
            $sql = "SELECT     
                        id,
                        apellidos,
                        nombre,
                        telefono,
                        ciudad,
                        dni,
                        email
                    FROM  
                        clientes  
                    WHERE
                        id = :id";

            //Conectamos con la base de datos
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
            return $pdoSt->fetch();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método update
    //Actuliza los detalles de un cliente una vez editados en el formuliario
    public function update(classCliente $cliente, $id)
    {
        try {
            $sql = "UPDATE clientes
                    SET
                        apellidos=:apellidos,
                        nombre=:nombre,
                        telefono=:telefono,
                        ciudad=:ciudad,
                        dni=:dni,
                        email=:email,
                        update_at = now()
                    WHERE
                        id=:id
                    LIMIT 1";

            //Conectamos con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(":nombre", $cliente->nombre, PDO::PARAM_STR, 30);
            $pdoSt->bindParam(":apellidos", $cliente->apellidos, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(":email", $cliente->email, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(":telefono", $cliente->telefono, PDO::PARAM_STR, 9);
            $pdoSt->bindParam(":ciudad", $cliente->ciudad, PDO::PARAM_STR, 30);
            $pdoSt->bindParam(":dni", $cliente->dni, PDO::PARAM_STR, 9);
            $pdoSt->bindParam(":id", $id, PDO::PARAM_INT);

            //Ejecutamos
            $pdoSt->execute();
        } catch (PDOException $error) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }



    //Método update
    //Permite ordenar la tabla de cliente por cualquiera de las columnas del main
    //El criterio de ordenación se establec mediante el número de la columna del select
    public function order(int $criterio)
    {
        try {
            $sql = "SELECT 
                        id,
                        concat_ws(', ', apellidos, nombre) cliente,
                        telefono,
                        ciudad,
                        dni,
                        email
                    FROM 
                        clientes 
                    ORDER BY
                        :criterio";

            //Conectamos con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(":criterio", $criterio, PDO::PARAM_INT);

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
    //Permite filtar la tabla clientes a partir de una expresión de búsqueda
    public function filter($expresion)
    {
        try {
            $sql = "SELECT 
                        id,
                        concat_ws(', ', apellidos, nombre) cliente,
                        telefono,
                        ciudad,
                        dni,
                        email
                    FROM 
                        clientes 
                    WHERE 
                        concat_ws(  
                                    ' ',
                                    id,
                                    apellidos,
                                    nombre,
                                    telefono,
                                    ciudad,
                                    dni,
                                    email
                                )
                        LIKE 
                                :expresion
                    
                    ORDER BY id ASC";

            //Conectamos con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Enlazamos parámetros con variable
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

    //Método validateUniqueEmail
    //Validación de email único
    public function validateUniqueEmail($email)
    {
        try {
            $sql = "SELECT * FROM clientes 
                    WHERE email = :email";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdost = $conexion->prepare($sql);

            //Enlazamos parámetros con variable
            $pdost->bindParam(':email', $email, PDO::PARAM_STR);

            //Ejecutamos
            $pdost->execute();

            //Si el número de filas encontradas es distinto de 0, significa que ya existe, por lo que no es único
            if ($pdost->rowCount() != 0) {
                return false;
            }

            //De lo contrario, es correcto
            return true;
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método validateDNI
    //Validación de dni único
    public function validateDNI($dni)
    {
        try {
            $sql = "SELECT * FROM clientes 
                     WHERE dni = :dni";


            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdost = $conexion->prepare($sql);

            //Enlazamos parámetros con variable
            $pdost->bindParam(':dni', $dni, PDO::PARAM_STR);

            //Ejecutamos
            $pdost->execute();

            //Si el número de filas encontradas es distinto de 0, significa que ya existe, por lo que no es válido
            if ($pdost->rowCount() != 0) {
                return false;
            }

            //De lo contrario, es correcto
            return true;
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método getCSV
    //Pillamos los datos del CSV
    function getCSV()
    {

        try {
            $sql = "SELECT 
                        clientes.id,
                        clientes.apellidos,
                        clientes.nombre,
                        clientes.email,
                        clientes.telefono,
                        clientes.ciudad,
                        clientes.dni
                    FROM
                        clientes
                    ORDER BY 
                        id";

            //Conectamos con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdost = $conexion->prepare($sql);

            //Establecemos tipo fetch
            $pdost->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos 
            $pdost->execute();

            //devuelvo objeto pdostatement
            return $pdost;
        } catch (PDOException $e) {

            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método getCuentasDelCliente
    //Pillamos las cuentas del cliente a partir del id
    public function getCuentasDelCliente($idCliente)
    {
        try {
            $sql = "SELECT 
                id,
                num_cuenta,
                id_cliente,
                fecha_alta,
                fecha_ul_mov,
                num_movtos,
                saldo
            FROM 
                cuentas 
            WHERE 
                id_cliente = :idCliente";


            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Enlazamos parámetros con variable
            $pdoSt->bindParam(":idCliente", $idCliente, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos TODOS los datos que coinciden
            return $pdoSt->fetchAll();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método deleteCuentas
    //Permite ejecutar comando DELETE en la tabla Cuentas a partir del id
    public function deleteCuentas($idCuenta)
    {
        try {
            $sql = "DELETE FROM cuentas WHERE id = :idCuenta";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Enlazamos parámetros con variable
            $pdoSt->bindParam(":idCuenta", $idCuenta, PDO::PARAM_INT);

            //Ejecutamos
            $pdoSt->execute();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }
}
