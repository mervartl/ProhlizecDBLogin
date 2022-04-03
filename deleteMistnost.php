<?php
session_start();
require_once('_includes/redirect.class.php');
require_once('_includes/connect_db.class.php');
if (empty($_SESSION)) {
    Redirect::redir();
}
if ($_SESSION['admin'] === 0) {
    Redirect::redir();
} else {
    $roomId = filter_input(INPUT_GET, "room_id", FILTER_VALIDATE_INT);

    if ($roomId === null) {
        http_response_code(400); //bad request
        $state = "BadRequest";
        echo "<h1>Chybný požadavek</h1>";
        exit;
    }
    else
    {
        $cquery = "SELECT * FROM room WHERE room_id=:roomId";
        $pdo = DB::connect();
        $cstmt = $pdo->prepare($cquery);
        $cstmt->execute(["roomId" => $roomId]);
    }

    if ($cstmt->rowCount() == 0)
    {
        http_response_code(404);
        $state = "NotFound";
        echo "<h1>Místnost nenalezena</h1>";
        exit;
    }    
    else {
        $room = $cstmt->fetch();
        echo "<div class='container'><h2>Vážně chceš smazat místnost {$room->name} ({$room->no}) a všechny jeho korespondující záznamy?</h2>";
        echo "<form method='post'><input type='submit' class='btn btn-danger' name='delete'></input></form></div>";

        function delete()
        {
            global $roomId;
            $pdo = DB::connect();

            $query = 'DELETE FROM `room` WHERE room_id=:roomId';
            $stmt = $pdo->prepare($query);

            $kquery = 'DELETE FROM `key` WHERE room=:roomId';
            $kstmt = $pdo->prepare($kquery);

            $rquery = 'UPDATE `employee` SET room=null WHERE room=:roomId';
            $rstmt = $pdo->prepare($rquery);

            $rstmt->execute(["roomId" => $roomId]);
            $kstmt->execute(["roomId" => $roomId]);
            $stmt->execute(["roomId" => $roomId]);

            header('Location: zamestnanci.php');
        }
        if ($_SERVER['REQUEST_METHOD'] == "POST" and isset($_POST['delete'])) {
            delete();
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
    <title>Smazat místnost</title>
</head>

<body>
</body>

</html>