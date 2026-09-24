<?php
    // Sinu andmed
    $db_server = 'db';
    $db_andmebaas = 'autorent';
    $db_kasutaja = 'okane';
    $db_salasona = 'okane';

    // Ühendus andmebaasiga
    $yhendus = mysqli_connect($db_server, $db_kasutaja, $db_salasona, $db_andmebaas);

    // Ühenduse kontroll
    if (!$yhendus) {
        die('Ei saa ühendust andmebaasiga');
    }
?>
