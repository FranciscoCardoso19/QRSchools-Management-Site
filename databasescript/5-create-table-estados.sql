USE qrschools;

DROP TABLE IF EXISTS estados;

CREATE TABLE estados (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    estado ENUM('Disponivel','Em manutenção','Avariado') DEFAULT 'Disponivel'
);

INSERT INTO estados(estado)
    VALUES
    ('Disponivel'),
    ('Em manutenção'),
    ('Avariado');