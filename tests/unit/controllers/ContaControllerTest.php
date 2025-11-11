<?php
namespace tests\unit\controllers;

use PHPUnit\Framework\TestCase;
use app\controllers\ContaController;
use app\contracts\models\IContaModel;
use app\contracts\dao\IContaDao;
use app\contracts\controllers\ICaixaEletronicoController;
use app\contracts\core\ILogger;
use app\contracts\core\INotification;

class ContaControllerTest extends TestCase
{
    private $contaModelMock;
    private $contaDaoMock;
    private $caixaEletronicoMock;
    private $loggerMock;
    private $notificationMock;
    private $contaController;

    protected function setUp(): void
    {
        $this->contaModelMock = $this->createMock(IContaModel::class);
        $this->contaDaoMock = $this->createMock(IContaDao::class);
        $this->caixaEletronicoMock = $this->createMock(ICaixaEletronicoController::class);
        $this->loggerMock = $this->createMock(ILogger::class);
        $this->notificationMock = $this->createMock(INotification::class);

        $this->contaController = new ContaController(
            $this->contaModelMock,
            $this->contaDaoMock,
            $this->caixaEletronicoMock,
            $this->loggerMock,
            $this->notificationMock
        );
    }
    
    public function testCriarContaComSucesso()
    {
        $this->contaDaoMock->method('createConta')->willReturn(true);
        $this->loggerMock->expects($this->once())->method('log');
        $this->notificationMock->expects($this->once())->method('add');

        $this->contaController->criarConta('Nova Conta', 100.00);
    }

    public function testAlternarContaComSucesso()
    {
        $contaId = 1;
        $contaData = ['conta_id' => 1, 'conta_nome' => 'Conta Teste', 'conta_saldo' => 100.00];

        $this->contaDaoMock->method('getContaById')->willReturn($contaData);
        $this->contaModelMock->expects($this->once())->method('loadDataConta');
        $this->loggerMock->expects($this->once())->method('log');
        $this->notificationMock->expects($this->once())->method('add');

        $this->contaController->alternarConta($contaId);
        
        $this->assertEquals($contaId, $_SESSION['conta_id']);
    }

    public function testAlternarContaComContaNaoEncontrada()
    {
        $this->contaDaoMock->method('getContaById')->willReturn(null);
        $this->notificationMock->expects($this->once())->method('add');
        $this->loggerMock->expects($this->once())->method('log');

        $this->expectException(\Exception::class);
        $this->contaController->alternarConta(999);
    }

    public function testListarContas()
    {
        $expected = [['conta_id' => 1, 'conta_nome' => 'Conta Teste']];
        $this->contaDaoMock->method('getAllContas')->willReturn($expected);

        $result = $this->contaController->listarContas();
        
        $this->assertEquals($expected, $result);
    }

    public function testSaqueContaComSaldoInsuficiente()
    {
        $_POST = [
            'regra_saque' => 0,
            'valor_saque' => 150.00,
            'conta_id' => 1
        ];

        $contaData = ['conta_id' => 1, 'conta_nome' => 'Conta Teste', 'conta_saldo' => 100.00];
        
        $this->contaDaoMock->method('getContaById')->willReturn($contaData);
        $this->contaModelMock->method('isSaquePermitidoConta')->willReturn(false);
        $this->notificationMock->expects($this->once())->method('add');
        $this->loggerMock->expects($this->once())->method('log');

        $this->contaController->saqueContaAction();
    }

    public function testDepositoContaAction()
    {
        $_POST = [
            'cedulas' => '{"100":2, "50":1}',
            'conta_id' => 1
        ];

        $contaData = ['conta_id' => 1, 'conta_nome' => 'Conta Teste', 'conta_saldo' => 100.00];
        $caixaModelMock = $this->createMock(\app\contracts\models\ICaixaEletronicoModel::class);
        
        $this->contaDaoMock->method('getContaById')->willReturn($contaData);
        $this->caixaEletronicoMock->method('getCaixaEletronicoModel')->willReturn($caixaModelMock);
        $caixaModelMock->method('CalculaTotalByCedulas')->willReturn(250.00);
        
        $this->caixaEletronicoMock->expects($this->once())->method('depositoCaixaEletronicoAction');
        $this->contaModelMock->expects($this->once())->method('DepositoConta');
        $this->contaDaoMock->expects($this->once())->method('updateSaldo');
        $this->notificationMock->expects($this->once())->method('add');

        $this->contaController->depositoContaAction();
    }

    protected function tearDown(): void
    {
        unset($_SESSION['conta_id']);
        unset($_POST);
    }
}