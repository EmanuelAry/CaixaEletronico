# Sistema de Caixa Eletrônico
## 📋 Descrição do Projeto
Sistema de caixa eletrônico desenvolvido em PHP puro, seguindo princípios SOLID e arquitetura MVC. O sistema oferece funcionalidades completas de gestão de contas, saques, depósitos e controle de inventário de cédulas.

## 🚀 Funcionalidades Implementadas

### ✅ Questão 1 - Gestão de Inventário
- Modelo robusto para gerenciamento de cédulas e moedas
- Suporte para 12 denominações diferentes (R$200 até R$0.05)
- Operações de carregamento, descarregamento e consulta
- Cálculo automático do valor total disponível

### ✅ Questão 2 - Sistema de Saque com Estratégias
- **Contrato/Interface** para estratégias de composição
- **Duas implementações**:
    - 🎯 **Estratégia Padrão**: Menor quantidade de cédulas
    - 🔄 **Estratégia Alternativa**: Preserva cédulas de maior valor
- **Injeção de dependência** para troca dinâmica de estratégias

### ✅ Questão 3 - Sistema de Notificações
- **Interface de notificação** extensível
- **Registro em arquivo de texto** com data/hora
- **Sistema de sessão** para notificações em tempo real
- Múltiplos tipos de notificação (success, error, warning, info)

### ✅ Questão 4 - Detecção de Valores Indisponíveis
- **Validação inteligente** de composição de cédulas
- **Sugestões de valores alternativos** para saque
- **Feedback claro** ao usuário sobre indisponibilidade

### ✅ Questão 5 - Sistema de Contas
- **Autenticação por ID único**
- **Gestão de saldo** integrada
- **Operações vinculadas** à conta do usuário

## 🏗️ Arquitetura do Sistema
```
CaixaEletronico/
├── public/
│   └── index.php                 # Ponto de entrada
├── src/
│   ├── core/                     # Classes core
│   │   ├── Router.php           # Sistema de roteamento
│   │   ├── Database.php         # Conexão com banco
│   │   ├── Logger.php           # Log de operações
│   │   └── Notification.php     # Sistema de notificações
│   ├── contracts/               # Interfaces
│   │   ├── core/
│   │   │   ├── ILogger.php
│   │   │   └── INotification.php
│   │   ├── models/
│   │   │   ├── IContaModel.php
│   │   │   └── ICaixaEletronicoModel.php
│   │   ├── dao/
│   │   │   ├── IContaDao.php 
│   │   │   └── ICaixaEletronicoDao.php 
│   │   ├── controllers/  
│   │   │   ├── IContaController.php
│   │   │   └── ICaixaEletronicoController.php
│   │   └── services/   
│   │       ├── ContaService.php
│   │       └── CaixaEletronicoService.php
│   ├── models/                  # Modelos de dados
│   │   ├── ContaModel.php    
│   │   ├── ContratoModel.php    # Estratégias de saque
│   │   └── CaixaEletronicoModel.php
│   ├── dao/                     # Data Access Object
│   │   ├── ContaDao.php
│   │   └── CaixaEletronicoDao.php
│   ├── controllers/             # Controladores
│   │   ├── ContaController.php
│   │   └── CaixaEletronicoController.php
│   ├── services/               # Services
│   │   ├── ContaService.php
│   │   └── CaixaEletronicoService.php
│   ├── views/                   # Telas do sistema
│   │   ├── conta/
│   │   │   ├── selecionarconta.php
│   │   │   └── criarconta.php
│   │   └── templates/
│   │       ├── header.php
│   │       ├── footer.php
│   │       └── notifications.php
│   └── helpers/                 # Auxiliares
│       └── UrlHelper.php
└── README.md
```
## 🛠️ Tecnologias e Princípios
- **PHP 7.4+** (sem frameworks externos)
- **MySQL** para persistência
- **Arquitetura MVC** customizada
- **Princípios SOLID** aplicados
- **Inversão de Dependência**
- **Roteamento customizado**
- **Sistema de templates** próprio

## 📁 Estrutura de Banco de Dados
### Tabela: qtd_cedula_caixa
```sql
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
);
```
### Tabela: contas
```sql
CREATE TABLE contas (
    conta_id INT AUTO_INCREMENT PRIMARY KEY,
    conta_nome VARCHAR(100) NOT NULL,
    conta_saldo DECIMAL(10,2) DEFAULT 0.00
);
```
## 🎯 Como Executar
### Pré-requisitos
- PHP 7.4 ou superior
- MySQL 5.7 ou superior
- Apache/Nginx com mod_rewrite habilitado
### Instalação
1. Clone o repositório:
```
bash
git clone https://github.com/EmanuelAry/CaixaEletronico.git
cd CaixaEletronico
```
### Configure o banco de dados:
```
bash
mysql -u root -p < database/caixa_eletronico.sql
Configure as credenciais do banco em src/core/Database.php
```
### Acesse via navegador:
```text
http://localhost/caixaeletronico/public/
```
## 🖥️ Telas do Sistema
1. Tela de Seleção de Conta
![Interface para selecionar conta existente ou criar nova conta](/telas/selecao_conta.png)

2. Tela de Criação de Conta
![Formulário para criação de novas contas bancárias](/telas/criar_conta.png)

3. Tela de Operações
![Interface para realização de saques, depósitos e consultas](/telas/operacoes_conta.png)

4. Tela de Gestão Estoque do Caixa
![Painel administrativo para controle de cédulas e moedas](/telas/estoque_caixa.png)
