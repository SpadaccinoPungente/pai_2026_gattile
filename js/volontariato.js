document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("form-volontariato");
    const divErrore = document.getElementById("errore-js");
    const radioButtons = document.querySelectorAll('input[name="fascia_oraria"]');

    // Funzione che interroga il server per verificare il numero di iscritti e disabilita le fasce
    function aggiornaDisponibilita() {
        fetch("api/get_turni.php")
            .then(response => response.json())
            .then(data => {
                radioButtons.forEach(radio => {
                    const conteggio = data[radio.value] || 0;
                    const spanStato = radio.nextElementSibling;
                    
                    if (conteggio >= 2) {
                        radio.disabled = true; // Impedisce la sottomissione disabilitandolo visivamente
                        if (spanStato) spanStato.textContent = " (Completo)";
                        spanStato.style.color = "red";
                    } else {
                        radio.disabled = false;
                        if (spanStato) spanStato.textContent = ` (${conteggio}/2 iscritti)`;
                        spanStato.style.color = "green";
                    }
                });
            })
            .catch(err => console.error("Errore recupero turni:", err));
    }

    // Eseguiamo al caricamento per blindare le fasce piene
    aggiornaDisponibilita();

    if (form) {
        form.addEventListener("submit", function(event) {
            event.preventDefault(); // Blocchiamo il refresh e gestiamo via Fetch API
            
            const formData = new FormData(form);
            const fasciaOraria = formData.get("fascia_oraria");

            if (!fasciaOraria) {
                mostraMessaggio("errore", "Seleziona una fascia oraria valida.");
                return;
            }

            fetch("api/prenota_turno.php", {
                method: "POST",
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === "error") {
                    mostraMessaggio("errore", data.message);
                } else if (data.status === "success") {
                    mostraMessaggio("successo", data.message);
                    form.reset();
                    aggiornaDisponibilita(); // Ricarichiamo le disponibilità aggiornate
                }
            })
            .catch(error => {
                console.error("Errore fetch:", error);
                mostraMessaggio("errore", "Errore di connessione al server.");
            });
        });
    }

    function mostraMessaggio(tipo, testo) {
        if (!divErrore) return;
        divErrore.textContent = testo;
        divErrore.classList.remove("hidden-alert", "alert-success", "alert-danger");
        
        if (tipo === "errore") {
            divErrore.classList.add("alert-danger");
        } else {
            divErrore.classList.add("alert-success");
        }
    }
});