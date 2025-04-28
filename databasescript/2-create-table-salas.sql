USE qrschools;

DROP TABLE IF EXISTS salas;

CREATE TABLE salas(
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE,
    piso TINYINT UNSIGNED NOT NULL
);

INSERT INTO salas(nome, piso)
    VALUES('CTE 1', 1),
        ('CTE 2', 1),
        ('CTE 3', 1),
        ('CTE 4', 0),
        ('CTE 5', 0),
        ('CTE 6', 0),
        ('CTE MF1', 1),
        ('CTE MF2', 1),
        ('CTE MF3', 1);