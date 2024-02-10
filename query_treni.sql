-- Creazione delle tabelle

CREATE TABLE stazione (
    id INT AUTO_INCREMENT,
    nome VARCHAR(30),
    PRIMARY KEY (id)
);

CREATE TABLE tratta (
    id INT AUTO_INCREMENT,
    prima_stazione INT,
    ultima_stazione INT,
    FOREIGN KEY (prima_stazione) REFERENCES stazione(id),
    FOREIGN KEY (ultima_stazione) REFERENCES stazione(id),
    PRIMARY KEY (id)
);

CREATE TABLE treno (
    id VARCHAR(30),
    tratta INT,
    FOREIGN KEY (tratta) REFERENCES tratta(id),
    PRIMARY KEY (id)
);

CREATE TABLE sottotratta (
    id INT AUTO_INCREMENT,
    tratta INT,
    orario_partenza TIME,
    orario_arrivo TIME,
    prima_stazione INT,
    ultima_stazione INT,
    FOREIGN KEY (prima_stazione) REFERENCES stazione(id),
    FOREIGN KEY (ultima_stazione) REFERENCES stazione(id),
    FOREIGN KEY (tratta) REFERENCES tratta(id),
    PRIMARY KEY (id, prima_stazione, ultima_stazione, orario_partenza, orario_arrivo)
);

CREATE TABLE ritardo (
    id INT AUTO_INCREMENT,
    minuti INT,
    treno VARCHAR(30),
    data DATE,
    sottotratta INT,
    FOREIGN KEY (treno) REFERENCES treno(id),
    FOREIGN KEY (sottotratta) REFERENCES sottotratta(id),
    PRIMARY KEY (id, sottotratta)
);


CREATE TABLE macchinista (
    CF VARCHAR(15),
    nome VARCHAR(15),
    cognome VARCHAR(15),
    password VARCHAR(15),
    treno VARCHAR(30),
    FOREIGN KEY (treno) REFERENCES treno(id),
    PRIMARY KEY (CF)
);

CREATE TABLE utente (
    email VARCHAR(50),
    nome VARCHAR(15),
    cognome VARCHAR(15),
    password VARCHAR(15),
    PRIMARY KEY (email)
);

CREATE TABLE biglietto (
    id INT AUTO_INCREMENT,
    prezzo FLOAT,
    disponibilita INT,
    tratta INT,
    data_partenza DATE,
    orario_partenza TIME,
    FOREIGN KEY (tratta) REFERENCES sottotratta(id),
    PRIMARY KEY (id)
);

-- Inserimento dei dati di esempio
-- Inserimento dei dati di esempio per le stazioni
INSERT INTO stazione (nome) VALUES
('Stazione A'),
('Stazione B'),
('Stazione C');

-- Inserimento dei dati di esempio per le tratte
INSERT INTO tratta (prima_stazione, ultima_stazione) VALUES
(1, 2),
(1, 3),
(2, 3);

-- Inserimento dei dati di esempio per i treni
INSERT INTO treno (id, tratta) VALUES
('EXP001', 1),
('REG002', 2),
('FAST003', 3);
('123456', 3);

-- Inserimento dei dati di esempio per le sottotratte
INSERT INTO sottotratta (tratta, orario_partenza, orario_arrivo, prima_stazione, ultima_stazione) VALUES
(1, '08:00:00', '08:30:00', 1, 2),
(1, '08:35:00', '09:00:00', 2, 3),
(2, '09:00:00', '09:30:00', 1, 2),
(2, '09:35:00', '10:00:00', 2, 3),
(3, '10:00:00', '10:30:00', 1, 2),
(3, '10:35:00', '11:00:00', 2, 3);

-- Inserimento dei dati di esempio per i ritardi
INSERT INTO ritardo (minuti, treno, data, sottotratta) VALUES
(10, 'EXP001', '2024-02-09', 1),
(5, 'REG002', '2024-02-10', 2);

-- Inserimento dei dati di esempio per i macchinisti
INSERT INTO macchinista (CF, nome, cognome, password, treno) VALUES
('ABCD12345', 'Mario', 'Rossi', 'password123', 'EXP001'),
('EFGH56789', 'Luca', 'Bianchi', 'qwerty456', 'REG002'),
('bena', 'Leonardo', 'Benatti', '1234', 'REG002');

-- Inserimento dei dati di esempio per gli utenti
INSERT INTO utente (email, nome, cognome, password) VALUES
('user1@example.com', 'Giulia', 'Verdi', 'user123'),
('user2@example.com', 'Marco', 'Neri', 'pass456');

-- Inserimento dei dati di esempio per i biglietti
INSERT INTO biglietto (prezzo, disponibilita, tratta, data_partenza, orario_partenza) VALUES
(25.50, 50, 1, '2024-02-15', '08:00:00'),
(15.75, 30, 2, '2024-02-17', '10:30:00');
