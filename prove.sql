-- Active: 1707411005403@@127.0.0.1@3306@gestione_treni

-- trovare i ritardi di un treno partendo da ritardo

SELECT r.minuti, r.treno, r.data_tratta, r.orario_tratta, s1.nome AS "partenza", s2.nome AS "partenza" FROM ritardo r
LEFT JOIN treno t ON r.treno = t.id
LEFT JOIN tratta ON t.tratta = tratta.id
LEFT JOIN stazione s1 ON tratta.prima_stazione = s1.id
LEFT JOIN stazione s2 ON tratta.ultima_stazione = s2.id
WHERE t.id = "Frecciarossa 1000";

-- trovare gli orari disponibili e i ritardi da una sottotratta (partenza e destinazione)
SELECT * FROM sottotratta 
LEFT JOIN ritardo ON sottotratta.id = ritardo.sottotratta
WHERE prima_stazione = (SELECT id FROM stazione WHERE nome = "Stazione B")
AND ultima_stazione = (SELECT id FROM stazione WHERE nome = "Stazione C")

-- login utente
SELECT * FROM utente WHERE email = "leo" AND password = "1234"

SELECT nome FROM stazione WHERE id = 5