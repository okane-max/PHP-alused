<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>PHP-alused</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
    <h1>Harjutus 01 - muutujad</h1>
    <?php
        $muutuja = "sõne";
        $arv = 611;
        $tahtkuju = "jäär";
        echo $muutuja,"<br>",$arv,"<br>",$tahtkuju,"<br>";
        echo "“It’s My Life” – Dr. Alban","<br>";
        echo "(\(\<br>";
        echo "( -.-)<br>";
        echo 'o_(")(")<br>';
    ?>
    <h2>Harjutus 02 - vormid</h2>
    <form action="h01.php" method="get">
        Arv1: <input type="number" name="arv1"><br>
        Arv2: <input type="number" name="arv2"><br>
        <input type="submit" value="Liida" class><br>
    </form>
    <?php
        if(!empty($_GET['arv1']) && !empty($_GET['arv2'])){
        $nr1 = $_GET['arv1'];
        $nr2 = $_GET['arv2'];
        $liida = $nr1 + $nr2;
        echo $liida;
        }
    ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>