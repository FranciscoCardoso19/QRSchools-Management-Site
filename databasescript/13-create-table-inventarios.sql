USE qrschools;

DROP TABLE IF EXISTS inventarios;

CREATE TABLE inventarios (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_user INT(11) UNSIGNED NOT NULL,
    data_inventario DATE NOT NULL,
    FOREIGN KEY (id_user) REFERENCES users(id)
);

INSERT INTO inventarios(id_user, data_inventario)
    VALUES
    (1,'2025-03-26');