-- Criar banco de dados
CREATE DATABASE IF NOT EXISTS ebcursos_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- Criar usuário e conceder privilégios
CREATE USER IF NOT EXISTS 'ebcursos'@'localhost' IDENTIFIED BY 'ebcursos123';
GRANT ALL PRIVILEGES ON ebcursos_platform.* TO 'ebcursos'@'localhost';
FLUSH PRIVILEGES;

-- Criar tabela de alunos
CREATE TABLE IF NOT EXISTS alunos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    cpf VARCHAR(14) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Criar tabela de cursos
CREATE TABLE IF NOT EXISTS cursos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    descricao TEXT,
    thumbnail VARCHAR(255),
    status ENUM('ativo', 'inativo') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Criar tabela de matrículas
CREATE TABLE IF NOT EXISTS matriculas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id INT NOT NULL,
    curso_id INT NOT NULL,
    progress INT DEFAULT 0,
    status ENUM('ativo', 'concluido', 'cancelado') DEFAULT 'ativo',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (aluno_id) REFERENCES alunos(id) ON DELETE CASCADE,
    FOREIGN KEY (curso_id) REFERENCES cursos(id) ON DELETE CASCADE
);

-- Inserir aluno de teste
INSERT INTO alunos (nome, email, cpf, senha) VALUES 
('Aluno Teste', 'teste@ebcursos.com.br', '000.000.000-00', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Inserir curso de teste
INSERT INTO cursos (nome, descricao, thumbnail, status) VALUES
('Curso de Teste', 'Este é um curso de teste para desenvolvimento', 'https://via.placeholder.com/800x400', 'ativo');
