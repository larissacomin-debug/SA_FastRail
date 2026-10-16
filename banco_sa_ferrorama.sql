-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

CREATE SCHEMA IF NOT EXISTS mydb DEFAULT CHARACTER SET utf8;
USE mydb;

-- -----------------------------------------------------
-- Table mydb.perfil
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.perfil (
    idPerfil INT NOT NULL AUTO_INCREMENT,
    tipoPerfil VARCHAR(45) NOT NULL,
    descricaoPerfil VARCHAR(100) NOT NULL,
    PRIMARY KEY (idPerfil)
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.pessoa
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.pessoa (
    idPessoa INT NOT NULL AUTO_INCREMENT,
    nomePessoa VARCHAR(100) NULL,
    cpf VARCHAR(14) NOT NULL,
    telefone VARCHAR(20) NOT NULL,
    email VARCHAR(100) NOT NULL,
    data_nascimento DATE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    idPerfil INT NOT NULL DEFAULT 1,
    PRIMARY KEY (idPessoa),
    UNIQUE INDEX cpf_UNIQUE (cpf ASC) VISIBLE,
    UNIQUE INDEX email_UNIQUE (email ASC) VISIBLE,
    INDEX perfil_idx (idPerfil ASC) VISIBLE,
    CONSTRAINT fk_pessoa_perfil
        FOREIGN KEY (idPerfil)
        REFERENCES mydb.perfil (idPerfil)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.Trens
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.Trens (
    idTrem INT NOT NULL AUTO_INCREMENT,
    modelo VARCHAR(45) NOT NULL,
    tipo_abastecimento VARCHAR(45) NOT NULL,
    tipo_carga VARCHAR(45) NOT NULL,
    PRIMARY KEY (idTrem)
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.trilho
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.trilho (
    idTrilho INT NOT NULL AUTO_INCREMENT,
    localizacao VARCHAR(45) NOT NULL,
    distancia VARCHAR(45) NOT NULL,
    PRIMARY KEY (idTrilho)
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.Sensor
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.Sensor (
    idSensor INT NOT NULL AUTO_INCREMENT,
    modelo VARCHAR(45) NOT NULL,
    posicao VARCHAR(45) NULL,
    registros VARCHAR(45) NOT NULL,
    idTrem INT NOT NULL,
    idTrilho INT NULL,
    PRIMARY KEY (idSensor),
    INDEX idTrem_idx (idTrem ASC) VISIBLE,
    INDEX idTrilho_idx (idTrilho ASC) VISIBLE,
    CONSTRAINT fk_sensor_trem
        FOREIGN KEY (idTrem)
        REFERENCES mydb.Trens (idTrem)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT fk_sensor_trilho
        FOREIGN KEY (idTrilho)
        REFERENCES mydb.trilho (idTrilho)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.dados_sensor
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.dados_sensor (
    idDados_sensor INT NOT NULL AUTO_INCREMENT,
    idSensor INT NOT NULL,
    data_hora DATETIME NOT NULL,
    valor_coletado FLOAT NOT NULL,
    PRIMARY KEY (idDados_sensor),
    INDEX idSensor_idx (idSensor ASC) VISIBLE,
    CONSTRAINT fk_dados_sensor_sensor
        FOREIGN KEY (idSensor)
        REFERENCES mydb.Sensor (idSensor)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.Viagem
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.Viagem (
    idViagem INT NOT NULL AUTO_INCREMENT,
    idTrem INT NOT NULL,
    idTrilho INT NOT NULL,
    data_viagem DATE NOT NULL,
    horario_partida TIME NOT NULL,
    horario_chegada TIME NOT NULL,
    PRIMARY KEY (idViagem),
    INDEX idTrem_idx (idTrem ASC) VISIBLE,
    INDEX idTrilho_idx (idTrilho ASC) VISIBLE,
    CONSTRAINT fk_viagem_trem
        FOREIGN KEY (idTrem)
        REFERENCES mydb.Trens (idTrem)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT fk_viagem_trilho
        FOREIGN KEY (idTrilho)
        REFERENCES mydb.trilho (idTrilho)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.pagamento
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.pagamento (
    idPagamento INT NOT NULL AUTO_INCREMENT,
    valor_passagem FLOAT NOT NULL,
    forma_pagamento VARCHAR(45) NOT NULL,
    idPessoa INT NOT NULL,
    idIngresso INT NOT NULL,
    data_pagamento DATE NOT NULL,
    PRIMARY KEY (idPagamento),
    INDEX idPessoa_idx (idPessoa ASC) VISIBLE,
    CONSTRAINT fk_pagamento_pessoa
        FOREIGN KEY (idPessoa)
        REFERENCES mydb.pessoa (idPessoa)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.ingressos
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.ingressos (
    idIngressos INT NOT NULL AUTO_INCREMENT,
    idPessoa INT NOT NULL,
    idPagamento INT NOT NULL,
    assento INT NOT NULL,
    idViagem INT NOT NULL,
    PRIMARY KEY (idIngressos),
    INDEX idViagem_idx (idViagem ASC) VISIBLE,
    INDEX idPagamento_idx (idPagamento ASC) VISIBLE,
    INDEX idPessoa_idx (idPessoa ASC) VISIBLE,
    CONSTRAINT fk_ingressos_viagem
        FOREIGN KEY (idViagem)
        REFERENCES mydb.Viagem (idViagem)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT fk_ingressos_pagamento
        FOREIGN KEY (idPagamento)
        REFERENCES mydb.pagamento (idPagamento)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT fk_ingressos_pessoa
        FOREIGN KEY (idPessoa)
        REFERENCES mydb.pessoa (idPessoa)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Table mydb.Manutencao
-- -----------------------------------------------------

CREATE TABLE IF NOT EXISTS mydb.Manutencao (
    idManutencao INT NOT NULL AUTO_INCREMENT,
    dataHora DATETIME NOT NULL,
    motivo VARCHAR(45) NOT NULL,
    idTrem INT NOT NULL,
    idSensor INT NOT NULL,
    PRIMARY KEY (idManutencao),
    INDEX idTrem_idx (idTrem ASC) VISIBLE,
    INDEX idSensor_idx (idSensor ASC) VISIBLE,
    CONSTRAINT fk_manutencao_trem
        FOREIGN KEY (idTrem)
        REFERENCES mydb.Trens (idTrem)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION,
    CONSTRAINT fk_manutencao_sensor
        FOREIGN KEY (idSensor)
        REFERENCES mydb.Sensor (idSensor)
        ON DELETE NO ACTION
        ON UPDATE NO ACTION
)
ENGINE = InnoDB;

-- -----------------------------------------------------
-- Perfil padrão
-- -----------------------------------------------------

INSERT INTO mydb.perfil
(idPerfil, tipoPerfil, descricaoPerfil)
VALUES
(1, 'Cliente', 'Usuário padrão do sistema');

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;