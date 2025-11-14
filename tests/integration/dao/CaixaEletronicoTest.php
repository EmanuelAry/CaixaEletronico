<?php
namespace tests\integration\dao;

use app\dao\CaixaEletronicoDao;
use PDO;
use PHPUnit\Framework\TestCase;

class CaixaEletronicoTestDaoTest extends TestCase{
    private $pdo;
    private $dao;

    protected function setUp(): void {
        // Configurar banco de dados em memória
        $this->pdo = new PDO('sqlite::memory:');
        $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        
        // Criar tabela de teste
        $this->pdo->exec('
            CREATE TABLE qtd_cedula_caixa (
                qtd_cedula_caixa_id INTEGER PRIMARY KEY AUTOINCREMENT,
                qtd_cedula_200_real int(11) NOT NULL DEFAULT 0, 
                qtd_cedula_100_real int(11) NOT NULL DEFAULT 0, 
                qtd_cedula_50_real  int(11) NOT NULL DEFAULT 0,
                qtd_cedula_20_real  int(11) NOT NULL DEFAULT 0, 
                qtd_cedula_10_real  int(11) NOT NULL DEFAULT 0, 
                qtd_cedula_5_real   int(11) NOT NULL DEFAULT 0,
                qtd_cedula_2_real   int(11) NOT NULL DEFAULT 0, 
                qtd_cedula_1_real   int(11) NOT NULL DEFAULT 0, 
                qtd_cedula_50_cents int(11) NOT NULL DEFAULT 0,
                qtd_cedula_25_cents int(11) NOT NULL DEFAULT 0, 
                qtd_cedula_10_cents int(11) NOT NULL DEFAULT 0, 
                qtd_cedula_5_cents  int(11) NOT NULL DEFAULT 0
            )
        ');

        // Mock da dependência IDatabase
        $databaseStub = $this->createStub(\app\contracts\core\IDatabase::class);
        $databaseStub->method('getConnection')->willReturn($this->pdo);

        $this->dao = new CaixaEletronicoDao($databaseStub);
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
}