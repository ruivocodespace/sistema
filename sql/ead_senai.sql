-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 27/10/2026 às 21:42
-- Versão do servidor: 9.1.0
-- Versão do PHP: 7.4.33


CREATE DATABASE IF NOT EXISTS ead_senai;
SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";




/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;


--
-- Banco de dados: ead_senai
--


-- --------------------------------------------------------


--
-- Estrutura para tabela aulas
--


DROP TABLE IF EXISTS aulas;
CREATE TABLE IF NOT EXISTS aulas (
  id int NOT NULL AUTO_INCREMENT,
  modulo_id int NOT NULL,
  titulo varchar(150) NOT NULL,
  video_url varchar(500) DEFAULT NULL,
  duracao varchar(10) DEFAULT NULL,
  descricao text,
  ordem int NOT NULL DEFAULT '1',
  PRIMARY KEY (id),
  KEY modulo_id (modulo_id)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------


--
-- Estrutura para tabela cursos
--
CREATE TABLE IF NOT EXISTS cursos (
  id int NOT NULL AUTO_INCREMENT,
  modulo_id int DEFAULT NULL,
  titulo varchar(150) NOT NULL,
  descricao text NOT NULL,
  capa varchar(255) DEFAULT NULL,
  ativo tinyint(1) NOT NULL DEFAULT '1',
  criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  ALTER TABLE cursos ADD COLUMN professor VARCHAR(150) NULL AFTER descricao;
  KEY modulo_id (modulo_id)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4;


--
-- Despejando dados para a tabela cursos
--


INSERT INTO cursos (id, modulo_id, titulo, descricao, capa, ativo, criado_em) VALUES
(3, 2, 'php do zero', 'teste', 'uploads/6ae0ff332a315.jpeg', 1, '2026-10-27 19:42:49');


-- --------------------------------------------------------


--
-- Estrutura para tabela inscricoes
--


DROP TABLE IF EXISTS inscricoes;
CREATE TABLE IF NOT EXISTS inscricoes (
  id int NOT NULL AUTO_INCREMENT,
  usuario_id int NOT NULL,
  curso_id int NOT NULL,
  criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_inscricao (usuario_id,curso_id),
  KEY curso_id (curso_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


-- --------------------------------------------------------


--
-- Estrutura para tabela modulos
--


DROP TABLE IF EXISTS modulos;
CREATE TABLE IF NOT EXISTS modulos (
  id int NOT NULL AUTO_INCREMENT,
  titulo varchar(150) NOT NULL,
  descricao text,
  ordem int NOT NULL DEFAULT '1',
  PRIMARY KEY (id)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4;


--
-- Despejando dados para a tabela modulos
--


INSERT INTO modulos (id, titulo, descricao, ordem) VALUES
(2, 'Modulo teste', 'descrição', 1);


-- --------------------------------------------------------


--
-- Estrutura para tabela usuarios
--


DROP TABLE IF EXISTS usuarios;
CREATE TABLE IF NOT EXISTS usuarios (
  id int NOT NULL AUTO_INCREMENT,
  nome varchar(150) NOT NULL,
  email varchar(150) NOT NULL,
  senha varchar(255) NOT NULL,
  tipo enum('aluno','admin') NOT NULL DEFAULT 'aluno',
  criado_em timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY uq_email (email)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4;


--
-- Despejando dados para a tabela usuarios
--


INSERT INTO usuarios (id, nome, email, senha, tipo, criado_em) VALUES
(1, 'WELLINGTON MARQUES OLIVEIRA', 'weto@gmail.com', '$2y$10$ap7cIrn1VibKdiKQhCwMDeYW9N3f5ViJyHuPlyclAAQpMYo145RzC', 'admin', '2026-03-05 20:13:40');


--
-- Restrições para tabelas despejadas
--


--
-- Restrições para tabelas aulas
--
ALTER TABLE aulas
  ADD CONSTRAINT aulas_ibfk_1 FOREIGN KEY (modulo_id) REFERENCES modulos (id) ON DELETE CASCADE;


--
-- Restrições para tabelas cursos
--
ALTER TABLE cursos
  ADD CONSTRAINT cursos_ibfk_1 FOREIGN KEY (modulo_id) REFERENCES modulos (id) ON DELETE SET NULL;


--
-- Restrições para tabelas inscricoes
--
ALTER TABLE inscricoes
  ADD CONSTRAINT inscricoes_ibfk_1 FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE CASCADE,
  ADD CONSTRAINT inscricoes_ibfk_2 FOREIGN KEY (curso_id) REFERENCES cursos (id) ON DELETE CASCADE;
COMMIT;


/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;