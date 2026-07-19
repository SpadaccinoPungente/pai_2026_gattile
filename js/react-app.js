// js/react-app.js

function AppAdozioni() {
    // Stati per i dati e la gestione del caricamento/errori
    const [gatti, setGatti] = React.useState([]);
    const [caricamento, setCaricamento] = React.useState(true);
    const [errore, setErrore] = React.useState(null);

    // Stati per i filtri di ricerca e l'ordinamento delle card
    const [ricerca, setRicerca] = React.useState("");
    const [ordinamento, setOrdinamento] = React.useState("data_arrivo");

    // Stato per memorizzare gli ID dei gatti selezionati dall'utente
    const [selezionati, setSelezionati] = React.useState([]);

    // Lettura della variabile globale definita in PHP per verificare lo stato di login[cite: 1]
    const isLoggato = window.APP_CONFIG ? window.APP_CONFIG.isLoggato : false;

    // 1. Caricamento asincrono iniziale dei dati dei gatti dall'API JSON[cite: 1]
    React.useEffect(() => {
        fetch("api/get_gatti.php")
            .then(response => {
                if (!response.ok) {
                    throw new Error("Impossibile caricare i dati dei felini.");
                }
                return response.json();
            })
            .then(data => {
                setGatti(data);
                setCaricamento(false);
            })
            .catch(err => {
                setErrore(err.message);
                setCaricamento(false);
            });
    }, []);

    // 2. Comunicazione verso Vanilla JS tramite CustomEvent[cite: 1]
    // Ogni volta che lo stato 'selezionati' cambia, notifichiamo il document[cite: 1]
    React.useEffect(() => {
        const evento = new CustomEvent("gattiSelezionatiCambiati", {
            detail: { idGatti: selezionati }
        });
        document.dispatchEvent(evento);
    }, [selezionati]);

    // 3. Gestione del click sulla card (attiva solo se l'utente è autenticato)[cite: 1]
    const gestisciSelezione = (id) => {
        if (!isLoggato) return; // I visitatori non autenticati possono solo visualizzare[cite: 1]

        setSelezionati(prevSelezionati => {
            if (prevSelezionati.includes(id)) {
                return prevSelezionati.filter(gattoId => gattoId !== id); // Rimuove se già presente
            } else {
                return [...prevSelezionati, id]; // Aggiunge se non presente
            }
        });
    };

    // 4. Logica di filtraggio in tempo reale (opera per nome o descrizione)[cite: 1]
    const gattiFiltrati = gatti.filter(gatto => {
        const termine = ricerca.toLowerCase();
        return (
            gatto.nome.toLowerCase().includes(termine) ||
            gatto.descrizione.toLowerCase().includes(termine)
        );
    });

    // 5. Logica di ordinamento (in base a età, colore del mantello o data di arrivo)[cite: 1]
    const gattiOrdinati = [...gattiFiltrati].sort((a, b) => {
        if (ordinamento === "eta") {
            return a.eta - b.eta;
        }
        if (ordinamento === "colore_mantello") {
            return a.colore_mantello.localeCompare(b.colore_mantello);
        }
        if (ordinamento === "data_arrivo") {
            return new Date(a.data_arrivo) - new Date(b.data_arrivo);
        }
        return 0;
    });

    // Gestione degli stati visivi di caricamento o errore transitori
    if (caricamento) return <div>Caricamento dei gattini in corso...</div>;
    if (errore) return <div style={{ color: "red" }}>Errore: {errore}</div>;

    return (
        <div>
            {/* Pannello di Controllo: Filtro di Ricerca e Selezione Ordinamento[cite: 1] */}
            <div style={{ display: "flex", gap: "20px", marginBottom: "20px", background: "#f5f5f5", padding: "15px", borderRadius: "6px" }}>
                <div style={{ flex: "1" }}>
                    <label htmlFor="cerca-gatto" style={{ fontWeight: "bold", display: "block", marginBottom: "5px" }}>Cerca per nome o descrizione:</label>
                    <input
                        id="cerca-gatto"
                        type="text"
                        placeholder="Es: Tigrato, Fuffi..."
                        value={ricerca}
                        onChange={(e) => setRicerca(e.target.value)}
                        style={{ width: "100%", padding: "8px", borderRadius: "4px", border: "1px solid #ccc" }}
                    />
                </div>
                <div>
                    <label htmlFor="ordina-gatto" style={{ fontWeight: "bold", display: "block", marginBottom: "5px" }}>Ordina per:</label>
                    <select
                        id="ordina-gatto"
                        value={ordinamento}
                        onChange={(e) => setOrdinamento(e.target.value)}
                        style={{ padding: "8px", borderRadius: "4px", border: "1px solid #ccc" }}
                    >
                        <option value="data_arrivo">Data di Arrivo</option>
                        <option value="eta">Età</option>
                        <option value="colore_mantello">Colore del pelo</option>
                    </select>
                </div>
            </div>

            {/* Contenitore a Griglia delle Card dei Gatti[cite: 1] */}
            <div style={{ display: "flex", flexWrap: "wrap", gap: "20px" }}>
                {gattiOrdinati.length === 0 ? (
                    <p>Nessun felino corrisponde ai criteri di ricerca selezionati.</p>
                ) : (
                    gattiOrdinati.map(gatto => {
                        const isSelezionato = selezionati.includes(gatto.id);
                        return (
                            <div
                                key={gatto.id}
                                onClick={() => gestisciSelezione(gatto.id)}
                                style={{
                                    border: isSelezionato ? "3px solid #007bff" : "1px solid #ccc",
                                    borderRadius: "8px",
                                    padding: "15px",
                                    width: "220px",
                                    cursor: isLoggato ? "pointer" : "default",
                                    backgroundColor: isSelezionato ? "#e6f2ff" : "#fff",
                                    transition: "all 0.2s ease"
                                }}
                            >
                                {/* Immagine placeholder standard richiesta dall'esame[cite: 1] */}
                                <div style={{ background: "#e0e0e0", height: "130px", display: "flex", alignItems: "center", justifyConent: "center", borderRadius: "4px", marginBottom: "10px", fontSize: "45px" }}>
                                    🐱
                                </div>
                                <h3 style={{ margin: "0 0 10px 0" }}>{gatto.nome}</h3>
                                <p style={{ fontSize: "14px", color: "#555", height: "60px", overflow: "hidden" }}>{gatto.descrizione}</p>
                                <div style={{ fontSize: "12px", color: "#777", marginTop: "10px" }}>
                                    <div><strong>Età:</strong> {gatto.eta} mesi</div>
                                    <div><strong>Pelo:</strong> {gatto.lunghezza_pelo} ({gatto.colore_mantello})</div>
                                    <div><strong>Razza:</strong> {gatto.razza}</div>
                                </div>
                                {isLoggato && (
                                    <div style={{ marginTop: "10px", textAlign: "right", color: "#007bff", fontWeight: "bold", fontSize: "12px" }}>
                                        {isSelezionato ? "★ Selezionato" : "Clicca per selezionare"}
                                    </div>
                                )}
                            </div>
                        );
                    })
                )}
            </div>
        </div>
    );
}

// Inizializzazione e montaggio definitivo del componente all'interno del DOM di PHP[cite: 1]
const root = ReactDOM.createRoot(document.getElementById("react-adozioni-root"));
root.render(<AppAdozioni />);