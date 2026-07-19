<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once '../includes/db.php';
header('Content-Type: application/json');

// Controllo sessione
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Devi effettuare il login per prenotare un turno.']);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $fascia_oraria = isset($_POST['fascia_oraria']) ? trim($_POST['fascia_oraria']) : '';

    if (empty($fascia_oraria)) {
        echo json_encode(['status' => 'error', 'message' => 'Seleziona una fascia oraria valida.']);
        exit();
    }

    $utente_id = $_SESSION['utente_id'];
    $conn = connetti_modificatore();

    // Controllo di integrità asincrono lato server (Max 2)
    $stmt_check = $conn->prepare("SELECT COUNT(*) FROM turni_volontariato WHERE fascia_oraria = ?");
    $stmt_check->bind_param("s", $fascia_oraria);
    $stmt_check->execute();
    $stmt_check->bind_result($totale_iscritti);
    $stmt_check->fetch();
    $stmt_check->close();

    if ($totale_iscritti >= 2) {
        // Ritorna errore strutturato in JSON (Specifiche esame)
        echo json_encode(['status' => 'error', 'message' => 'Errore: La fascia oraria selezionata ha già raggiunto il limite massimo di 2 volontari.']);
    } else {
        $stmt_insert = $conn->prepare("INSERT INTO turni_volontariato (utente_id, fascia_oraria) VALUES (?, ?)");
        $stmt_insert->bind_param("is", $utente_id, $fascia_oraria);

        if ($stmt_insert->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'Turno di volontariato prenotato con successo!']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Hai già prenotato un turno in questa esatta fascia oraria.']);
        }
        $stmt_insert->close();
    }
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Metodo non consentito.']);
}
?>