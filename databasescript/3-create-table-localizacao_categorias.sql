USE qrschools;

DROP TABLE IF EXISTS localizacao_categorias;

CREATE TABLE localizacao_categorias(
    id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(40) NOT NULL,
    referencia VARCHAR(5) NOT NULL,
    tamanho VARCHAR(20) NOT NULL,
    mobilidade VARCHAR(20) NOT NULL,
    abertura VARCHAR(50) NOT NULL,
    num_portas VARCHAR(20),
    material VARCHAR(20) NOT NULL,
    cor VARCHAR(20),
    forma VARCHAR(20)
);

INSERT INTO localizacao_categorias (nome, referencia, tamanho, mobilidade, abertura, num_portas, material, cor, forma)
    VALUES
    ('Arm. Bancada simples', 'AB', 'Baixo', 'Bancada', 'portas', 1, 'Madeira', NULL, 'Prateleiras'),
    ('Arm. Bancada triplo', 'AB', 'Baixo','Bancada', 'portas', 3, 'Madeira', NULL, 'Prateleiras'),
    ('Arm. Bancada duplo', 'AB', 'Baixo','Bancada', 'portas', 2, 'Madeira', NULL, 'Prateleiras'),
    ('Arm. Alto cacifo', 'AC', 'Alto','Fixo', 'Cacifo', 15, 'Madeira', NULL, 'Portas'),
    ('Arm. Alto portas correr', 'AF', 'Alto','Fixo', 'Portas Corerr plastico', 1, 'Plastico', NULL, 'Prateleiras'),
    ('Arm. Alto portas vidro', 'AV', 'Alto','Fixo', 'Portas Correr vidro', 2, 'Metal', NULL, 'Prateleiras'),
    ('Arm. Alto madeira', 'AF', 'Alto','Fixo', 'Portas', 2, 'Madeira', NULL, 'Prateleiras'),
    ('Arm. Baixo porta vidro', 'BV', 'Baixo','Fixo', 'Portas Correr vidro', 1, 'Metal', NULL, 'Prateleiras'),
    ('Arm. Baixo de tabuleiros c/ porta', 'BT', 'Médio','Fixo', 'Portas', 2, 'Madeira', NULL, 'Tabuleiros'),
    ('Arm. Baixo de tabuleiros s/ porta', 'ST', 'Baixo','Fixo', 's/ portas', NULL, 'Madeira', NULL, 'Tabuleiros'),
    ('Arm. Baixo', 'BF', 'Baixo','Fixo', 'Portas', 2, 'Madeira', NULL, 'Prateleiras'),
    ('Arm. Metal movel', 'BM', 'Medio','Movel', 'Portas', 2, 'Metal', NULL, NULL),
    ('Arm. Gavetas movel', 'GM', 'Medio','Movel', 's/ portas', NULL, 'Madeira', NULL, 'Gavetas'),
    ('Arm. PC Portateis', 'AE', 'Medio','Movel', 'Portas', 2, 'Metal', NULL, 'Prateleiras');