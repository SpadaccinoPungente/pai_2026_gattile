<?php
// api/get_gatti.php

// 1. Impostiamo gli header HTTP per comunicare al browser che la risposta sarà un JSON
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Origin: *"); // Permette l'accesso asincrono
header("Access-Control-Allow-Methods: GET");

// 2. Includiamo le funzioni di connessione al database
// Usiamo il percorso relativo corretto per risalire alla cartella includes
require_once '../includes/db.php';

try {
    // Connessione con l'utente database "lettore" (ha solo privilegi di SELECT)
    $conn = connetti_lettore();
    
    // Query per estrarre tutti i gatti presenti nella struttura
    $query = "SELECT id, nome, descrizione, peso, colore_mantello, lunghezza_pelo, razza, colore_occhi, eta, sesso, data_arrivo FROM gatti";
    $risultato = $conn->query($query);
    
    $gatti = array();
    
    if ($risultato) {
        while ($riga = $risultato->fetch_assoc()) {
            // Convertiamo i tipi di dato nativi numerici per evitare che JavaScript li legga come stringhe
            $riga['id'] = intval($riga['id']);
            $riga['peso'] = floatval($riga['peso']);
            $riga['eta'] = intval($riga['eta']);
            
            $gatti[] = $riga;
        }
        
        // Risposta con codice HTTP 200 (OK) e l'array convertito in JSON
        http_response_code(200);
        echo json_encode($gatti, JSON_PRETTY_PRINT);
    } else {
        throw new Exception("Errore nell'esecuzione della query.");
    }
    
    $conn->close();

} catch (Exception $e) {
    // In caso di errore del server, restituiamo un codice 500 e un messaggio di errore strutturato in JSON
    http_response_code(500);
    echo json_encode(array(
        "errore" => true,
        "messaggio" => $e->getMessage()
    ));
}
?>