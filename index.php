<?php
session_start();
require_once("_includes/connect_db.class.php");
$state = "ok";

$pdo = DB::connect();

if ($_POST) {
    if ($_POST['user']=="" && $_POST['pass']=="") {
        echo "<h2 class='text-center'>Žádné údaje</h2>";
    } else {
        $username = $_POST['user'];
        $password = $_POST['pass'];

        $query = 'SELECT `login`, `password`, `admin`, employee_id FROM employee WHERE `login`=:username';
        $stmt = $pdo->prepare($query);
        $stmt->execute(["username" => $username]);

        $data = $stmt->fetch();

        if ($data) {
            if ($username == $data->login && $password == $data->password) {

                //echo "prihlasen";
                $_SESSION['user'] = $username;
                $_SESSION['password'] = $password;
                $_SESSION['admin'] = $data->admin;
                $_SESSION['employee_id'] = $data->employee_id;
                header('Location: prohlizec.php');
            } else {
                echo "<h2 class='text-center'>Špatné údaje</h2>";
            }
        } else {
            echo "<h2 class='text-center'>Špatné údaje</h2>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>LOGIN</title>
</head>

<body>
    <div class=>
        <form method="post" class="container">
            <label for="user">Uživatelské jméno:</label><br>
            <input name="user" type="text" class="form-control">
            <br><label for="pass">Heslo:</label><br>
            <input name="pass" type="password" class="form-control">
            <br><br>
            <input type="submit" value="Přihlásit" class="btn btn-primary mb-3">
        </form>
    </div>
</body>

</html>