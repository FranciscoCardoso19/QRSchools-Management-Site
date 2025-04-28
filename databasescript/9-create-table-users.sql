USE qrschools;

DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_cargo INT(11) UNSIGNED NOT NULL DEFAULT 2,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(100) NOT NULL,
    FOREIGN KEY (id_cargo) REFERENCES cargos(id) ON DELETE CASCADE
);

INSERT INTO users(id_cargo, name, email, password)
    VALUES
        (1, 'Francisco', 'francisco@gmail.com', 'francisco123'),
        (1, 'André', 'andre@gmail.com', 'andre123'),
        (1, 'Gonçalo', 'goncalo@gmail.com', 'goncalo123');