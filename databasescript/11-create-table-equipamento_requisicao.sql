USE qrschools;

DROP TABLE IF EXISTS equipamento_requisicao;

CREATE TABLE equipamento_requisicao (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_requesicao INT(11) UNSIGNED NOT NULL,
    id_equipamento INT(11) UNSIGNED NOT NULL,
    id_estado INT(11) UNSIGNED NOT NULL,
    FOREIGN KEY (id_requesicao) REFERENCES estados(id)  ON DELETE CASCADE,
    FOREIGN KEY (id_equipamento) REFERENCES estados(id)  ON DELETE CASCADE,
    FOREIGN KEY (id_estado) REFERENCES estados(id) 
);

INSERT INTO equipamento_requisicao(id_requesicao, id_equipamento, id_estado)
    VALUES
    (1,1,1);