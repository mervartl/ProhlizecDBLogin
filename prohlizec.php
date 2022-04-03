<?php
session_start();
require_once('_includes/redirect.class.php');
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
    <title>Prohlížeč databáze</title>
</head>
<body><div class="container">
<h2>Prohlížeč databáze</h2>

<table class='table'>
<tbody>
<tr>
<td><a href='zamestnanci.php'>Seznam zaměstnanců </a></td>
</tr>
<tr>
<td><a href='mistnosti.php'>Seznam místností </a></td>
</tbody>
</table>
</div></body>
</html>
<?php
echo "<div class='container'><a href='changePass.php' class='btn btn-primary' role='button'>Změnit heslo</a>   ";
echo "<a href='_includes/logout.class.php' class='btn btn-info' role='button'>Odhlásit</a></div>";
?>