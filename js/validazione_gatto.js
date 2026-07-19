// js/validazione_gatto.js

document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("form-gatto");
    
    if (form) {
        form.addEventListener("submit", function(event) {
            const nome = document.getElementById("nome").value.trim();
            const peso = parseFloat(document.getElementById("peso").value);
            const eta = parseInt(document.getElementById("eta").value, 10);
            const dataArrivo = document.getElementById("data_arrivo").value;

            // 1. Controllo testi obbligatori
            if (nome === "") {
                alert("Errore: Il nome del gatto è obbligatorio.");
                event.preventDefault();
                return;
            }

            // 2. Validazione Peso (deve essere un numero positivo verosimile)
            if (isNaN(peso) || peso <= 0 || peso > 20) {
                alert("Errore: Inserisci un peso valido e maggiore di 0 kg (max 20 kg).");
                event.preventDefault();
                return;
            }

            // 3. Validazione Età (espressa in mesi, deve essere maggiore o uguale a 0)
            if (isNaN(eta) || eta < 0) {
                alert("Errore: L'età deve essere un numero intero espresso in mesi (0 o superiore).");
                event.preventDefault();
                return;
            }

            // 4. Validazione Data di Arrivo
            if (!dataArrivo) {
                alert("Errore: Seleziona una data di arrivo valida.");
                event.preventDefault();
                return;
            }
        });
    }
});