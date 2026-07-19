<!-- db.php - Gestione connessioni e privilegi -->

<?php
// Costanti di connessione al database (evitano hardcoding)
define('DB_SERVER', 'localhost');
define('DB_NAME', 'gattile_db');

function connetti_lettore() {
    // Visualizzare gatti, verificare disponibilità orarie
    $connessione = new mysqli(DB_SERVER, "lecture", "P@ssw0rd!", DB_NAME);
    if ($connessione->connect_error) {
        // die() ~ exit() -> stampa stringa e termina l'esecuzione del PHP
        die("Connessione fallita (Lettore): " . $connessione->connect_error);
    }
    $connessione->set_charset("utf8mb4"); // Integrità di codifica da specifiche
    return $connessione;
}

function connetti_modificatore() {
    // Prenotazioni, turni, inserimento nuovi gatti
    $connessione = new mysqli(DB_SERVER, "modifier", "Str0ng#Admin9", DB_NAME);
    if ($connessione->connect_error) {
        die("Connessione fallita (Modificatore): " . $connessione->connect_error);
    }
    $connessione->set_charset("utf8mb4");
    return $connessione;
}

function connetti_registratore() {
    // Solo nella pagina di registrazione
    $connessione = new mysqli(DB_SERVER, "registrator", "ToB31nsert?", DB_NAME);
    if ($connessione->connect_error) {
        die("Connessione fallita (Registratore): " . $connessione->connect_error);
    }
    $connessione->set_charset("utf8mb4");
    return $connessione;
}
?>