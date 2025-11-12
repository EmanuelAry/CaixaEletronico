<?php
namespace tests\integration\dao;

use app\dao\ContaDao;
use PDO;
use PHPUnit\Framework\TestCase;

class ContaDaoTest extends TestCase{
    private $pdo;
    private $dao;

    protected function setUp(): void {
        // Configurar banco de dados em memória
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Criar tabela de teste
        $this->pdo->exec('
            CREATE TABLE conta (
                conta_id INTEGER PRIMARY KEY AUTOINCREMENT,
                conta_nome VARCHAR(100) NOT NULL,
                conta_email VARCHAR(100) UNIQUE NOT NULL,
                conta_senha VARCHAR(255) NOT NULL,
                conta_saldo DECIMAL(10,2) DEFAULT 0
            )
        ');

        // Mock da dependência IDatabase
        $databaseStub = $this->createStub(\app\contracts\core\IDatabase::class);
        $databaseStub->method('getConnection')->willReturn($this->pdo);

        $this->dao = new ContaDao($databaseStub);
    }

    protected function tearDown(): void{
        // Limpar banco após cada teste
        $this->pdo = null;
        $this->dao = null;
    }

    public function testValidarCadastroNovaContaBanco() {
        $dadosConta = [
            'conta_nome' => 'João Melão',
            'conta_email' => 'joao.melao@email.com',
            'conta_saldo' => 900,00,
            'conta_senha' => '123'
        ];

        $resultado = $this->dao->createConta($dadosConta);
        $this->assertTrue($resultado);

        // Verificar se os dados foram realmente salvos no banco em memória
        $stmt = $this->pdo->query('SELECT * FROM conta');
        $contas = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $this->assertCount(1, $contas);
        $this->assertEquals('João Melão', $contas[0]['conta_nome']);
    }
    
    public function testValidarBuscaDadosLoginContaEmail() {
        // Inserir dados de teste diretamente no banco
        $this->pdo->exec("
            INSERT INTO conta (conta_nome, conta_email, conta_senha, conta_saldo)
            VALUES ('João Melão', 'joao.melao@email.com', 'senha123', 1500.75)
        ");

        // Buscar conta por email
        $conta = $this->dao->getContaByEmail('joao.melao@email.com');

        $this->assertIsArray($conta);
        $this->assertEquals('João Melão', $conta['conta_nome']);
        $this->assertEquals('joao.melao@email.com', $conta['conta_email']);
        $this->assertEquals(1500.75, $conta['conta_saldo']);
        $this->assertEquals('senha123', $conta['conta_senha']);
        $this->assertArrayHasKey('conta_id', $conta);
    }

        public function testValidarBuscaDadosLoginContaId () {
        // Inserir dados de teste diretamente no banco
        $this->pdo->exec("
            INSERT INTO conta (conta_nome, conta_email, conta_senha, conta_saldo)
            VALUES ('João Melão', 'joao.melao@email.com', 'senha123', 1500.75)
        ");

        // Buscar conta por email
        $conta = $this->dao->getContaById(1);

        $this->assertIsArray($conta);
        $this->assertEquals('João Melão', $conta['conta_nome']);
        $this->assertEquals('joao.melao@email.com', $conta['conta_email']);
        $this->assertEquals(1500.75, $conta['conta_saldo']);
        $this->assertEquals('senha123', $conta['conta_senha']);
        $this->assertArrayHasKey('conta_id', $conta);
    }
}