CREATE DATABASE IF NOT EXISTS sistema_agendamento
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE sistema_agendamento;

CREATE TABLE IF NOT EXISTS usuarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS agendamentos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NULL,
  nome_cliente VARCHAR(120) NOT NULL,
  contato VARCHAR(120) NOT NULL,
  servico VARCHAR(120) NOT NULL,
  data_agendamento DATE NOT NULL,
  hora_agendamento TIME NOT NULL,
  observacoes TEXT NULL,
  status ENUM('pendente', 'confirmado', 'cancelado') NOT NULL DEFAULT 'pendente',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_agendamentos_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS publicacoes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT UNSIGNED NULL,
  autor_nome VARCHAR(120) NOT NULL,
  titulo VARCHAR(180) NOT NULL,
  conteudo TEXT NOT NULL,
  imagem_url VARCHAR(500) NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_publicacoes_usuario
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
    ON DELETE SET NULL
    ON UPDATE CASCADE
);

INSERT INTO usuarios (nome, email, senha)
SELECT 'Administrador', 'admin@local.test', '$2y$10$iVyUgRbcAFz/TxqdGn5R5.mcpa5QetHQobUM5SNjVmbSopjXGlyQO'
WHERE NOT EXISTS (
  SELECT 1 FROM usuarios WHERE email = 'admin@local.test'
);

INSERT INTO publicacoes (usuario_id, autor_nome, titulo, conteudo, imagem_url)
SELECT
  u.id,
  u.nome,
  'Bem-vindo ao agendamento online',
  'Consulte as informações publicadas pela administração e realize o seu agendamento nesta página.',
  NULL
FROM usuarios u
WHERE u.email = 'admin@local.test'
  AND NOT EXISTS (
    SELECT 1 FROM publicacoes WHERE titulo = 'Bem-vindo ao agendamento online'
  );
