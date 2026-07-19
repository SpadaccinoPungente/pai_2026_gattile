// js/validazioni.js

document.addEventListener("DOMContentLoaded", function() {
    const form = document.getElementById("form-registrazione");
    
    if (form) {
        form.addEventListener("submit", function(event) {
            // Recuperiamo i valori inseriti nei campi
            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value;
            const confermaPassword = document.getElementById("conferma_password").value;

            // 1. Vincolo Username: deve cominciare con un carattere alfabetico
            const usernameRegex = /^[a-zA-Z]/;
            if (!usernameRegex.test(username)) {
                alert("Errore: Lo username deve iniziare con una lettera alfabetica.");
                event.preventDefault(); // Blocca l'invio del form
                return;
            }

            // 2. Vincolo Lunghezza Password: da 8 a 16 caratteri
            if (password.length < 8 || password.length > 16) {
                alert("Errore: La password deve essere lunga da 8 a 16 caratteri.");
                event.preventDefault();
                return;
            }

            // 3. Vincolo Complessità Password: almeno una maiuscola, una minuscola, un numero e un carattere speciale
            // Spiegazione regex: 
            // (?=.*[a-z]) -> almeno una minuscola
            // (?=.*[A-Z]) -> almeno una maiuscola
            // (?=.*[0-9]) -> almeno un numero
            // (?=.*[!@#$%^&*(),.?\":{}|<>]) -> almeno un carattere speciale
            const passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*[0-9])(?=.*[!@#$%^&*(),.?\":{}|<>])/;
            if (!passwordRegex.test(password)) {
                alert("Errore: La password deve contenere almeno una lettera maiuscola, una minuscola, un numero e un carattere speciale.");
                event.preventDefault();
                return;
            }

            // 4. Vincolo Coincidenza Password
            if (password !== confermaPassword) {
                alert("Errore: La password e la conferma password non coincidono.");
                event.preventDefault();
                return;
            }
        });
    }
});