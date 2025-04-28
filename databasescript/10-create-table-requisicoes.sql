USE qrschools;

DROP TABLE IF EXISTS requisicoes;

CREATE TABLE requisicoes (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11) UNSIGNED NOT NULL,
    disciplina VARCHAR(255) NOT NULL,
    data_pedido DATETIME NOT NULL,
    data_previsao_entrega DATE NOT NULL,
    data_entrega DATETIME NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id) ON DELETE CASCADE
);

INSERT INTO requisicoes(id_user, disciplina, data_pedido, data_previsao_entrega, data_entrega)
    VALUES
    (1, 'Matematica', '2024-12-09 12:48:00', '2024-12-20', '2024-12-18 15:22:00'),
    (2, 'Portugues', '2024-12-09 13:50:00', '2024-12-20', '2024-12-22 09:20:00'),
    (3, 'PSI', '2024-12-09 15:30:00', '2024-12-20', '2024-12-17 10:50:00');