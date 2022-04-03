<?php
session_start();
require_once('_includes/redirect.class.php');
if(empty($_SESSION))
{
    Redirect::redir();
}

require_once ("_includes/connect_db.class.php");

$state = "OK";
$roomId = filter_input(INPUT_GET, "room_id", FILTER_VALIDATE_INT);

if ($roomId === null) 
{
    http_response_code(400); //bad request
    $state = "BadRequest";
}
else
{

    $pdo = DB::connect();

    $rquery = "SELECT * FROM room WHERE room_id=:roomId";
    $rstmt = $pdo->prepare($rquery);
    $rstmt->execute(["roomId" => $roomId]);

    if ($rstmt->rowCount() == 0)
    {
        http_response_code(404);
        $state = "NotFound";
    }
    else
    {
        $room = $rstmt->fetch();
    }
    
    $wagequery = 'SELECT AVG(wage) FROM employee WHERE room=:roomId';
    $wagestmt = $pdo->prepare($wagequery);
    $wagestmt->execute(["roomId" => $roomId]);
    $avgwage = $wagestmt->fetchColumn();

    $keysquery = 'SELECT * FROM `key` WHERE room=:roomId ORDER BY employee DESC';
    $keysstmt = $pdo->prepare($keysquery);
    $keysstmt->execute(["roomId" => $roomId]);

    $equery = 'SELECT * FROM employee ORDER BY surname ASC';
    $estmt = $pdo->prepare($equery);
    $estmt->execute();
    
    $estmt2 = $pdo->prepare($equery);
    $estmt2->execute();
    while($key=$keysstmt->fetch())
    {
        $empKeyId[]=$key->employee;
    }
    $count = 0;
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
    <title>Detail místosti <?php if($state==="OK"){echo "{$room->no}";}?></title>
</head>
<body><div class="container"><?php
if ($state === "OK") {
    echo "<h1>Místnost č. {$room->no} </h1>";
    echo "<strong>Číslo:</strong> {$room->no} <br>";
    echo "<strong>Název:</strong> {$room->name} <br>";
    echo "<strong>Tel:</strong>" . " " . ($room->phone ?: '&mdash;&mdash;') . "<br>";
    echo "<strong>Lidé:</strong>";
    while($eId=$estmt->fetch())
    {
        if($room->room_id==$eId->room)
        {
            echo "<br><a href=zamestnanec.php?employee_id={$eId->employee_id}>{$eId->name} {$eId->surname}</a>";
            $count=1;
        }
    }    
    if($count==0)
    {
        echo " &mdash;&mdash;";
    }
    echo "<br><strong>Průměrná mzda:</strong>" . " " . (number_format($avgwage,2, ',', ' ') ?: '&mdash;&mdash;') .  "<br>";
    echo "<strong>Klíče:</strong><br>";
    while($eKeys=$estmt2->fetch())
    {  
        for($i = 0;$i < count($empKeyId);$i++)
        {            
            if($empKeyId[$i]==$eKeys->employee_id)
            {
                echo "<a href=zamestnanec.php?employee_id={$empKeyId[$i]}>{$eKeys->name} {$eKeys->surname}</a> <br>";
            }
        }
    }
    echo "<br><a href=mistnosti.php><i class='bi bi-caret-left-fill'></i> Zpět na seznam místností</a>";
}
elseif ($state === "NotFound")
{
    echo "<h1>Místnost nenalezena</h1>";
}
elseif ($state === "BadRequest")
{
    echo "<h1>Chybný požadavek</h1>";
}
?></div></body>
</html>