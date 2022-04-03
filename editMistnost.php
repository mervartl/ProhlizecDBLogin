<?php
session_start();
require_once('_includes/redirect.class.php');
require_once('_includes/connect_db.class.php');
require_once('_includes/checkString.class.php');
if (empty($_SESSION)) {
    Redirect::redir();
}
$state = "OK";
$roomId = filter_input(INPUT_GET, "room_id", FILTER_VALIDATE_INT);

$pdo = DB::connect();

$rquery = "SELECT * FROM room WHERE room_id=:roomId";
$rstmt = $pdo->prepare($rquery);
$rstmt->execute(["roomId" => $roomId]);

if ($roomId === null) {
    http_response_code(400);
    $state = "BadRequest";
    echo "<h1>Chybný požadavek</h1>";
    exit;
} elseif ($rstmt->rowCount() == 0) {
    http_response_code(404);
    $state = "NotFound";
    echo "<h1>Místnost nenalezena</h1>";
    exit;
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
    <title>Editace místnosti</title>
</head>

<body>
    <form method="post" class="container">
        <h1>Editace místnosti <a href=prohlizec.php class='btn btn-secondary'><i class='bi bi-caret-left-fill'></i> Zpět</a></h1><br>
        <label for="no">Číslo místnosti:</label><br>
        <input name="no" type="number" class="form-control" required>
        <br><label for="name">Jméno místnosti:</label><br>
        <input name="name" type="text" class="form-control" required>
        <br><label for="phone">Telefon:</label><br>
        <input name="phone" type="number" class="form-control">
        <br><input type="submit" value="Odeslat" class="btn btn-primary mb-3">
    </form>
</body>

</html>
<?php
if ($_SESSION['admin'] === 0) {
    Redirect::redir();
} else {
    if ($_POST) {
        $aquery = 'SELECT * FROM room';
        $astmt = $pdo->prepare($aquery);
        $astmt->execute();

        if (!is_nan($_POST['no'])) {
            $roomNumber = filter_var($_POST['no'], FILTER_VALIDATE_INT);
            $nCheck = 1;
        } else echo "<div class='container'>Číslo místností musí být ČÍSLO</div><br>";

        if (Check::checkkk($_POST['name'])) {
            $roomName = htmlspecialchars($_POST['name']);
            $nCheck++;
        } else echo "<div class='container'>Jméno místnosti musí být TEXT</div><br>";

        $phoneLength = strlen((string)$_POST['phone']);

        if ($_POST['phone'] == "") {
            $roomPhone = null;
            $nCheck++;
        } elseif (!is_nan($_POST['phone']) && $phoneLength == 4) {
            $roomPhone = filter_var($_POST['phone'], FILTER_VALIDATE_INT);
            $nCheck++;
        } else echo "<div class='container'>Telefon místnosti musí být čtyřmístné číslo nebo nic</div><br>";

        $duplicateCheck = 0;
        if ($nCheck === 3) {
            foreach ($astmt as $room) {
                if ($room->no == $roomNumber) {
                    echo "<div class='container'>Číslo místnosti už je v databázi použito.</div><br>";
                    $duplicateCheck++;
                }

                if ($room->name == $roomName) {
                    echo "<div class='container'>Název místnosti už je v databázi použit.</div><br>";
                    $duplicateCheck++;
                }

                if ($roomNumber !== ""); {
                    if ($room->phone == $roomNumber) {
                        echo "<div class='container'>Telefon místnosti už je v databázi použit.</div><br>";
                        $duplicateCheck++;
                    }
                }
            }
        }

        if ($duplicateCheck === 0 && $nCheck === 3) {
            if ($_POST['phone'] == "") {
                $var = null;
                $query = 'UPDATE `room` SET `no`=' . $roomNumber . ', `name`=' . "'{$roomName}'" . ', `phone`= ' . var_export($var, true) . ' WHERE room_id=:roomId';
            } else {
                $query = 'UPDATE `room` SET `no`= ' . $roomNumber . ", `name`= '" . "'{$roomName}'" . "', `phone`= " . $roomPhone . " WHERE room_id=:roomId";
            }
            $stmt = $pdo->prepare($query);
            $stmt->execute(["roomId" => $roomId]);

            echo "<div class='container'>Místnost {$roomName} byla změněna.</div>";
        }
        $nCheck = 0;
    }
}
?>