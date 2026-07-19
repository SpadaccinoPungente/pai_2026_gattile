// js/prenotazioni_visita.js

document.addEventListener("DOMContentLoaded", function() {
    // Recuperiamo gli elementi del form Vanilla JS dal DOM di gatti.php
    const inputNascosto = document.getElementById("gatti_selezionati_input");
    const displayVisivo = document.getElementById("elenco-gatti-selezionati-visivo");
    const btnInvia = document.getElementById("btn-invia-prenotazione");

    // Ci iscriviamo all'evento personalizzato globale generato da React
    document.addEventListener("gattiSelezionatiCambiati", function(event) {
        // Recuperiamo l'array degli ID dei gatti dal dettaglio dell'evento
        const idGattiSelezionati = event.detail.idGatti;

        if (idGattiSelezionati && idGattiSelezionati.length > 0) {
            // 1. Aggiorniamo l'input hidden convertendo l'array in una stringa separata da virgole (es: "1,2,4")
            inputNascosto.value = idGattiSelezionati.join(",");

            // 2. Aggiorniamo il testo visivo per dare un feedback all'utente
            if (idGattiSelezionati.length === 1) {
                displayVisivo.innerText = "1 gatto selezionato (Pronto per la prenotazione)";
            } else {
                displayVisivo.innerText = idGattiSelezionati.length + " gatti selezionati (Pronto per la prenotazione)";
            }

            // 3. Attiviamo il pulsante di sottomissione del form
            btnInvia.disabled = false;
            btnInvia.style.backgroundColor = "#007bff"; // Cambia in blu
            btnInvia.style.cursor = "pointer";
        } else {
            // Se l'array è vuoto, resettiamo lo stato del form al default di sicurezza
            inputNascosto.value = "";
            displayVisivo.innerText = "Nessun gatto selezionato dalla lista sopra.";
            
            btnInvia.disabled = true;
            btnInvia.style.backgroundColor = "#6c757d"; // Grigio disabilitato
            btnInvia.style.cursor = "not-allowed";
        }
    });
});