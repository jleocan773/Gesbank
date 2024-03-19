<?php
class PerfilModel extends Model
{
    //Método getUserID
    //Devuelve los datos de un usuario por su ID
    public function getUserId($id)
    {
        try {
            $sql = "SELECT * FROM users WHERE id= :id LIMIT 1";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $result = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $result->bindParam(":id", $id, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $result->setFetchMode(PDO::FETCH_CLASS | PDO::FETCH_PROPS_LATE, 'classUser');

            //Ejecutamos
            $result->execute();

            //Retornamos los datos
            return $result->fetch();
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método updatePass
    //Actualizar password
    public function updatePass(classUser $user)
    {
        try {
            //Encriptamos la contraseña
            $password_encriptado = password_hash($user->password, CRYPT_BLOWFISH);
            $update = " UPDATE users SET password = :password WHERE id = :id";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $result = $conexion->prepare($update);

            //Vinculamos los parámetros
            $result->bindParam(':password', $password_encriptado, PDO::PARAM_STR, 50);
            $result->bindParam(':id', $user->id, PDO::PARAM_INT);

            //Ejecutamos
            $result->execute();
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método validateName
    //Valida nombre de usuario, ha de ser único
    public function validateName($name)
    {

        try {
            $sql = "SELECT * FROM users WHERE name = :name";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $result = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $result->bindParam(':name', $name, PDO::PARAM_STR);

            //Ejecutamos
            $result->execute();

            //Si el número de filas encontradas es distinto de 0, significa que ya existe, por lo que no es válido
            if ($result->rowCount() != 0)
                return FALSE;

            //De lo contrario, es correcto
            return TRUE;
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método validateEmail
    //Valida nombre de usuario ha de ser único
    public function validateEmail($email)
    {

        try {
            $sql = "SELECT * FROM users WHERE email = :email  ";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $result = $conexion->prepare($sql);

            //Vinculamos los parámetros
            $result->bindParam(':email', $email, PDO::PARAM_STR);

            //Ejecutamos
            $result->execute();

            //Si el número de filas encontradas es distinto de 0, significa que ya existe, por lo que no es válido
            if ($result->rowCount() != 0)
                return FALSE;

            //De lo contrario, es correcto
            return TRUE;
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método update 
    //Actualizar perfil name y email
    public function update(classUser $user)
    {
        try {

            $update = "UPDATE users SET
                            name = :name,
                            email = :email
                        WHERE id = :id
                        LIMIT 1";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $result = $conexion->prepare($update);

            //Vinculamos los parámetros
            $result->bindParam(':name', $user->name, PDO::PARAM_STR, 50);
            $result->bindParam(':email', $user->email, PDO::PARAM_STR, 50);
            $result->bindParam(':id', $user->id, PDO::PARAM_INT);

            //Ejecutamos
            $result->execute();
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método delete
    //Eliminar perfil según la ID del usuario
    public function delete($id)
    {

        try {
            $delete = "DELETE FROM users WHERE id = :id";

            //Conectar con la base de datos
            $conexion = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $result = $conexion->prepare($delete);

            //Vinculamos los parámetros
            $result->bindParam(':id', $id, PDO::PARAM_INT);

            //Ejecutamos
            $result->execute();
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }
}
