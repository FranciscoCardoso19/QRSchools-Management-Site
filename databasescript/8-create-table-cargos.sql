USE qrschools;

DROP TABLE IF EXISTS cargos;

CREATE TABLE cargos(
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(20) NOT NULL
);

INSERT INTO cargos(nome)
    VALUES
    ('Administrador'),
    ('Professor');