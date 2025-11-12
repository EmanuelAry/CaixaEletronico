<?php
namespace tests\unit\services;

use PHPUnit\Framework\TestCase;
use app\services\ContaService;
use app\contracts\models\IContaModel;
use app\contracts\dao\IContaDao;
use app\contracts\services\ICaixaEletronicoService;
use app\contracts\core\ILogger;
use app\core\Notification;

class ContaServiceTest extends TestCase
{
    private $contaModelMock;
    private $contaDaoMock;
    private $caixaEletronicoMock;
    private $loggerMock;
    private $notifications;
    private $contaService;

    protected function setUp(): void
    {
        $this->contaModelMock = $this->createMock(IContaModel::class);
        $this->contaDaoMock = $this->createMock(IContaDao::class);
        $this->caixaEletronicoMock = $this->createMock(ICaixaEletronicoService::class);
        $this->loggerMock = $this->createMock(ILogger::class);
        //Não feito mock pois nesse caso as notificações serão utilizadas para validar os asserts
        $this->notifications = new Notification();

        $this->contaService = new ContaService(
            $this->contaModelMock,
            $this->contaDaoMock,
            $this->loggerMock,
            $this->notifications,
            $this->caixaEletronicoMock
        );
    }
    
    public function testValidarCadastroNovaConta(){
        $this->contaDaoMock->method('createConta')->willReturn(true);
        $this->loggerMock->expects($this->once())->method('log');

        $this->contaService->criarConta('João Melão','joao.melao@email.com', 900.00, '123');
        
        //Valida as notificações geradas
        $notifications = $this->notifications->getNotifications();
        $this->assertCount(1, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals('Conta criada com sucesso', $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste");
        $this->assertEquals('success', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");

    }

    public function testValidarBloqueioCadastroNovaContaComDadoNullo(){
        $this->contaDaoMock->method('createConta')->willReturn(true);
        $this->loggerMock->method('log');
        
        $this->contaService->criarConta(null,null, null, null);
        $this->contaService->criarConta(null,'joao.melao@email.com', 900.00, '123');
        $this->contaService->criarConta('João Melão',null, 900.00, '123');
        $this->contaService->criarConta('João Melão','joao.melao@email.com', null, '123');
        $this->contaService->criarConta('João Melão','joao.melao@email.com', 900.00, null);
        
        $notifications = $this->notifications->getNotifications();

        $this->assertCount(10, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals('Existem dados nulos no cadastro de conta', $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste de todos os dados nulo");
        $this->assertEquals('error', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");
        $this->assertEquals('Existem dados nulos no cadastro de conta', $notifications[2]['message'], "Mensagem da notificação não bate com o estipulado no teste com nome nulo");
        $this->assertEquals('error', $notifications[2]['type'], "tipo da notificação não bate com o estipulado no teste");
        $this->assertEquals('Existem dados nulos no cadastro de conta', $notifications[4]['message'], "Mensagem da notificação não bate com o estipulado no teste com e-mail nul");
        $this->assertEquals('error', $notifications[4]['type'], "tipo da notificação não bate com o estipulado no teste");
        $this->assertEquals('Existem dados nulos no cadastro de conta', $notifications[6]['message'], "Mensagem da notificação não bate com o estipulado no teste conta com saldo nul");
        $this->assertEquals('error', $notifications[6]['type'], "tipo da notificação não bate com o estipulado no teste");
        $this->assertEquals('Existem dados nulos no cadastro de conta', $notifications[8]['message'], "Mensagem da notificação não bate com o estipulado no teste conta com senha nulo");
        $this->assertEquals('error', $notifications[8]['type'], "tipo da notificação não bate com o estipulado no teste");

    }

    public function testValidarBloqueioCadastroNovaContaNomeCaracteresEspeciais (){
        $this->contaDaoMock->method('createConta')->willReturn(true);
        $this->loggerMock->method('log');

        // $this->assertFalse($this->contaService->criarConta('1@#$%¨&*()_+! ','joao.melao@email.com', 900.00, '123'), 'Erro no teste ao criar conta com nome com caracteres especiais');
        $this->contaService->criarConta('1@#$%¨&*()_+! ','joao.melao@email.com', 900.00, '123');

        $notifications = $this->notifications->getNotifications();
        $this->assertCount(2, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals('O nome não pode conter caracteres especiais ou números', $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste de inclusão de nome com caracteres especiais");
        $this->assertEquals('error', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");
        

    }

    public function testValidarLoginConta() {
        $conta = [
            'conta_id'=> 1,
            'conta_nome' => 'João Melão',
            'conta_saldo' => '900,00',
            'conta_email' => 'joao.melao@email.com',
            'conta_senha' => password_hash('123', PASSWORD_BCRYPT)
        ];
        $this->contaDaoMock->method('getContaByEmail')->willReturn($conta);
        $this->contaModelMock->method('loadDataConta');
        $this->loggerMock->method('log');

        $this->contaService->loginConta('joao.melao@email.com', '123');

        $notifications = $this->notifications->getNotifications();
        $this->assertCount(1, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals('Login realizado com sucesso', $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste");
        $this->assertEquals('success', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");
    }

    public function testBloqueioLoginContaSenhaIncorreta () {
        $conta = [
            'conta_id'=> 1,
            'conta_nome' => 'João Melão',
            'conta_saldo' => '900,00',
            'conta_email' => 'joao.melao@email.com',
            'conta_senha' => password_hash('senha_correta', PASSWORD_BCRYPT)
        ];
        $this->contaDaoMock->method('getContaByEmail')->willReturn($conta);
        $this->contaModelMock->method('loadDataConta');
        $this->loggerMock->method('log');

        $this->expectException(\Exception::class);
        $this->contaService->loginConta('joao.melao@email.com', 'incorreta ');

        $notifications = $this->notifications->getNotifications();
        $this->assertCount(2, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals('Email ou senha incorretos', $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste");
        $this->assertEquals('error', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");
    }


    // public function testAlternarContaComSucesso()
    // {
    //     $contaId = 1;
    //     $contaData = ['conta_id' => 1, 'conta_nome' => 'Conta Teste', 'conta_saldo' => 100.00];

    //     $this->contaDaoMock->method('getContaById')->willReturn($contaData);
    //     $this->contaModelMock->expects($this->once())->method('loadDataConta');
    //     $this->loggerMock->expects($this->once())->method('log');
    //     $this->notificationMock->expects($this->once())->method('add');

    //     $this->contaService->alternarConta($contaId);
        
    //     $this->assertEquals($contaId, ['conta_id']);
    // }

    // public function testAlternarContaComContaNaoEncontrada()
    // {
    //     $this->contaDaoMock->method('getContaById')->willReturn(null);
    //     $this->notificationMock->expects($this->once())->method('add');
    //     $this->loggerMock->expects($this->once())->method('log');

    //     $this->expectException(\Exception::class);
    //     $this->contaService->alternarConta(999);
    // }

    // public function testListarContas()
    // {
    //     $expected = [['conta_id' => 1, 'conta_nome' => 'Conta Teste']];
    //     $this->contaDaoMock->method('getAllContas')->willReturn($expected);

    //     $result = $this->contaService->listarContas();
        
    //     $this->assertEquals($expected, $result);
    // }

    // public function testSaqueContaComSaldoInsuficiente()
    // {
    //     $_POST = [
    //         'regra_saque' => 0,
    //         'valor_saque' => 150.00,
    //         'conta_id' => 1
    //     ];

    //     $contaData = ['conta_id' => 1, 'conta_nome' => 'Conta Teste', 'conta_saldo' => 100.00];
        
    //     $this->contaDaoMock->method('getContaById')->willReturn($contaData);
    //     $this->contaModelMock->method('isSaquePermitidoConta')->willReturn(false);
    //     $this->notificationMock->expects($this->once())->method('add');
    //     $this->loggerMock->expects($this->once())->method('log');

    //     $this->contaService->saqueContaAction();
    // }

    // public function testDepositoContaAction()
    // {
    //     $_POST = [
    //         'cedulas' => '{"100":2, "50":1}',
    //         'conta_id' => 1
    //     ];

    //     $contaData = ['conta_id' => 1, 'conta_nome' => 'Conta Teste', 'conta_saldo' => 100.00];
    //     $caixaModelMock = $this->createMock(\app\contracts\models\ICaixaEletronicoModel::class);
        
    //     $this->contaDaoMock->method('getContaById')->willReturn($contaData);
    //     $this->caixaEletronicoMock->method('getCaixaEletronicoModel')->willReturn($caixaModelMock);
    //     $caixaModelMock->method('CalculaTotalByCedulas')->willReturn(250.00);
        
    //     $this->caixaEletronicoMock->expects($this->once())->method('depositoCaixaEletronicoAction');
    //     $this->contaModelMock->expects($this->once())->method('DepositoConta');
    //     $this->contaDaoMock->expects($this->once())->method('updateSaldo');
    //     $this->notificationMock->expects($this->once())->method('add');

    //     $this->contaService->depositoContaAction();
    // }

    // protected function tearDown(): void
    // {
    //     unset($_SESSION['conta_id']);
    //     unset($_POST);
    // }
}