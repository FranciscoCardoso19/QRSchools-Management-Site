USE qrschools;

DROP TABLE IF EXISTS equipamento_categorias;

CREATE TABLE equipamento_categorias(
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(40) NOT NULL
);

INSERT INTO equipamento_categorias (nome)
    VALUES
    ('Placas De Desenvolvimento'),
    ('Cartão De Memoria'),
    ('Pilha'),
    ('Robot'),
    ('Sensor'),
    ('Microcontrolador'),
    ('Display'),
    ('Proteção'),
    ('Armário');