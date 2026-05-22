<?php
    // Sinu andmed
    $db_server = 'localhost';
    $db_andmebaas = 'autorent';
    $db_kasutaja = 'mario';
    $db_salasona = 'mario';

    // Ühendus andmebaasiga
    $yhendus = mysqli_connect($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);

    // Ühenduse kontroll
    if (!$yhendus) {
        die('Ei saa ühendust andmebaasiga');
    }
?>