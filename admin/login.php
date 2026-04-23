<?php
    session_start();
    include('../config.php');
?>

<!doctype html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  </head>
  <body>
<?php 
    $msg = "";

    if (!empty($_POST)) {
        $uname = $_POST['user'];
        $password = $_POST['password'];

        // kasutaja andmebaasist
        $paring = "SELECT user, password FROM users WHERE user='".$uname."'";
        print_r($paring);
        $valjund = mysqli_query($yhendus, $paring);
        $rida = mysqli_fetch_assoc($valjund);
        var_dump($rida);

        if (!empty($rida)) {
            $hash = $rida['password'];
            if ($uname== $rida['user'] && password_verify($password, $hash)) {
                $_SESSION['tuvastamine'] = 'misiganes';
                header("Location: index.php");
            }else{
                $msg = "kasutaja vale";
            }
        }
    }
?>
    <div class="container">
        <div class="row">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <form method="post" action="login.php">
                    <div class="mb-3">
                        <label for="u" class="form-label">Username</label>
                        <input name="user" type="text" class="form-control" id="u">
                    </div>
                    <div class="mb-3">
                        <label for="p" class="form-label">Password</label>
                        <input name="password" type="password" class="form-control" id="p">
                    </div>
                    <button type="submit" class="btn btn-primary">Logi sisse</button>
                </form>
                <?php echo $msg; ?>
            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>


<div class="container">
    <div class="row">
        <div class="col">
            <div class="col-sm-4"></div>
            <div class="col-sm-4">

            </div>
            <div class="col-sm-4"></div>
        </div>
    </div>
</div>