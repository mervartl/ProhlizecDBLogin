<?php
session_start();
require_once('_includes/redirect.class.php');
if(empty($_SESSION))
{
    Redirect::redir();
}

require_once ("_includes/connect_db.class.php");

$state = "OK";

$employeeId = filter_input(INPUT_GET, "employee_id", FILTER_VALIDATE_INT);

if ($employeeId === null)
{
    http_response_code(400); //bad request
    $state = "BadRequest";
}
else
{
    $pdo = DB::connect();

    $equery = 'SELECT * FROM employee WHERE employee_id=:employeeId';
    $estmt = $pdo->prepare($equery);
    $estmt->execute(["employeeId" => $employeeId]);

    if ($estmt->rowCount() == 0)
    {
        http_response_code(404);
        $state = "NotFound";
    } 
    else 
    {
        $employee = $estmt->fetch();
    }
    
    $rquery = 'SELECT * FROM room ORDER BY `name` ASC';
    $rstmt = $pdo->prepare($rquery);
    $rstmt->execute();

    $rstmt2 = $pdo->prepare($rquery);
    $rstmt2->execute();

    $kquery = 'SELECT * FROM `key`';
    $kstmt = $pdo->prepare($kquery);
    $kstmt->execute();
}
?>
<!doctype html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3" crossorigin="anonymous">
    <title>Detail zaměstnance <?php if($state==="OK"){echo "{$employee->name} {$employee->surname}";}?></title>
</head>
<body><div class="container"><?php
if ($state === "OK")
{
    foreach($kstmt as $key)
    {
        if($key->employee==$employee->employee_id)
        {
            $keysIds[]=$key->room;
        }
    }

    echo "<h1>Osoba: {$employee->name} {$employee->surname}</h1>";
    echo "<strong>Jméno:</strong> {$employee->name}<br>";
    echo "<strong>Příjmení:</strong> {$employee->surname}<br>";
    echo "<strong>Pozice:</strong> {$employee->job}<br>";
    echo "<strong>Mzda:</strong> {$employee->wage}<br>";
    echo "<strong>Místnost:</strong><br>";
    while($room=$rstmt->fetch())
    {            
        if($room->room_id==$employee->room)
        {
            echo "<a href=zamestnanec.php?employee_id={$employee->employee_id}>{$room->name}</a> <br>";
        }
    }
    echo "<strong>Klíče:</strong> <br>";
    while($r=$rstmt2->fetch())  
    {
        for($i=0;$i<count($keysIds);$i++)
        {
            if($r->room_id==$keysIds[$i])
            echo "<a href=mistnost.php?room_id={$r->room_id}>{$r->name}</a><br>";
        }
    }
    
    echo "<br><a href=zamestnanci.php><i class='bi bi-caret-left-fill'></i> Zpět na seznam zaměstnanců</a>";
} 
elseif ($state === "NotFound") 
{
    echo "<h1>Zaměstnanec nenalezen</h1>";
} 
elseif ($state === "BadRequest") 
{
    echo "<h1>Chybný požadavek</h1>";
}
?></div></body>
</html>