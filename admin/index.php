<?php
session_start();

if (!isset($_SESSION['tuvastamine'])) {
  header('Location: login.php');
  exit();
  }

?>

<?php include('../config.php'); ?>
<?php include('../header.php'); ?>

<!-- sisu -->
<div class="container">
    <h2>Adminni ala</h2>
    <a href="lisa.php" class="btn btn-success">+ Lisa auto</a>
    <div class="row row-cols-1 row-cols-md-4 g-4">
<!-- üks auto -->
<?php
    // sõnumi kuvamine
    if(isset($_GET['msg'])){
      echo '<div class="alert alert-success" role="alert"> Kõik on hästi! </div>';
    }


  //autode kuvamine
    $paring = "SELECT * FROM cars";
    if (!empty($_GET["otsi"])) {
        $otsing = $_GET["otsi"];
        $paring .= " WHERE mark LIKE '%".$otsing."%'";
    } 
    $paring .= " LIMIT 8";
    // var_dump($_GET["otsi"]);

    $valjund = mysqli_query($yhendus, $paring); //saadan päringu andmebaasi

?>

<table class="table">
  <thead>
    <tr>
      <th scope="col">#</th>
      <th scope="col">Mark</th>
      <th scope="col">Mudel</th>
      <th scope="col">Hind</th>
      <th scope="col">Hind</th>
      <th scope="col">Hind</th>
      <th scope="col">Hind</th>
      <th scope="col">Hind</th>
      <th scope="col">Hind</th>
      <th scope="col">Hind</th>
      <th scope="col">Hind</th>
      <th scope="col">Hind</th>
      <th scope="col">Kustuta</th>
      <th scope="col">Muuda</th>
    </tr>
  </thead>
  <tbody>
    <?php
        while($rida = mysqli_fetch_assoc($valjund)){       //sikutan vastuse alla
            // var_dump($rida);                       //kuvan testvastuse
    ?>
    <tr>
      <th scope="row"><?php echo $rida["id"]; ?></th>
      <td><?php echo $rida["mark"]; ?></td>
      <td><?php echo $rida["model"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><?php echo $rida["price"]; ?></td>
      <td><a href="kustuta.php?delid=<?= $rida["id"]; ?>" class="btn btn-danger">Kustuta</a></td>
      <td><a href="muuda.php?editid=<?= $rida["id"]; ?>" class="btn btn-warning">Muuda</a></td>
    </tr>

    <?php } ?>

  </tbody>
</table>

</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>
