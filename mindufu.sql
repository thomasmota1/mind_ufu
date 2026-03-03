

DROP DATABASE IF EXISTS mind_ufu;
CREATE DATABASE mind_ufu CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE mind_ufu;


CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255),
    foto VARCHAR(255) DEFAULT NULL,
    bio TEXT DEFAULT NULL,
    link_linkedin VARCHAR(255) DEFAULT NULL,
    link_instagram VARCHAR(255) DEFAULT NULL,
    link_youtube VARCHAR(255) DEFAULT NULL,
    link_twitter VARCHAR(255) DEFAULT NULL,
    link_facebook VARCHAR(255) DEFAULT NULL,
    link_github VARCHAR(255) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE disciplinas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    usuario_id INT DEFAULT NULL
);

CREATE TABLE pastas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    disciplina_id INT,
    nome VARCHAR(100) NOT NULL,
    FOREIGN KEY (disciplina_id) REFERENCES disciplinas(id) ON DELETE CASCADE
);

CREATE TABLE paginas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pasta_id INT,
    titulo VARCHAR(255) DEFAULT 'Nova Pagina',
    conteudo TEXT,
    FOREIGN KEY (pasta_id) REFERENCES pastas(id) ON DELETE CASCADE
);


CREATE TABLE comunidades (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    codigo VARCHAR(10) NOT NULL UNIQUE,
    cor VARCHAR(7) DEFAULT '#0d6efd',
    icone VARCHAR(20) DEFAULT 'bi-people-fill',
    criador_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE membros_comunidade (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comunidade_id INT,
    usuario_id INT,
    data_entrada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comunidade_id) REFERENCES comunidades(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


CREATE TABLE questionarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    comunidade_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    criador_id INT DEFAULT NULL,
    publicado BOOLEAN DEFAULT FALSE,
    permite_salvar_antes BOOLEAN DEFAULT TRUE,
    data_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (comunidade_id) REFERENCES comunidades(id) ON DELETE CASCADE,
    FOREIGN KEY (criador_id) REFERENCES usuarios(id)
);

CREATE TABLE perguntas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    questionario_id INT NOT NULL,
    enunciado TEXT NOT NULL,
    pontos INT DEFAULT 1,
    FOREIGN KEY (questionario_id) REFERENCES questionarios(id) ON DELETE CASCADE
);

CREATE TABLE alternativas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pergunta_id INT NOT NULL,
    texto VARCHAR(255) NOT NULL,
    e_correta BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (pergunta_id) REFERENCES perguntas(id) ON DELETE CASCADE
);

CREATE TABLE quizzes_salvos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    questionario_id INT NOT NULL,
    data_salvo DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (questionario_id) REFERENCES questionarios(id) ON DELETE CASCADE,
    UNIQUE(usuario_id, questionario_id)
);


CREATE TABLE eventos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT,
    data_inicio DATETIME NOT NULL,
    cor VARCHAR(7) DEFAULT '#0d6efd',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);


CREATE TABLE IF NOT EXISTS decks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    usuario_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS flashcards (
    id INT AUTO_INCREMENT PRIMARY KEY,
    deck_id INT NOT NULL,
    pergunta TEXT NOT NULL,
    resposta TEXT NOT NULL,
    FOREIGN KEY (deck_id) REFERENCES decks(id) ON DELETE CASCADE
);


