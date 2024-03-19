<?php

class usuariosModel extends Model
{
    //Método get
    //Consulta SELECT a la tabla clientes
    public function getUsers()
    {
        try {
            $sql = "SELECT * from users";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos
            return $pdoSt;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método getRoles
    //Consulta SELECT a la tabla roles
    public function getRoles()
    {
        try {
            $sql = "SELECT * from roles";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos
            return $pdoSt;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método create
    //Ejecuta INSERT sobre la tabla cuentas
    public function create($nombre, $email, $password, $id_rol)
    {
        try {

            //Encriptamos la password
            $password = password_hash($password, PASSWORD_BCRYPT);

            //Primero tenemos que crear el usuario
            $sql = "INSERT INTO users VALUES (
                null,
                :nombre,
                :email,
                :pass,
                default,
                default)";

            //Conectar con la base de datos
            $pdo = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $stmt = $pdo->prepare($sql);

            //Vinculamos los parámetros
            $stmt->bindParam(':nombre', $nombre, PDO::PARAM_STR, 50);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR, 50);
            $stmt->bindParam(':pass', $password, PDO::PARAM_STR, 60);

            //Ejecutamos
            $stmt->execute();

            //Guardamos en una variable el valor id de este último registro insertado
            $id_usuario = $pdo->lastInsertId();

            //Ahora asociamos el rol al usuario
            //Asignamos rol de registrado
            $sql = "INSERT INTO roles_users VALUES (
                null,
                :user_id,
                :role_id,
                default,
                default)";

            //Preparamos la consulta SQL para su ejecución
            $stmt = $pdo->prepare($sql);

            //Vinculamos los parámetros
            $stmt->bindParam(':user_id', $id_usuario, PDO::PARAM_INT);
            $stmt->bindParam(':role_id', $id_rol, PDO::PARAM_INT);

            //Ejecutamos
            $stmt->execute();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método isEmailUnique
    //Comprueba si un email está disponible
    public function isEmailUnique($email)
    {
        try {
            $sql = "SELECT COUNT(*) FROM users WHERE email = :email";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vincular los parámetros
            $pdoSt->bindParam(":email", $email, PDO::PARAM_STR);

            //Ejecutamos
            $pdoSt->execute();

            //Obtenemos el conteo
            //Si el conteo es cero, significa que el email es único
            $count = $pdoSt->fetchColumn();

            //Si el conteo es cero, significa que el email es válido
            return $count == 0;
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método getUserByID
    //Consulta SELECT a la tabla usuarios
    public function getUserByID($id)
    {
        try {
            $sql = "SELECT * FROM users WHERE id = :id";

            //Conectar con la base de datos
            $pdo = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $pdo->prepare($sql);

            //Vincular los parámetros
            $pdoSt->bindParam(':id', $id, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos
            return $pdoSt->fetch();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método getRoleOfUser
    //Consulta SELECT a la tabla roles
    public function getRoleOfUser($id)
    {
        try {
            $sql = "SELECT roles.id, roles.name
                    FROM roles
                    INNER JOIN roles_users ON roles.id = roles_users.role_id
                    INNER JOIN users ON roles_users.user_id = users.id
                    WHERE users.id = :id";

            //Conectar con la base de datos
            $pdo = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $pdo->prepare($sql);

            //Vincular los parámetros
            $pdoSt->bindParam(':id', $id, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos
            return $pdoSt->fetch();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método delete
    //Permite eliminar un usuario, ejecuta DELETE 
    public function delete($id)
    {
        try {
            $sql = " DELETE FROM users WHERE id=:id;";


            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vincular los parámetros
            $pdoSt->bindParam(":id", $id, PDO::PARAM_INT);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos
            return $pdoSt;
        } catch (PDOException $error) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método order
    //Permite ordenar la tabla por cualquiera de las columnas de la tabla
    public function order(int $criterio)
    {
        try {
            $sql = "SELECT * from users ORDER BY :criterio";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vincular los parámetros
            $pdoSt->bindParam(':criterio', $criterio, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos
            return $pdoSt;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método filter
    //Permite filtrar la tabla cuentas a partir de una expresión de búsqueda o filtrado
    public function filter($expresion)
    {
        try {

            $sql = " SELECT 
                        users.id,
                        users.name,
                        users.email
                    FROM 
                        users
                    WHERE 
                        concat_ws(  ' ',
                                    id,
                                    name,
                                    email
                                )
                    LIKE
                        :expresion ";


            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vincular los parámetros
            $expresion = "%" . $expresion . "%";
            $pdoSt->bindValue(':expresion', $expresion, PDO::PARAM_STR);

            //Preparamos la consulta SQL para su ejecución
            $pdoSt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $pdoSt->execute();

            //Retornamos
            return $pdoSt;
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }

    //Método update
    //Actualiza los detalles de un usuario, incluido el rol
    public function update(classUser $usuario, $id, $idRol)
    {
        try {
            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Actualizamos los detalles del usuario en la tabla users
            $sql = "UPDATE users SET
                    name = :name,
                    email = :email,
                    password = :password,
                    update_at = NOW()
                WHERE
                    id=:id";


            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(":name", $usuario->name, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(":email", $usuario->email, PDO::PARAM_STR, 50);
            $pdoSt->bindParam(":password", $usuario->password, PDO::PARAM_STR, 60);
            $pdoSt->bindParam(":id", $id, PDO::PARAM_INT);

            //Ejecutamos
            $pdoSt->execute();

            //Actualizamos el rol del usuario en la tabla roles_users
            $sql = "UPDATE roles_users SET
                    role_id = :role_id,
                    update_at = NOW()
                WHERE
                    user_id = :user_id";

            //Preparamos la consulta SQL para su ejecución
            $pdoSt = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $pdoSt->bindParam(":role_id", $idRol, PDO::PARAM_INT);
            $pdoSt->bindParam(":user_id", $id, PDO::PARAM_INT);

            //Ejecutamos
            $pdoSt->execute();
        } catch (PDOException $e) {
            require_once("template/partials/errorDB.php");
            exit();
        }
    }
}
