<?php
session_start();
require_once('_includes/redirect.class.php');
require_once('_includes/connect_db.class.php');
require_once('_includes/checkString.class.php');
if (empty($_SESSION)) {
    Redirect::redir();
}
$state = "OK";
$employeeId = filter_input(INPUT_GET, "employee_id", FILTER_VALIDATE_INT);

$pdo = DB::connect();

$emquery = "SELECT * FROM employee WHERE employee_id=:employeeId";
$emstmt = $pdo->prepare($emquery);
$emstmt->execute(["employeeId" => $employeeId]);

if ($employeeId === null) {
    http_response_code(400);
    $state = "BadRequest";
    echo "<h1>Chybný požadavek</h1>";
    exit;
} elseif ($emstmt->rowCount() == 0) {
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
    <title>Edit zaměstnance</title>
</head>

<body>
    <form method="post" class="container">
        <h1>Edit zaměstnance <a href=prohlizec.php class='btn btn-secondary'><i class='bi bi-caret-left-fill'></i> Zpět</a></h1><br>

        <br><label for="name">Křestní jméno:</label><br>
        <input name="name" type="text" class="form-control" required>

        <br><label for="surname">Příjmení:</label><br>
        <input name="surname" type="text" class="form-control" required>

        <br><label for="job">Práce:</label><br>
        <input name="job" type="text" class="form-control" required>

        <br><label for="wage">Mzda:</label><br>
        <input name="wage" type="number" class="form-control" required>

        <br><label for="room">Domovní místnost:</label><br>
        <input name="room" type="text" class="form-control" required>

        <br><label for="login">Přihlasovací jméno:</label><br>
        <input name="login" type="text" class="form-control" required>

        <br><label for="password">Heslo:</label><br>
        <input name="password" type="password" class="form-control" required>

        <br><label for="confirmPassword">Potvrdit heslo:</label><br>
        <input name="confirmPassword" type="password" class="form-control" required>

        <br><input name="admin" id="admin" class="form-check-input" type="checkbox" value="1">
        <label class="form-check-label" for="admin">Admin</label><br>

        <br><input type="submit" value="Odeslat" class="btn btn-primary mb-3">
</body>

</html>
<?php
if ($_SESSION['admin'] === 0) {
    Redirect::redir();
} else {

    $pdo = DB::connect();

    $kquery = 'SELECT * FROM `key`';
    $kstmt = $pdo->prepare($kquery);
    $kstmt->execute();

    $rquery = 'SELECT room_id, `name` FROM room';
    $rstmt = $pdo->prepare($rquery);
    $rstmt->execute();


    $lastRoomId = 0;
    echo "<div class=container>";
    foreach ($rstmt as $keyRoom) {
        foreach ($kstmt as $key) {
            if ($key->room == $keyRoom->room_id) {
                if ($lastRoomId !== $key->room) {
                    $keyRName = $keyRoom->name;
                    $lastRoomId = $key->room;
                    echo "<br><input name='{$key->room}' id='{$key->room}' class='form-check-input' type='checkbox' value='{$key->room}'>
                    <label class='form-check-label' for='{$key->room}'>Klíč k místnosti {$keyRName}</label><br>";
                }
                break;
            }
        }
    }
    echo "</div></form>";

    if ($_POST) {
        $aquery = 'SELECT * FROM employee';
        $astmt = $pdo->prepare($aquery);
        $astmt->execute();

        $rquery = 'SELECT room_id, `name` FROM room';
        $rstmt = $pdo->prepare($rquery);
        $rstmt->execute();

        $kequery = 'SELECT * FROM `key` ORDER BY room DESC';
        $kestmt = $pdo->prepare($kequery);
        $kestmt->execute();

        $lastKeyId = 0;
        $employeeKeys = "";
        foreach ($kestmt as $key) {
            if (isset($_POST[$key->room])) {
                if ($lastKeyId !== $key->room) {
                    $employeeKeys .= "{$key->room},";
                    $lastKeyId = $key->room;
                }
            }
        }
        $employeeKeys = substr($employeeKeys, 0, -1);


        if (Check::checkkk($_POST['name'])) {
            $employeeName = htmlspecialchars($_POST['name']);
            $nCheck = 1;
        } else echo "<div class='container'>Jméno zaměstnance musí být TEXT</div><br>";

        if (Check::checkkk($_POST['surname'])) {
            $employeeSurname = htmlspecialchars($_POST['surname']);
            $nCheck++;
        } else echo "<div class='container'>Příjmení zaměstnance musí být TEXT</div><br>";

        if (Check::checkkk($_POST['job'])) {
            $employeeJob = htmlspecialchars($_POST['job']);
            $nCheck++;
        } else echo "<div class='container'>Práce zaměstnance musí být TEXT</div><br>";

        if (!is_nan($_POST['wage'])) {
            $employeeWage = filter_var($_POST['wage'], FILTER_VALIDATE_INT);
            $nCheck++;
        } else echo "<div class='container'>Mzda musí být ČÍSLO</div><br>";

        if (Check::checkkk($_POST['room'])) {
            $roomName = htmlspecialchars($_POST['room']);
            $nCheck++;

            $rCheck = false;

            foreach ($rstmt as $room) {
                if ($roomName === $room->name) {
                    $employeeRoom = $room->room_id;
                    $rCheck = true;
                }
            }
            if (!$rCheck) {
                echo "<div class='container'>Taková místnost neexistuje.</div><br>";
            }
        } else echo "<div class='container'>Název místnosti musí být TEXT</div><br>";

        if ($_POST['login']) {
            $employeeLogin = htmlspecialchars($_POST['login']);
            $nCheck++;
        } else echo "<div class='container'>Přihlašovací jméno musí být TEXT</div><br>";

        if ($_POST['password'] === $_POST['confirmPassword']) {
            $employeePass = htmlspecialchars($_POST['password']);
            $nCheck++;
        } else echo "<div class='container'>Hesla se neshodují.</div><br>";

        if (isset($_POST['admin'])) {
            $employeeAdmin = 1;
        } else $employeeAdmin = 0;

        $duplicateCheck = 0;

        if ($nCheck === 7) {
            foreach ($astmt as $employee) {
                if ($employee->login == $employeeLogin) {
                    echo "<div class='container'>Přihlašovací jméno už je zabráno.</div><br>";
                    $duplicateCheck++;
                }
            }
        } 
        if ($duplicateCheck === 0 && $nCheck === 7) {
            $query = 'UPDATE `employee` SET `name`=' . "'{$employeeName}'" . ", `surname`=" . "'{$employeeSurname}'" . ", `job`=" . "'{$employeeJob}'" . ", `wage`=" . $employeeWage . ", `room`=" . $employeeRoom . ", `login`=" . "'{$employeeLogin}'" . ", `password`=" . "'{$employeePass}'" . ", `admin`= " . $employeeAdmin . ' WHERE employee_id= ' . $employeeId;
            $stmt = $pdo->prepare($query);
            $stmt->execute();

            $keys_arr = explode(",", $employeeKeys);


            $allKeysQuery = "DELETE FROM `key` WHERE employee= " . $employeeId;
            $allKeysStmt = $pdo->prepare($allKeysQuery);
            $allKeysStmt->execute();

            foreach ($keys_arr as $keyy) {
                $keysQuery = "INSERT INTO `key`(employee, room) VALUES (" . $employeeId . "," . $keyy . ")";
                $keysStmt = $pdo->prepare($keysQuery);
                $keysStmt->execute();
            }

            echo "<div class='container'>Zaměstnanec {$employeeSurname} {$employeeName} upraven.</div>";
        }
        $nCheck = 0;
    }
}

?>