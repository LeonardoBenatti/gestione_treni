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


CREATE TABLE capotreno (
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
('Stazione C'),
('bologna'),
('san pietro in c'),
('ferrara'),
('rovigo'),
('monselice'),
('termee euganee-abano-montegrotto'),
('padova'),
('venezia mestre'),
('venezia santa lucia');
INSERT INTO stazione (nome) VALUES
('castel maggiore'),
('funo'),
('san giorgio di piano'),
('galliera'),
('poggio renatico'),
('coronella');


-- Inserimento dei dati di esempio per le tratte
INSERT INTO tratta (prima_stazione, ultima_stazione) VALUES
(1, 2),
(1, 3),
(2, 3),
(4, 12),
(4, 6);


-- Inserimento dei dati di esempio per i treni
INSERT INTO treno (id, tratta) VALUES
('EXP001', 1),
('REG002', 2),
('FAST003', 3);

-- Inserimento dei dati di esempio per le sottotratte
INSERT INTO sottotratta (tratta, orario_partenza, orario_arrivo, prima_stazione, ultima_stazione) VALUES
(1, '08:00:00', '08:30:00', 1, 2),
(1, '08:35:00', '09:00:00', 2, 3),
(2, '09:00:00', '09:30:00', 1, 2),
(2, '09:35:00', '10:00:00', 2, 3),
(3, '10:00:00', '10:30:00', 1, 2),
(3, '10:35:00', '11:00:00', 2, 3);

INSERT INTO sottotratta (tratta, orario_partenza, orario_arrivo, prima_stazione, ultima_stazione) VALUES
(4, '15:10:00', '15:28:00', 4, 5),
(4, '15:28:00', '15:41:00', 5, 6),
(4, '15:41:00', '16:03:00', 6, 7),
(4, '16:03:00', '16:28:00', 7, 8),
(4, '16:28:00', '16:35:00', 8, 9),
(4, '16:35:00', '16:53:00', 9, 10),
(4, '16:53:00', '17:10:00', 10, 11),
(4, '17:10:00', '17:20:00', 11, 12);

INSERT INTO sottotratta (tratta, orario_partenza, orario_arrivo, prima_stazione, ultima_stazione) VALUES
(5, '06:50:00', '07:03:00', 4, 13),
(5, '07:03:00', '07:07:00', 13, 14),
(5, '07:07:00', '07:12:00', 14, 15),
(5, '07:12:00', '07:18:00', 15, 5),
(5, '07:18:00', '07:24:00', 5, 16),
(5, '07:24:00', '07:29:00', 16, 17),
(5, '07:29:00', '07:40:00', 17, 6);

-- Inserimento dei dati di esempio per i ritardi
INSERT INTO ritardo (minuti, treno, data, sottotratta) VALUES
(10, 'EXP001', '2024-02-09', 1),
(5, 'REG002', '2024-02-10', 2);

-- Inserimento dei dati di esempio per i capotreno
INSERT INTO capotreno (CF, nome, cognome, password, treno) VALUES
('ABCD12345', 'Mario', 'Rossi', 'password123', 'EXP001'),
('EFGH56789', 'Luca', 'Bianchi', 'qwerty456', 'REG002');

-- Inserimento dei dati di esempio per gli utenti
INSERT INTO utente (email, nome, cognome, password) VALUES
('user1@example.com', 'Giulia', 'Verdi', 'user123'),
('user2@example.com', 'Marco', 'Neri', 'pass456');

-- Inserimento dei dati di esempio per i biglietti
INSERT INTO biglietto (prezzo, disponibilita, tratta, data_partenza, orario_partenza) VALUES
(25.50, 50, 1, '2024-02-15', '08:00:00'),
(15.75, 30, 2, '2024-02-17', '10:30:00');
