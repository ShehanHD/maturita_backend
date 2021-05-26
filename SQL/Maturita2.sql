CREATE DATABASE IF NOT EXISTS carpool;

-- autista e passeggero tutti i due utenti possiamo trovare nella tabella
-- in questa tabella ho aggiunto il campo password perchè mi serviva a fare login
-- poi la patente aggiungo come chiave esterna
CREATE TABLE IF NOT EXISTS utente(
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  cognome VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  telefono VARCHAR(14) NOT NULL UNIQUE,
  carta_identita VARCHAR(20) NOT NULL UNIQUE
);

-- la tabella autista è un risultato di normalizzazione della tabella utente
CREATE TABLE IF NOT EXISTS autista(
  id_utente INT NOT NULL PRIMARY KEY,
  numero_patente VARCHAR(255) NOT NULL UNIQUE,
  grado VARCHAR(10) NOT NULL,
  rilasciato DATE NOT NULL,
  scadenza DATE NOT NULL,
  FOREIGN KEY (id_utente) REFERENCES utente(id)
);


-- qui discrive il veicolo che viene usata nel viaggio
-- autista e la chiave esterna
-- uso (targa, id_autista) come un primary key per tenere la relazione  di uno a molti(cosi un veicolo non puo assegnare ai due autisti o a stesso autista)
CREATE TABLE IF NOT EXISTS veicolo(
  targa VARCHAR(20),
  marca VARCHAR(20) NOT NULL,
  modello VARCHAR(20) NOT NULL,
  alimentazione ENUM('diesel', 'benzina', 'eletrica'),
  numero_posti INT NOT NULL,
  aria_condizionata ENUM('si', 'no'),
  foto BLOB NOT NULL,
  id_autista INT NOT NULL,
  PRIMARY KEY (targa, id_autista),
  FOREIGN KEY (id_autista) REFERENCES autista(id_utente)
);

-- id primario non viene creata da solo, ma il sistema genera un chiave significatva
-- in questa tabella ho collegato con il veicolo, cosi io tengo le chiavi (id_veicolo, id_autista) come chiavi esterne
CREATE TABLE IF NOT EXISTS viaggio(
  id VARCHAR(20) PRIMARY KEY,
  id_autista INT NOT NULL,
  id_veicolo VARCHAR(20) NOT NULL,
  partenza VARCHAR(20) NOT NULL,
  destinazione VARCHAR(20) NOT NULL,
  durata DOUBLE NOT NULL,
  data_di_partenza DATETIME NOT NULL,
  contributo INT NOT NULL DEFAULT 0,
  creato_al DATETIME DEFAULT NOW(),
  stato ENUM('Completed', 'Not Completed') DEFAULT 'Not Completed',
  bagagli BOOL DEFAULT FALSE,
  soste VARCHAR(20) DEFAULT 'none',
  animali BOOL DEFAULT FALSE,
  FOREIGN KEY (id_veicolo, id_autista) REFERENCES veicolo(targa, id_autista)
);

-- questa tabella collega con altre due tabelle, che sono utenti(passeggero) e il viaggio
-- poi creo un chiave unica per dare un vincolo al passeggero, cosi non possono prenotare stesso viaggio due volte
CREATE TABLE IF NOT EXISTS prenotazione(
  id INT AUTO_INCREMENT PRIMARY KEY,
  id_passeggero INT NOT NULL,
  id_viaggio VARCHAR(20) NOT NULL,
  stato ENUM('pending', 'accepted', 'refused') NOT NULL DEFAULT 'pending',
  UNIQUE (id_passeggero, id_viaggio),
  FOREIGN KEY (id_passeggero) REFERENCES utente(id),
  FOREIGN KEY (id_viaggio) REFERENCES viaggio(id)
);

CREATE TABLE IF NOT EXISTS feedback(
  id INT PRIMARY KEY AUTO_INCREMENT,
  da_chi ENUM('autista', 'passeggero') DEFAULT 'passeggero',
  giudizio VARCHAR(255) NOT NULL,
  voto INT NOT NULL,
  id_viaggio VARCHAR(20),
  id_autista INT NOT NULL,
  id_passeggero INT NOT NULL,
  UNIQUE (id_autista, id_viaggio, id_passeggero, da_chi),
  FOREIGN KEY (id_viaggio) REFERENCES viaggio(id),
  FOREIGN KEY (id_passeggero) REFERENCES utente(id),
  FOREIGN KEY (id_autista) REFERENCES autista(id_utente)
);