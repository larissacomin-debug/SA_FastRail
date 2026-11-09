-- -----------------------------------------------------
-- Banco do FastRail (SA-Ferrorama)
-- Compatível com MySQL e MariaDB (XAMPP).
-- Pode ser importado direto pelo phpMyAdmin: o próprio
-- script cria o banco "banco_sa_ferrorama".
-- -----------------------------------------------------

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS banco_sa_ferrorama
    DEFAULT CHARACTER SET utf8mb4
    DEFAULT COLLATE utf8mb4_unicode_ci;

USE banco_sa_ferrorama;

DROP TABLE IF EXISTS manutencao;
DROP TABLE IF EXISTS ingressos;
DROP TABLE IF EXISTS pagamento;
DROP TABLE IF EXISTS viagem;
DROP TABLE IF EXISTS dados_sensor;
DROP TABLE IF EXISTS sensor;
DROP TABLE IF EXISTS rotas;
DROP TABLE IF EXISTS trilho;
DROP TABLE IF EXISTS trens;
DROP TABLE IF EXISTS pessoa;
DROP TABLE IF EXISTS perfil;

-- -----------------------------------------------------
-- Tabela perfil
-- -----------------------------------------------------
CREATE TABLE perfil (
    idPerfil INT NOT NULL AUTO_INCREMENT,
    tipoPerfil VARCHAR(45) NOT NULL,
    descricaoPerfil VARCHAR(100) NOT NULL,
    PRIMARY KEY (idPerfil)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela pessoa
-- -----------------------------------------------------
CREATE TABLE pessoa (
    idPessoa INT NOT NULL AUTO_INCREMENT,
    nomePessoa VARCHAR(100) NOT NULL,
    cpf VARCHAR(14) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    idPerfil INT NOT NULL DEFAULT 1,
    PRIMARY KEY (idPessoa),
    UNIQUE INDEX cpf_UNIQUE (cpf),
    UNIQUE INDEX email_UNIQUE (email),
    INDEX perfil_idx (idPerfil),
    CONSTRAINT fk_pessoa_perfil
        FOREIGN KEY (idPerfil) REFERENCES perfil (idPerfil)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela trens
-- estado_atual: operacao | manutencao | parado
-- -----------------------------------------------------
CREATE TABLE trens (
    idTrem INT NOT NULL AUTO_INCREMENT,
    modelo VARCHAR(45) NOT NULL,
    tipo_abastecimento VARCHAR(45) NOT NULL,
    tipo_carga VARCHAR(45) NOT NULL,
    estado_atual VARCHAR(20) NOT NULL DEFAULT 'operacao',
    PRIMARY KEY (idTrem)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela trilho (localizações usadas pelos sensores)
-- -----------------------------------------------------
CREATE TABLE trilho (
    idTrilho INT NOT NULL AUTO_INCREMENT,
    localizacao VARCHAR(45) NOT NULL,
    distancia VARCHAR(45) NOT NULL,
    PRIMARY KEY (idTrilho)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela rotas
-- status: Ativa | Inativa | Manutenção
-- -----------------------------------------------------
CREATE TABLE rotas (
    idRota INT NOT NULL AUTO_INCREMENT,
    nome_rota VARCHAR(100) NOT NULL,
    origem VARCHAR(100) NOT NULL,
    destino VARCHAR(100) NOT NULL,
    distancia DECIMAL(10,2) NOT NULL,
    status VARCHAR(20) NOT NULL DEFAULT 'Ativa',
    PRIMARY KEY (idRota)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela sensor
-- modelo: Temperatura | Velocidade | Pressão | Vibração
-- Ao excluir um trem, os sensores dele também são excluídos.
-- -----------------------------------------------------
CREATE TABLE sensor (
    idSensor INT NOT NULL AUTO_INCREMENT,
    modelo VARCHAR(45) NOT NULL,
    posicao VARCHAR(45) NULL,
    idTrem INT NOT NULL,
    idTrilho INT NULL,
    PRIMARY KEY (idSensor),
    INDEX idTrem_idx (idTrem),
    INDEX idTrilho_idx (idTrilho),
    CONSTRAINT fk_sensor_trem
        FOREIGN KEY (idTrem) REFERENCES trens (idTrem)
        ON DELETE CASCADE,
    CONSTRAINT fk_sensor_trilho
        FOREIGN KEY (idTrilho) REFERENCES trilho (idTrilho)
        ON DELETE SET NULL
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela dados_sensor (leituras)
-- -----------------------------------------------------
CREATE TABLE dados_sensor (
    idDados_sensor INT NOT NULL AUTO_INCREMENT,
    idSensor INT NOT NULL,
    data_hora DATETIME NOT NULL,
    valor_coletado FLOAT NOT NULL,
    PRIMARY KEY (idDados_sensor),
    INDEX idSensor_idx (idSensor),
    INDEX data_hora_idx (data_hora),
    CONSTRAINT fk_dados_sensor_sensor
        FOREIGN KEY (idSensor) REFERENCES sensor (idSensor)
        ON DELETE CASCADE
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela viagem
-- -----------------------------------------------------
CREATE TABLE viagem (
    idViagem INT NOT NULL AUTO_INCREMENT,
    idTrem INT NOT NULL,
    idTrilho INT NOT NULL,
    data_viagem DATE NOT NULL,
    horario_partida TIME NOT NULL,
    horario_chegada TIME NOT NULL,
    PRIMARY KEY (idViagem),
    INDEX idTrem_idx (idTrem),
    INDEX idTrilho_idx (idTrilho),
    CONSTRAINT fk_viagem_trem
        FOREIGN KEY (idTrem) REFERENCES trens (idTrem)
        ON DELETE CASCADE,
    CONSTRAINT fk_viagem_trilho
        FOREIGN KEY (idTrilho) REFERENCES trilho (idTrilho)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela pagamento
-- -----------------------------------------------------
CREATE TABLE pagamento (
    idPagamento INT NOT NULL AUTO_INCREMENT,
    valor_passagem DECIMAL(10,2) NOT NULL,
    forma_pagamento VARCHAR(45) NOT NULL,
    idPessoa INT NOT NULL,
    data_pagamento DATE NOT NULL,
    PRIMARY KEY (idPagamento),
    INDEX idPessoa_idx (idPessoa),
    CONSTRAINT fk_pagamento_pessoa
        FOREIGN KEY (idPessoa) REFERENCES pessoa (idPessoa)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela ingressos
-- -----------------------------------------------------
CREATE TABLE ingressos (
    idIngressos INT NOT NULL AUTO_INCREMENT,
    idPessoa INT NOT NULL,
    idPagamento INT NOT NULL,
    assento INT NOT NULL,
    idViagem INT NOT NULL,
    PRIMARY KEY (idIngressos),
    INDEX idViagem_idx (idViagem),
    INDEX idPagamento_idx (idPagamento),
    INDEX idPessoa_idx (idPessoa),
    CONSTRAINT fk_ingressos_viagem
        FOREIGN KEY (idViagem) REFERENCES viagem (idViagem)
        ON DELETE CASCADE,
    CONSTRAINT fk_ingressos_pagamento
        FOREIGN KEY (idPagamento) REFERENCES pagamento (idPagamento),
    CONSTRAINT fk_ingressos_pessoa
        FOREIGN KEY (idPessoa) REFERENCES pessoa (idPessoa)
) ENGINE = InnoDB;

-- -----------------------------------------------------
-- Tabela manutencao
-- -----------------------------------------------------
CREATE TABLE manutencao (
    idManutencao INT NOT NULL AUTO_INCREMENT,
    dataHora DATETIME NOT NULL,
    motivo VARCHAR(255) NOT NULL,
    idTrem INT NOT NULL,
    idSensor INT NULL,
    PRIMARY KEY (idManutencao),
    INDEX idTrem_idx (idTrem),
    INDEX idSensor_idx (idSensor),
    CONSTRAINT fk_manutencao_trem
        FOREIGN KEY (idTrem) REFERENCES trens (idTrem)
        ON DELETE CASCADE,
    CONSTRAINT fk_manutencao_sensor
        FOREIGN KEY (idSensor) REFERENCES sensor (idSensor)
        ON DELETE SET NULL
) ENGINE = InnoDB;

SET FOREIGN_KEY_CHECKS = 1;

-- -----------------------------------------------------
-- Dados iniciais
-- -----------------------------------------------------

INSERT INTO perfil (idPerfil, tipoPerfil, descricaoPerfil) VALUES
(1, 'Cliente', 'Usuário padrão do sistema'),
(2, 'Funcionário', 'Funcionário da empresa');

-- Usuário de teste: professor@gmail.com / prof12345
INSERT INTO pessoa (nomePessoa, cpf, telefone, email, data_nascimento, senha, idPerfil) VALUES
('Professor', '000.000.000-00', '(00) 00000-0000', 'professor@gmail.com', '1990-01-01',
 '$2y$12$SfY7IpIEM0jPS3Yoe8AT3OZqVTJffo.BfMZRn2rQ9ffXIsL2tW4VG', 2);

INSERT INTO trilho (localizacao, distancia) VALUES
('Estação Central', '0 km'),
('Estação Norte', '25 km'),
('Estação Sul', '18 km'),
('Pátio de Manutenção', '5 km');

INSERT INTO trens (modelo, tipo_abastecimento, tipo_carga, estado_atual) VALUES
('Hyundai Rotem', 'Elétrico', 'Pessoas', 'operacao'),
('Alstom', 'Diesel', 'Mercadorias', 'manutencao'),
('Siemens', 'Elétrico', 'Pessoas', 'parado');

INSERT INTO rotas (nome_rota, origem, destino, distancia, status) VALUES
('Rota Norte - Sul', 'Estação Norte', 'Estação Sul', 43.00, 'Ativa'),
('Rota Central', 'Estação Central', 'Estação Norte', 25.00, 'Manutenção');
