-- Active: 1707218068252@@127.0.0.1@3306@gestione_treni

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
    id INT,
    tratta INT,
    orario_partenza TIME,
    orario_arrivo TIME,
    prima_stazione INT,
    ultima_stazione INT,
    sottotratta_successiva INT,
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
INSERT INTO sottotratta (id, tratta, orario_partenza, orario_arrivo, prima_stazione, ultima_stazione, sottotratta_successiva) VALUES
(7, 4, '15:10:00', '15:28:00', 4, 5, 8),
(8, 4, '15:28:00', '15:41:00', 5, 6, 9),
(9, 4, '15:41:00', '16:03:00', 6, 7, 10),
(10, 4, '16:03:00', '16:28:00', 7, 8, 11),
(11, 4, '16:28:00', '16:35:00', 8, 9, 12),
(12, 4, '16:35:00', '16:53:00', 9, 10, 13),
(13, 4, '16:53:00', '17:10:00', 10, 11, 14),
(14, 4, '17:10:00', '17:20:00', 11, 12, NULL);

INSERT INTO sottotratta (id, tratta, orario_partenza, orario_arrivo, prima_stazione, ultima_stazione, sottotratta_successiva) VALUES
(15, 4, '18:10:00', '18:28:00', 4, 5, 16),
(16, 4, '18:28:00', '18:41:00', 5, 6, 17),
(17, 4, '18:41:00', '19:03:00', 6, 7, 18),
(18, 4, '19:03:00', '19:28:00', 7, 8, 19),
(19, 4, '19:28:00', '19:35:00', 8, 9, 20),
(20, 4, '19:35:00', '19:53:00', 9, 10, 21),
(21, 4, '19:53:00', '20:10:00', 10, 11, 22),
(22, 4, '20:10:00', '20:20:00', 11, 12, NULL);

INSERT INTO sottotratta (id, tratta, orario_partenza, orario_arrivo, prima_stazione, ultima_stazione, sottotratta_successiva) VALUES
(23, 5, '06:50:00', '07:03:00', 4, 13, 24),
(24, 5, '07:03:00', '07:07:00', 13, 14, 25),
(25, 5, '07:07:00', '07:12:00', 14, 15, 26),
(26, 5, '07:12:00', '07:18:00', 15, 5, 27),
(27, 5, '07:18:00', '07:24:00', 5, 16, 28),
(28, 5, '07:24:00', '07:29:00', 16, 17, 29),
(29, 5, '07:29:00', '07:40:00', 17, 6, NULL);

ALTER TABLE sottotratta
ADD CONSTRAINT fk_sottotratta_successiva
FOREIGN KEY (sottotratta_successiva)
REFERENCES sottotratta(id);


-- Inserimento dei dati di esempio per i ritardi
INSERT INTO ritardo (minuti, treno, data, sottotratta) VALUES
(10, 'EXP001', '2024-02-09', 7),
(5, 'REG002', '2024-02-10', 8);

-- Inserimento dei dati di esempio per i capotreno
INSERT INTO capotreno (CF, nome, cognome, password, treno) VALUES
('ABCD12345', 'Mario', 'Rossi', 'password123', 'EXP001'),
('EFGH56789', 'Luca', 'Bianchi', 'qwerty456', 'REG002');

-- Inserimento dei dati di esempio per gli utenti
INSERT INTO utente (email, nome, cognome, password) VALUES
('user1@example.com', 'Giulia', 'Verdi', 'user123'),
('user2@example.com', 'Marco', 'Neri', 'pass456');
