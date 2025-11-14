<?php
namespace tests\unit\services;

use PHPUnit\Framework\TestCase;
use app\services\ContaService;
use app\contracts\models\IContaModel;
use app\contracts\dao\IContaDao;
use app\contracts\services\ICaixaEletronicoService;
use app\contracts\core\ILogger;
use app\core\Notification;

class ContaServiceTest extends TestCase{
    private $contaModelMock;
    private $contaDaoMock;
    private $caixaEletronicoMock;
    private $loggerMock;
    private $notifications;
    private $contaService;

    protected function setUp(): void {
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

    public function testValidarLoginConta(){
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

    public function testBloqueioLoginContaSenhaIncorreta(){
        $conta = [
            'conta_id'=> 1,
            'conta_nome' => 'João Melão',
            'conta_saldo' => 900.00,
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

    public function testValidarSaqueConta(){
        $regraSaque = 0;
        $valorSaque = 100;
        $contaId = 1;
        $conta = [
            'conta_id'=> $contaId,
            'conta_nome' => 'João Melão',
            'conta_saldo' => 900.00,
            'conta_email' => 'joao.melao@email.com',
            'conta_senha' => password_hash('senha_correta', PASSWORD_BCRYPT)
        ];
        $this->contaDaoMock->method('getContaById')->willReturn($conta);
        $this->contaModelMock->method('loadDataConta');
        $this->contaModelMock->method('isSaquePermitidoConta')->willReturn(true);
        $this->caixaEletronicoMock->method('saqueCaixaEletronico');
        $this->contaModelMock->method('SaqueConta');
        $this->contaModelMock->method('getSaldo')->willReturn($conta['conta_saldo'] - $valorSaque);
        $this->contaModelMock->method('getId')->willReturn($contaId);
        $this->contaDaoMock->method('updateSaldo')->willReturn($conta);
        $this->loggerMock->method('log');

        $this->contaService->saqueConta($contaId, $valorSaque, $regraSaque);

        $notifications = $this->notifications->getNotifications();
        $this->assertCount(1, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals("Saque de R$ " . number_format($valorSaque, 2) . " realizado com sucesso.", $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste");
        $this->assertEquals('success', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");
    } 

    public function testBloqueioSaqueContaSaldoInsuficiente(){
        $regraSaque = 0;
        $valorSaque = 100;
        $contaId = 1;
        $conta = [
            'conta_id'=> $contaId,
            'conta_nome' => 'João Melão',
            'conta_saldo' => 90.00,
            'conta_email' => 'joao.melao@email.com',
            'conta_senha' => password_hash('senha_correta', PASSWORD_BCRYPT)
        ];
        $this->contaDaoMock->method('getContaById')->willReturn($conta);
        $this->contaModelMock->method('loadDataConta');
        $this->contaModelMock->method('isSaquePermitidoConta')->willReturn(false);
        $this->caixaEletronicoMock->method('saqueCaixaEletronico');
        $this->contaModelMock->method('SaqueConta');
        $this->contaModelMock->method('getSaldo')->willReturn($conta['conta_saldo'] - $valorSaque);
        $this->contaModelMock->method('getId')->willReturn($contaId);
        $this->contaDaoMock->method('updateSaldo')->willReturn($conta);
        $this->loggerMock->method('log');

        $this->contaService->saqueConta($contaId, $valorSaque, $regraSaque);

        $notifications = $this->notifications->getNotifications();
        $this->assertCount(2, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals("Saldo insuficiente na conta ID: " . $contaId . " para saque de R$ " . number_format($valorSaque, 2), $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste");
        $this->assertEquals('error', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");
    } 

    public function testValidarDepositoValoresConta(){
        $contaId = 1;
        $saldoConta = 90.00;
        $valorDeposito = 70.00;
        $conta = [
            'conta_id'=> $contaId,
            'conta_nome' => 'João Melão',
            'conta_saldo' => $saldoConta,
            'conta_email' => 'joao.melao@email.com',
            'conta_senha' => password_hash('senha_correta', PASSWORD_BCRYPT)
        ];
        $cedulasDepostio = [
            '200'  => 0, 
            '100'  => 0, 
            '50'   => 1, 
            '20'   => 1, 
            '10'   => 0, 
            '5'    => 0, 
            '2'    => 0, 
            '1'    => 0, 
            '0.50' => 0, 
            '0.25' => 0, 
            '0.10' => 0, 
            '0.05' => 0 
        ];
        $this->contaDaoMock->method('getContaById')->willReturn($conta);
        $this->contaModelMock->method('loadDataConta');
        $this->caixaEletronicoMock->method('depositoCaixaEletronico');
        $caixaModelMock = $this->createMock(\app\contracts\models\ICaixaEletronicoModel::class);
        $this->caixaEletronicoMock->method('getCaixaEletronicoModel')->willReturn($caixaModelMock);
        $caixaModelMock->method('CalculaTotalByCedulas')->with($cedulasDepostio)->willReturn($valorDeposito);
        $this->caixaEletronicoMock->method('depositoCaixaEletronico')->with($cedulasDepostio);
        $this->contaModelMock->method('DepositoConta');
        $this->contaModelMock->method('getSaldo')->willReturn($saldoConta + $valorDeposito);
        $this->contaModelMock->method('getId')->willReturn($contaId);
        $this->contaDaoMock->method('updateSaldo');
        $this->loggerMock->method('log');

        $this->contaService->depositoConta($contaId, $cedulasDepostio);

        $notifications = $this->notifications->getNotifications();
        $this->assertCount(1, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals("Depósito de R$ " . number_format($valorDeposito, 2) . " realizado com sucesso.", $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste");
        $this->assertEquals('success', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");
    } 
}