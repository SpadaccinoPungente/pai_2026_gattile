<?php
require_once '../includes/db.php';
header('Content-Type: application/json');

$conn = connetti_lettore();
// Contiamo gli iscritti per ogni fascia oraria
$query = "SELECT fascia_oraria, COUNT(*) AS iscritti FROM turni_volontariato GROUP BY fascia_oraria";
$risultato = $conn->query($query);

$turni = [];
if ($risultato) {
    while ($riga = $risultato->fetch_assoc()) {
        $turni[$riga['fascia_oraria']] = intval($riga['iscritti']);
    }
}

echo json_encode($turni);
$conn->close();
?>