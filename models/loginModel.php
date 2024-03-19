<?php
class loginModel extends Model
{


    //Método getUserEmail
    //Devuelve un objeto de la clase Users a partir del email de usuario
    public function getUserEmail($email)
    {
        try {
            $sql = "SELECT * FROM Users WHERE email= :email LIMIT 1";

            //Conectar con la base de datos
            $pdo = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $stmt = $pdo->prepare($sql);

            //Vinculamos los parámetros
            $stmt->bindParam(":email", $email, PDO::PARAM_STR);

            //Establecemos tipo fetch
            $stmt->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $stmt->execute();

            //Retornamos los datos
            return $stmt->fetch();
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Devuelve id de perfil a partir del id de usuario
    public function getUserIdPerfil($id)
    {
        try {
            $sql = "SELECT 
                        ru.role_id
                    FROM
                        users u
                    INNER JOIN
                        roles_users ru ON u.id = ru.user_id
                    WHERE
                        u.id = :id
                    LIMIT 1";

            //Conectar con la base de datos
            $pdo = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $resultado = $pdo->prepare($sql);

            //Vinculamos los parámetros
            $resultado->bindParam(':id', $id, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $resultado->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $resultado->execute();

            //Retornamos los datos
            return $resultado->fetch()->role_id;
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }


    //Obtener el nombre perfil a partir del id de perfil
    public function getUserPerfil($id)
    {
        try {
            $sql = "SELECT 
                        name
                    FROM
                        roles
                    WHERE
                        id = :id
                    LIMIT 1";

            //Conectar con la base de datos
            $pdo = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $resultado = $pdo->prepare($sql);

            //Vinculamos los parámetros
            $resultado->bindParam(':id', $id, PDO::PARAM_INT);

            //Establecemos tipo fetch
            $resultado->setFetchMode(PDO::FETCH_OBJ);

            //Ejecutamos
            $resultado->execute();

            //Retornamos el campo name del objeto
            return $resultado->fetch()->name;
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }
}
