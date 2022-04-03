<?php
session_start();
require_once('_includes/redirect.class.php');
require_once('_includes/connect_db.class.php');
if(empty($_SESSION))
{
    Redirect::redir();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Změna hesla</title>
</head>
<body>
</body>
</html>
<?php
echo "<div class='container'>";
echo '<form method="post" class="container">';
echo '<label for="oldPass">Staré heslo:</label><br>
     <input name="oldPass" type="password" class="form-control">';
echo '<br><label for="newPass">Nové heslo:</label><br>
     <input name="newPass" type="password" class="form-control">';
echo '<br><label for="confirmPass">Potvrdit nové heslo:</label><br>
     <input name="confirmPass" type="password" class="form-control">';
echo '<br><input type="submit" value="Změnit heslo" class="btn btn-primary mb-3">';
echo "</div>";

if($_POST)
{
    if($_POST['oldPass']===$_SESSION['password'])
    {
        if($_POST['newPass']===$_POST['oldPass'])
        {
            echo "<div class='container'>Nové heslo je stejné jako staré</div>";
        }
        else
        {
            if($_POST['newPass']===$_POST['confirmPass'])
            {
                $pdo = DB::connect();
    
                $query = 'UPDATE employee SET `password`="'.$_POST['newPass'] .'"WHERE employee_id=:employeeId';
                $stmt = $pdo->prepare($query);
                $stmt->execute(["employeeId" => $_SESSION['employee_id']]);
    
                session_destroy();
                header('Location: index.php');
            }
            else
            {
                echo"<div class='container'>Nová hesla se neshodují</div>";
            }
        }        
    }
    else
    {
        echo "Špatné heslo";
    }
}
?>
