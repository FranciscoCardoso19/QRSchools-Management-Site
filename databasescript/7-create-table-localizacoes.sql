USE qrschools;

DROP TABLE IF EXISTS localizacoes;

CREATE TABLE localizacoes(
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_sala INT(11) UNSIGNED NOT NULL,
    id_parent_location INT(11) UNSIGNED,
    id_equipamento INT(11) UNSIGNED,
    id_localizacao_categoria INT(11) UNSIGNED NOT NULL,
    nome VARCHAR(100) NOT NULL,
    descricao VARCHAR(255), 
    FOREIGN KEY(id_sala) REFERENCES salas(id) ON DELETE CASCADE,
    FOREIGN KEY(id_parent_location) REFERENCES localizacoes(id) ON DELETE CASCADE,
    FOREIGN KEY(id_equipamento) REFERENCES equipamentos(id) ON DELETE CASCADE,
    FOREIGN KEY(id_localizacao_categoria) REFERENCES localizacao_categorias(id) ON DELETE CASCADE
);

INSERT INTO localizacoes(id, id_sala, id_parent_location, id_equipamento, id_localizacao_categoria, nome, descricao)
    VALUES
    -- CTE1
    (1 ,1, NULL, NULL, 1, 'Armário Baixo porta vidro', NULL),
    (2, 1, NULL, 1, 1, 'Armário Baixo-01', NULL),
    (3, 1, NULL, 2, 1, 'Armário Baixo-02', NULL),
    (4, 1, NULL, NULL, 1,'Armário Metal movel', NULL),
    (5, 1, NULL, NULL, 1,'Armário Gavetas movel', NULL);