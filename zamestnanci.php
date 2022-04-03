<?php
session_start();

require_once('_includes/redirect.class.php');
require_once ('_includes/connect_db.class.php');

if(empty($_SESSION))
{
    Redirect::redir();
}

$query = 'SELECT *, employee.`name` AS ename FROM employee LEFT OUTER JOIN room ON employee.room = room.room_id';
if(!array_key_exists('sort',$_GET))
{
    http_response_code(400);
}
else
{
    if($_GET['sort']=='ASCJ')
    {
        $query .= ' ORDER BY ename ASC';
    }
    elseif($_GET['sort']=='DESCJ')
    {
        $query .= ' ORDER BY ename DESC';
    }
    elseif($_GET['sort']=='ASCM')
    {
        $query .= ' ORDER BY room.`name` ASC';
    }
    elseif($_GET['sort']=='DESCM')
    {
        $query .= ' ORDER BY room.`name` DESC';
    }
    elseif($_GET['sort']=='ASCT')
    {
        $query .= ' ORDER BY phone ASC';
    }
    elseif($_GET['sort']=='DESCT')
    {
        $query .= ' ORDER BY phone DESC';
    }
    elseif($_GET['sort']=='ASCP')
    {
        $query .= ' ORDER BY employee.job ASC';
    }
    elseif($_GET['sort']=='DESCP')
    {
        $query .= ' ORDER BY employee.job DESC';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Seznam zaměstnanců</title>
</head>
<body><div class="container"><?php
$pdo = DB::connect();

$employee = $pdo->query($query);

if ($employee->rowCount() == 0)
{
    echo "Databáze neobsahuje žádná data";
}
else
{
    echo "<h1><strong>Seznam zaměstanců</strong></h1>";
    echo "<br><a href=prohlizec.php class='btn btn-secondary'><i class='bi bi-caret-left-fill'></i> Zpět</a>   ";
    echo "<a href='newZamestnanec.php' class='btn btn-success' role='button'>Založit nového zaměstnance</a>   ";
    echo "<a href='_includes/logout.class.php' class='btn btn-info' role='button'>Odhlásit</a>";
    echo "<table class='table table-striped'>";
    echo "<thead><tr><th>Jméno<a href='zamestnanci.php?sort=ASCJ'><i class='bi bi-caret-down-fill'></i></a><a href='zamestnanci.php?sort=DESCJ'><i class='bi bi-caret-up-fill'></i></a></th>";
    echo "<th>Místnost<a href='zamestnanci.php?sort=ASCM'><i class='bi bi-caret-down-fill'></i></a><a href='zamestnanci.php?sort=DESCM'><i class='bi bi-caret-up-fill'></i></a></th>";
    echo "<th>Telefon<a href='zamestnanci.php?sort=ASCT'><i class='bi bi-caret-down-fill'></i></a><a href='zamestnanci.php?sort=DESCT'><i class='bi bi-caret-up-fill'></i></a></th>";
    echo "<th>Pozice<a href='zamestnanci.php?sort=ASCP'><i class='bi bi-caret-down-fill'></i></a><a href='zamestnanci.php?sort=DESCP'><i class='bi bi-caret-up-fill'></i></a></th></tr></thead>";
    echo "<tbody>";
    
    while ($rowE = $employee->fetch())
    {     
        echo "<tr>";
        echo "<td><a href='zamestnanec.php?employee_id={$rowE->employee_id}'>{$rowE->surname} {$rowE->ename}</a></td>";
        echo "<td>{$rowE->name}</td>";
        echo "<td>" . ($rowE->phone ?: "&mdash;") . "</td>";
        echo "<td>" . ($rowE->job) . "</td>";
        if($_SESSION['admin']===1)
        {
            echo "<td><a href='editZamestnance.php?employee_id={$rowE->employee_id}' class='btn btn-primary' role='button'>Editovat</a>   ";
            echo "<a href='deleteZamestnanec.php?employee_id={$rowE->employee_id}' class='btn btn-danger' role='button'>Smazat</a></td>";
        }      
        else
        {
            echo "<td><a class='btn btn-primary disabled' aria-disabled='true' role='button'>Editovat</a>   ";
            echo "<a class='btn btn-danger disabled' aria-disabled='true role='button'>Smazat</a></td>";
        }
        echo "</tr>";
    }    
    echo "</tbody>";
    echo "</table>";
}
?></div></body>
</html>