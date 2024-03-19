<?php
class RegisterModel extends Model
{

    //Método validateName
    //Valida el nombre de usuario, solo comprobamos que el usuario sea mayor de 5 letras y menor que 50
    public function validateName($username)
    {
        //Validamos que el usuario sea mayor de 5 letras y menor que 50
        //Si no lo es devolvemos falso
        if ((strlen($username) < 5) || (strlen($username) > 50)) {
            return false;
        }

        //De lo contrario, devolvemos verdadero
        return true;
    }

    //Método validatePass
    //Validar password, solo comprobamos que el password sea mayor de 5 letras y menor que 50
    public function validatePass($pass)
    {
        //Validamos que el password sea mayor de 5 letras y menor que 50
        //Si no lo es devolvemos falso
        if ((strlen($pass) < 5) || (strlen($pass) > 50)) {
            return false;
        }

        //De lo contrario, devolvemos verdadero
        return true;
    }

    #Validar email unique
    public function validateEmailUnique($email)
    {

        try {
            $selectSQL = "SELECT * FROM users WHERE email = :email";

            //Conectar con la base de datos
            $pdo = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $resultado = $pdo->prepare($selectSQL);

            //Vinculamos los parámetros
            $resultado->bindParam(':email', $email, PDO::PARAM_STR, 50);

            //Ejecutamos
            $resultado->execute();

            //Si el número de filas encontradas es distinto de 0, significa que ya existe, por lo que no es válido
            if ($resultado->rowCount() != 0) {
                return false;
            }

            //De lo contrario, es correcto
            return true;
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }

    //Método create
    //Creo nuevo usuario a partir de los datos de formulario de registro
    public function create($name, $email, $pass)
    {
        try {

            //Encriptamos el password
            $password_encriptado = password_hash($pass, CRYPT_BLOWFISH);

            $insertarsql = "INSERT INTO users VALUES (
                 null,
                :nombre,
                :email,
                :pass,
                default,
                default)";

            //Conectar con la base de datos
            $pdo = $this->db->connect();

            //Preparamos la consulta SQL para su ejecución
            $stmt = $pdo->prepare($insertarsql);

            //Vinculamos los parámetros
            $stmt->bindParam(':nombre', $name, PDO::PARAM_STR, 50);
            $stmt->bindParam(':email', $email, PDO::PARAM_STR, 50);
            $stmt->bindParam(':pass', $password_encriptado, PDO::PARAM_STR, 60);

            //Ejecutamos
            $stmt->execute();

            //Por defecto asignaremos el rol de administrador para que se puedan ver todas las funciones sin tener que cambiar el rol en la base de datos
            //En un entorno real el rol que se le asignaría sería el de menos permisos posible, es decir, "registrado"
            $role_id = 1;
            $insertarsql = "INSERT INTO roles_users VALUES (
                null,
                :user_id,
                :role_id,
                default,
                default)";

            //Obtener id del último usuario insertado
            $ultimo_id = $pdo->lastInsertId();

            //Preparamos la consulta SQL para su ejecución
            $stmt = $pdo->prepare($insertarsql);

            //Vinculamos los parámetros
            $stmt->bindParam(':user_id', $ultimo_id);
            $stmt->bindParam(':role_id', $role_id);
            $stmt->execute();
        } catch (PDOException $e) {
            include_once('template/partials/errorDB.php');
            exit();
        }
    }
}
