CREATE DATABASE IF NOT EXISTS carpool;
CREATE TABLE IF NOT EXISTS patente(
  numero_patente VARCHAR(20) PRIMARY KEY,
  grado VARCHAR(10) NOT NULL,
  rilasciato DATE NOT NULL,
  scadenza DATE NOT NULL
);
CREATE TABLE IF NOT EXISTS autista(
  id INT PRIMARY KEY AUTO_INCREMENT,
  nome VARCHAR(50) NOT NULL,
  cognome VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  telefono VARCHAR(14) NOT NULL UNIQUE,
  id_patente VARCHAR(20) NOT NULL UNIQUE,
  FOREIGN KEY (id_patente) REFERENCES patente(numero_patente)
);
CREATE TABLE IF NOT EXISTS veicolo(
  targa VARCHAR(20) PRIMARY KEY,
  marca VARCHAR(20) NOT NULL,
  modello VARCHAR(20) NOT NULL,
  alimentazione ENUM('diesel', 'benzina', 'eletrica'),
  numero_posti INT NOT NULL,
  foto BLOB NOT NULL,
  id_autista INT NOT NULL,
  FOREIGN KEY (id_autista) REFERENCES autista(id)
);
CREATE TABLE IF NOT EXISTS caratteristiche(
  id INT PRIMARY KEY AUTO_INCREMENT,
  bagagli BOOL DEFAULT FALSE,
  soste VARCHAR(20) DEFAULT 'none',
  animali BOOL DEFAULT FALSE
);
CREATE TABLE IF NOT EXISTS viaggio(
  id VARCHAR(20),
  id_veicolo VARCHAR(20),
  partenza VARCHAR(20) NOT NULL,
  destinazione VARCHAR(20) NOT NULL,
  data_di_partenza DATETIME NOT NULL,
  contributo INT NOT NULL DEFAULT 0,
  creato_al DATETIME DEFAULT NOW(),
  id_caratteristiche INT NOT NULL,
  PRIMARY KEY (id, id_veicolo),
  FOREIGN KEY (id_veicolo) REFERENCES veicolo(targa),
  FOREIGN KEY (id_caratteristiche) REFERENCES caratteristiche(id)
);
CREATE TABLE IF NOT EXISTS passeggero(
  carta_identita VARCHAR(20) PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  cognome VARCHAR(50) NOT NULL,
  email VARCHAR(50) NOT NULL UNIQUE,
  telefono VARCHAR(14) NOT NULL UNIQUE
);
CREATE TABLE IF NOT EXISTS prenotazione(
  id_passeggero VARCHAR(20),
  id_viaggio VARCHAR(20),
  id_veicolo VARCHAR(20),
  stato ENUM('pending', 'accepted', 'refused') DEFAULT 'pending',
  PRIMARY KEY (id_passeggero, id_viaggio, id_veicolo),
  FOREIGN KEY (id_passeggero) REFERENCES passeggero(carta_identita),
  FOREIGN KEY (id_viaggio, id_veicolo) REFERENCES viaggio(id, id_veicolo)
);
CREATE TABLE IF NOT EXISTS feedback(
  id INT PRIMARY KEY AUTO_INCREMENT,
  da_chi ENUM('autista', 'passeggero') DEFAULT 'passeggero',
  giudizio VARCHAR(255) NOT NULL,
  voto INT NOT NULL,
  id_passeggero VARCHAR(20),
  id_viaggio VARCHAR(20),
  id_veicolo VARCHAR(20),
  FOREIGN KEY (id_passeggero, id_viaggio, id_veicolo) REFERENCES prenotazione(id_passeggero, id_viaggio, id_veicolo)
);