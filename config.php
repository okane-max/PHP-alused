<?php

    $db_server = 'localhost';
    $db_andmebaas = 'autorent';
    $db_kasutaja = 'root';
    $db_salasona = '';

    $yhendus = mysqli_connect($db_server,$db_andmebaas,$db_kasutaja,$db_salasona);

    if (!$yhendus) {
        die('Ei saa ühendust!');
    }

?>