<?php
// db.php - Gestione connessioni con separazione dei privilegi

function connetti_lettore() {
    // Usato per visualizzare gatti e verificare disponibilità orarie
    $connessione = new mysqli("localhost", "lecture", "P@ssw0rd!", "gattile_db");
    if ($connessione->connect_error) {
        die("Connessione fallita (Lettore): " . $connessione->connect_error);
    }
    return $connessione;
}

function connetti_modificatore() {
    // Usato per prenotazioni, turni e inserimento nuovi gatti
    $connessione = new mysqli("localhost", "modifier", "Str0ng#Admin9", "gattile_db");
    if ($connessione->connect_error) {
        die("Connessione fallita (Modificatore): " . $connessione->connect_error);
    }
    return $connessione;
}

function connetti_registratore() {
    // Usato ESCLUSIVAMENTE nella pagina di registrazione
    $connessione = new mysqli("localhost", "registrator", "ToB31nsert?", "gattile_db");
    if ($connessione->connect_error) {
        die("Connessione fallita (Registratore): " . $connessione->connect_error);
    }
    return $connessione;
}
?>