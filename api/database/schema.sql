-- Schema de REFERÊNCIA, inferido dos SELECT/INSERT do código (o zip não trazia o banco).
-- Compare com o seu banco real; não rode por cima de um banco em uso sem conferir.
CREATE DATABASE IF NOT EXISTS restaurante CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE restaurante;

CREATE TABLE IF NOT EXISTS restaurantes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  cnpj VARCHAR(18) NOT NULL UNIQUE,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS cargos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(80) NOT NULL UNIQUE
);

CREATE TABLE IF NOT EXISTS funcionarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cargo_id INT UNSIGNED NOT NULL,
  restaurante_id INT UNSIGNED NOT NULL,
  nome VARCHAR(150) NOT NULL,
  telefone VARCHAR(20) NULL,
  email VARCHAR(150) NULL,
  ativo TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (cargo_id) REFERENCES cargos(id),
  FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id)
);

CREATE TABLE IF NOT EXISTS categorias (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(100) NOT NULL,
  restaurante_id INT UNSIGNED NOT NULL,
  FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id)
);

CREATE TABLE IF NOT EXISTS produtos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT UNSIGNED NOT NULL,
  restaurante_id INT UNSIGNED NOT NULL,
  nome VARCHAR(150) NOT NULL,
  descricao TEXT NULL,
  preco DECIMAL(10,2) NOT NULL,
  disponivel TINYINT(1) NOT NULL DEFAULT 1,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id),
  FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id)
);

CREATE TABLE IF NOT EXISTS mesas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  restaurante_id INT UNSIGNED NOT NULL,
  numero INT NOT NULL,
  capacidade INT NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'livre',
  UNIQUE (restaurante_id, numero),
  FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id)
);

CREATE TABLE IF NOT EXISTS clientes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  restaurante_id INT UNSIGNED NOT NULL,
  nome VARCHAR(150) NOT NULL,
  telefone VARCHAR(20) NULL,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  FOREIGN KEY (restaurante_id) REFERENCES restaurantes(id)
);

CREATE TABLE IF NOT EXISTS enderecos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  cep VARCHAR(9) NOT NULL,
  estado CHAR(2) NOT NULL,
  cidade VARCHAR(100) NOT NULL,
  bairro VARCHAR(100) NOT NULL,
  rua VARCHAR(150) NOT NULL,
  numero VARCHAR(20) NOT NULL,
  complemento VARCHAR(100) NULL,
  referencia VARCHAR(150) NULL,
  principal TINYINT(1) NOT NULL DEFAULT 0,
  created_at DATETIME NOT NULL,
  updated_at DATETIME NOT NULL,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id)
);

-- Atenção: aqui o código original usa id_restaurante / id_cliente (nas outras tabelas é X_id). Mantive como estava.
CREATE TABLE IF NOT EXISTS comandas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  id_restaurante INT UNSIGNED NOT NULL,
  id_cliente INT UNSIGNED NULL,
  funcionario_id INT UNSIGNED NOT NULL,
  aberta_em DATETIME NOT NULL,
  fechada_em DATETIME NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'aberta',
  total DECIMAL(10,2) NOT NULL DEFAULT 0,
  FOREIGN KEY (id_restaurante) REFERENCES restaurantes(id),
  FOREIGN KEY (id_cliente) REFERENCES clientes(id),
  FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

CREATE TABLE IF NOT EXISTS pedidos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comanda_id INT UNSIGNED NOT NULL,
  funcionario_id INT UNSIGNED NOT NULL,
  criado_em DATETIME NOT NULL,
  status VARCHAR(20) NOT NULL DEFAULT 'pendente',
  observacao VARCHAR(255) NULL,
  FOREIGN KEY (comanda_id) REFERENCES comandas(id),
  FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id)
);

CREATE TABLE IF NOT EXISTS itens_pedidos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  pedido_id INT UNSIGNED NOT NULL,
  produto_id INT UNSIGNED NOT NULL,
  quantidade INT UNSIGNED NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  observacao VARCHAR(255) NULL,
  FOREIGN KEY (pedido_id) REFERENCES pedidos(id),
  FOREIGN KEY (produto_id) REFERENCES produtos(id)
);

CREATE TABLE IF NOT EXISTS pagamentos (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  comanda_id INT UNSIGNED NOT NULL,
  valor DECIMAL(10,2) NOT NULL,
  metodo VARCHAR(30) NOT NULL,
  pago_em DATETIME NOT NULL,
  FOREIGN KEY (comanda_id) REFERENCES comandas(id)
);

-- administrador_id: aponte para a sua tabela de administradores (o código não a define).
CREATE TABLE IF NOT EXISTS contas_acesso (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  administrador_id INT UNSIGNED NOT NULL UNIQUE,
  email VARCHAR(150) NOT NULL UNIQUE,
  senha_hash VARCHAR(255) NOT NULL,
  created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);
