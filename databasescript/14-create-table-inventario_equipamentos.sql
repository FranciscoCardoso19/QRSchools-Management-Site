USE qrschools;

DROP TABLE IF EXISTS inventario_equipamentos;

CREATE TABLE inventario_equipamentos (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_inventario INT(11) UNSIGNED NOT NULL,
    id_equipamento INT(11) UNSIGNED NOT NULL,
    id_user INT(11) UNSIGNED NOT NULL,
    checkado TINYINT UNSIGNED NOT NULL,
    local_correcto TINYINT UNSIGNED NOT NULL,
    estado VARCHAR(20) NOT NULL,
    observacoes TEXT,
    FOREIGN KEY (id_user) REFERENCES users(id),
    FOREIGN KEY (id_inventario) REFERENCES inventarios(id),
    FOREIGN KEY (id_equipamento) REFERENCES equipamentos(id)
);

INSERT INTO inventario_equipamentos(id_inventario, id_equipamento, id_user, checkado, local_correcto, estado, observacoes)
    VALUES
    (1, 1, 1, 0, 0, 'Bom estado', 'Nada a apontar');