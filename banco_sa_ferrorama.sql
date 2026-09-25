-- MySQL Workbench Forward Engineering

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------

-- -----------------------------------------------------
-- Schema mydb
-- -----------------------------------------------------
CREATE SCHEMA IF NOT EXISTS `mydb` DEFAULT CHARACTER SET utf8 ;
USE `mydb` ;

-- -----------------------------------------------------
-- Table `mydb`.`perfil`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`perfil` (
  `idPerfil` INT NOT NULL,
  `tipoPerfil` VARCHAR(45) NOT NULL,
  `descricaoPerfil` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idPerfil`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`pessoa`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`pessoa` (
  `idPessoa` INT NOT NULL,
  `nomePessoa` VARCHAR(45) NOT NULL,
  `telefone` BIGINT(12) NOT NULL,
  `email` VARCHAR(45) NOT NULL,
  `data_nascimento` DATE NOT NULL,
  `senha` VARCHAR(45) NOT NULL,
  `idPerfil` INT NOT NULL,
  `cpf` VARCHAR(15) NOT NULL,
  PRIMARY KEY (`idPessoa`),
  INDEX `perfil_idx` (`idPerfil` ASC) VISIBLE,
  CONSTRAINT `perfil`
    FOREIGN KEY (`idPerfil`)
    REFERENCES `mydb`.`perfil` (`idPerfil`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Trens`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Trens` (
  `idTrem` INT NOT NULL,
  `modelo` VARCHAR(45) NOT NULL,
  `tipo_abastecimento` VARCHAR(45) NOT NULL,
  `tipo_carga` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idTrem`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`trilho`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`trilho` (
  `idTrilho` INT NOT NULL,
  `localizacao` VARCHAR(45) NOT NULL,
  `distancia` VARCHAR(45) NOT NULL,
  PRIMARY KEY (`idTrilho`))
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Sensor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Sensor` (
  `idSensor` INT NOT NULL,
  `modelo` VARCHAR(45) NOT NULL,
  `posicao` VARCHAR(45) NOT NULL,
  `registros` VARCHAR(45) NOT NULL,
  `idTrem` INT NOT NULL,
  `idTrilho` INT NOT NULL,
  PRIMARY KEY (`idSensor`),
  INDEX `idTrem_idx` (`idTrem` ASC) VISIBLE,
  INDEX `idTrilho_idx` (`idTrilho` ASC) VISIBLE,
  CONSTRAINT `idTrem`
    FOREIGN KEY (`idTrem`)
    REFERENCES `mydb`.`Trens` (`idTrem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `idTrilho`
    FOREIGN KEY (`idTrilho`)
    REFERENCES `mydb`.`trilho` (`idTrilho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`dados_sensor`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`dados_sensor` (
  `idDados_sensor` INT NOT NULL,
  `idSensor` INT NOT NULL,
  `data_hora` DATETIME NOT NULL,
  `valor_coletado` FLOAT NOT NULL,
  PRIMARY KEY (`idDados_sensor`),
  INDEX `idSensor_idx` (`idSensor` ASC) VISIBLE,
  CONSTRAINT `idSensor`
    FOREIGN KEY (`idSensor`)
    REFERENCES `mydb`.`Sensor` (`idSensor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Viagem`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Viagem` (
  `idViagem` INT NOT NULL,
  `idTrem` INT NOT NULL,
  `idTrilho` INT NOT NULL,
  `data_viagem` DATE NOT NULL,
  `horario_partida` TIME NOT NULL,
  `horario_chegada` TIME NOT NULL,
  PRIMARY KEY (`idViagem`),
  INDEX `idTrem_idx` (`idTrem` ASC) VISIBLE,
  INDEX `idTrilho_idx` (`idTrilho` ASC) VISIBLE,
  CONSTRAINT `idTrem`
    FOREIGN KEY (`idTrem`)
    REFERENCES `mydb`.`Trens` (`idTrem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `idTrilho`
    FOREIGN KEY (`idTrilho`)
    REFERENCES `mydb`.`trilho` (`idTrilho`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`pagamento`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`pagamento` (
  `idPagamento` INT NOT NULL,
  `valor_passagem` FLOAT NOT NULL,
  `forma_pagamento` VARCHAR(45) NOT NULL,
  `idPessoa` INT NOT NULL,
  `idIngresso` INT NOT NULL,
  `data_pagamento` DATE NOT NULL,
  PRIMARY KEY (`idPagamento`),
  INDEX `idPessoa_idx` (`idPessoa` ASC) VISIBLE,
  CONSTRAINT `idPessoa`
    FOREIGN KEY (`idPessoa`)
    REFERENCES `mydb`.`pessoa` (`idPessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`ingressos`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`ingressos` (
  `idIngressos` INT NOT NULL,
  `idPessoa` INT NOT NULL,
  `idPagamento` INT NOT NULL,
  `assento` INT NOT NULL,
  `idViagem` INT NOT NULL,
  PRIMARY KEY (`idIngressos`),
  INDEX `idViagem_idx` (`idViagem` ASC) VISIBLE,
  INDEX `idPagamento_idx` (`idPagamento` ASC) VISIBLE,
  INDEX `idPessoa_idx` (`idPessoa` ASC) VISIBLE,
  CONSTRAINT `idViagem`
    FOREIGN KEY (`idViagem`)
    REFERENCES `mydb`.`Viagem` (`idViagem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `idPagamento`
    FOREIGN KEY (`idPagamento`)
    REFERENCES `mydb`.`pagamento` (`idPagamento`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `idPessoa`
    FOREIGN KEY (`idPessoa`)
    REFERENCES `mydb`.`pessoa` (`idPessoa`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


-- -----------------------------------------------------
-- Table `mydb`.`Manutencao`
-- -----------------------------------------------------
CREATE TABLE IF NOT EXISTS `mydb`.`Manutencao` (
  `idManutencao` INT NOT NULL,
  `dataHora` DATETIME NOT NULL,
  `motivo` VARCHAR(45) NOT NULL,
  `idTrem` INT NOT NULL,
  `idSensor` INT NOT NULL,
  PRIMARY KEY (`idManutencao`),
  INDEX `idTrem_idx` (`idTrem` ASC) VISIBLE,
  INDEX `idSensor_idx` (`idSensor` ASC) VISIBLE,
  CONSTRAINT `idTrem`
    FOREIGN KEY (`idTrem`)
    REFERENCES `mydb`.`Trens` (`idTrem`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION,
  CONSTRAINT `idSensor`
    FOREIGN KEY (`idSensor`)
    REFERENCES `mydb`.`Sensor` (`idSensor`)
    ON DELETE NO ACTION
    ON UPDATE NO ACTION)
ENGINE = InnoDB;


SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;
