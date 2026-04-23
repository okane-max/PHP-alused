<?php include('../config.php'); ?>
<?php include('../header.php'); ?>

<?php
    // if(!empty($_GET)){
    //    $mark = $_GET['mark'];
    //    $mudel = $_GET['mudel'];
    //    $mootor = $_GET['mootor'];
    //    $kütus = $_GET['kütus'];
    //    $hind = $_GET['hind'];

    //    $aasta = $_GET['aasta'];
    //    $transmission = $_GET['transmission'];
    //    $istmed = $_GET['istmed'];
    //    $kirjeldus = $_GET['kirjeldus'];
    //    $staatus = $_GET['staatus'];


    //    $sql = "INSERT INTO cars (mark, mudel, mootor, kütus, hind, aasta, transmission, istmed, kirjeldus, staatus) VALUES ('".$mark."', '".$mudel."', '".$mootor."', '".$kütus."', '".$hind."', '".$aasta."', '".$transmission."', '".$istmed."', '".$kirjeldus."', '".$staatus."')";

    //    $valjund = mysqli_query($yhendus, $sql); 
    //    $tulemus = mysqli_affected_rows($yhendus);
    //     if ($tulemus == 1) {
    //         // header("Location: index.php?msg=lisatud");
    //     } else {
    //         echo "Kirjet ei lisatud";
    //     }


    // }
   

    // }


    if(isset($_GET["editid"])){
        $id = $_GET["editid"];
        $paring = "SELECT * FROM cars WHERE id=$id";
        $valjund = mysqli_query($yhendus, $paring);
        $rida = mysqli_fetch_assoc($valjund);
            // print_r($rida['mark']);
    }

      if(isset($_GET["updateid"])){
        $id = $_GET["updateid"];
        $mark = $_GET['mark'];
        $mudel = $_GET['mudel'];
        $mootor = $_GET['mootor'];
        $kütus = $_GET['kütus'];
        $hind = $_GET['hind'];

        $aasta = $_GET['aasta'];
        $transmission = $_GET['transmission'];
        $istmed = $_GET['istmed'];
        $kirjeldus = $_GET['kirjeldus'];
        $staatus = $_GET['staatus'];

        $paring = "UPDATE cars SET mark = '".$mark."', mudel = '".$mudel."', mootor = '".$mootor."', kütus = '".$kütus."', hind = '".$hind."', aasta = '".$aasta."', transmission = '".$transmission."', istmed = '".$istmed."', kirjeldus = '".$kirjeldus."', staatus = '".$staatus."' WHERE cars.id = ".$id."";

        // print_r($paring);

        $valjund = mysqli_query($yhendus, $paring);
        $tulemus = mysqli_affected_rows($yhendus);
        if ($tulemus == 1) {
            header("Location: index.php?msg=uuendatud");
        } else {
            echo "Kirjet ei lisatud";
        }


    }



?>

<!-- sisu -->
<div class="container">
    <h2>Auto lisamine</h2>
    <form action="muuda.php" method="get">
        <div class="row g-4">
            <div class="col-sm-6">
                <input type="hidden" name="updateid" value="<?= $rida['id']; ?>">

                <label for="mark" class="form-label">Mark</label>
                <input type="text" class="form-control" id="mark" name="mark" value="<?= $rida['mark']; ?>">
                <label for="mudel" class="form-label">mudel</label>
                <input type="text" class="form-control" id="mudel" name="mudel" value="<?= $rida['mudel']; ?>">
                <label for="mootor" class="form-label">Mootor</label>
                <input type="text" class="form-control" id="mootor" name="mootor" value="<?= $rida['mootor']; ?>">
                <label for="kütus" class="form-label">Kütus</label>
                <input type="text" class="form-control" id="kütus" name="kütus" value="<?= $rida['kütus']; ?>">
                <label for="hind" class="form-label">Hind</label>
                <input type="number" class="form-control" id="hind" name="hind" value="<?= $rida['hind']; ?>">
            </div>
            <div class="col-sm-6">
                <label for="aasta" class="form-label">Aasta</label>
                <input type="number" class="form-control" id="aasta" name="aasta" value="<?= $rida['aasta']; ?>">
                <label for="transmission" class="form-label">Käigukast</label>
                <input type="text" class="form-control" id="transmission" name="transmission" value="<?= $rida['transmission']; ?>">
                <label for="istmed" class="form-label">Istmete arv</label>
                <input type="number" class="form-control" id="istmed" name="istmed" value="<?= $rida['istmed']; ?>">
                <label for="kirjeldus" class="form-label">Muu info</label>
                <input type="text" class="form-control" id="kirjeldus" name="kirjeldus" value="<?= $rida['kirjeldus']; ?>">
                <label for="staatus" class="form-label">Olek</label>
                <input type="text" class="form-control" id="staatus" name="staatus" value="<?= $rida['staatus']; ?>">
            </div>
            <input type="submit" value="Salvesta" class="btn btn-success">
        </div>
    </form>
   

</div>
<!-- /sisu -->

    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.bundle.min.js" integrity="sha384-FKyoEForCGlyvwx9Hj09JcYn3nv7wiPVlz7YYwJrWVcXK/BmnVDxM+D2scQbITxI" crossorigin="anonymous"></script>
  </body>
</html>