-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Tempo de geração: 21/10/2025 às 18:07
-- Versão do servidor: 10.4.32-MariaDB
-- Versão do PHP: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `caixa_eletronico`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `conta`
--

CREATE TABLE `conta` (
  `conta_id` int(11) NOT NULL,
  `conta_nome` varchar(50) NOT NULL,
  `conta_saldo` decimal(20,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `qtd_cedula_caixa`
--

CREATE TABLE `qtd_cedula_caixa` (
  `qtd_cedula_caixa_id` int(11) NOT NULL,
  `qtd_cedula_5_cents` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_10_cents` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_25_cents` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_50_cents` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_1_real` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_2_real` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_5_real` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_10_real` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_20_real` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_50_real` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_100_real` int(11) NOT NULL DEFAULT 0,
  `qtd_cedula_200_real` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `qtd_cedula_caixa`
--

INSERT INTO `qtd_cedula_caixa` (`qtd_cedula_caixa_id`, `qtd_cedula_5_cents`, `qtd_cedula_10_cents`, `qtd_cedula_25_cents`, `qtd_cedula_50_cents`, `qtd_cedula_1_real`, `qtd_cedula_2_real`, `qtd_cedula_5_real`, `qtd_cedula_10_real`, `qtd_cedula_20_real`, `qtd_cedula_50_real`, `qtd_cedula_100_real`, `qtd_cedula_200_real`) VALUES
(1, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10, 10);

--
-- Índices para tabelas despejadas
--

--
-- Índices de tabela `conta`
--
ALTER TABLE `conta`
  ADD PRIMARY KEY (`conta_id`);

--
-- Índices de tabela `qtd_cedula_caixa`
--
ALTER TABLE `qtd_cedula_caixa`
  ADD PRIMARY KEY (`qtd_cedula_caixa_id`);

--
-- AUTO_INCREMENT para tabelas despejadas
--

--
-- AUTO_INCREMENT de tabela `conta`
--
ALTER TABLE `conta`
  MODIFY `conta_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de tabela `qtd_cedula_caixa`
--
ALTER TABLE `qtd_cedula_caixa`
  MODIFY `qtd_cedula_caixa_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
