<?php
session_start();
if (!isset($_SESSION['tuvastamine'])) {
  header('Location: login.php');
  exit();
  }

//autode kuvamine
$paring = "SELECT * FROM cars";
if(!empty($_GET["otsi"])) {
    $otsing = $_GET["otsi"];
    $paring .= " WHERE mark LIKE '%".$otsing."%'";
}
$paring .= " LIMIT 8";
// var_dump($_GET["otsi"]);

$valjund = mysqli_query($yhendus, $paring); //saadan päringu andmebaasi

?>
<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Empty page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <h1>Yippee!</h1>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>