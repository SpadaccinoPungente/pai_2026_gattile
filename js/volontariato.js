// js/volontariato.js

document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("form-volontariato");
    if (!form) return; // Se l'utente non è loggato il form non c'è, interrompiamo lo script

    // 1. Interroghiamo asincronamente l'API per verificare lo stato attuale degli slot[cite: 1]
    fetch("api/get_turni.php")
        .then(response => {
            if (!response.ok) {
                throw new Error("Impossibile recuperare i dati dei turni dal server.");
            }
            return response.json();
        })
        .then(slotOccupati => {
            // Cerchiamo tutti i radio button presenti nel form
            const inputsRadio = form.querySelectorAll("input[type='radio'][name='fascia_oraria']");

            inputsRadio.forEach(radio => {
                const valoreFascia = radio.value;
                // Se la fascia oraria esiste nel JSON, prendiamo il totale degli iscritti, altrimenti è 0
                const iscritti = slotOccupati[valoreFascia] ? slotOccupati[valoreFascia] : 0;
                
                // Troviamo lo span di testo affiancato al radio per aggiornare la dicitura
                const contenitoreLabel = radio.parentElement;
                const indicatoreStato = contenitoreLabel.querySelector(".stato-slot");

                if (iscritti >= 2) {
                    // Se lo slot ha raggiunto o superato il limite di 2 volontari, disabilitiamo l'input[cite: 1]
                    radio.disabled = true;
                    indicatoreStato.innerText = "(❌ Completo - Max 2 volontari)";
                    indicatoreStato.style.color = "#dc3545";
                    contenitoreLabel.style.color = "#aaa"; // Grigio per indicare che non è cliccabile
                } else {
                    // Altrimenti mostriamo i posti ancora disponibili
                    const postiLiberi = 2 - iscritti;
                    indicatoreStato.innerText = "(" + postiLiberi + " posti disponibili)";
                    indicatoreStato.style.color = "#28a745";
                }
            });
        })
        .catch(error => {
            const divErrore = document.getElementById("errore-js");
            divErrore.innerText = "Errore di caricamento: " + error.message;
            divErrore.style.display = "block";
        });
});