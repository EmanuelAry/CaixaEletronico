<?php
namespace tests\unit\services;

use PHPUnit\Framework\TestCase;
use app\services\CaixaEletronicoService;
use app\contracts\models\ICaixaEletronicoModel;
use app\contracts\dao\ICaixaEletronicoDao;
use app\contracts\core\ILogger;
use app\core\Notification;

class CaixaEletronicoServiceTest extends TestCase{
    private $caixaEletronicoModelMock;
    private $caixaEletronicoDaoMock;
    private $loggerMock;
    private $notifications;
    private $caixaEletronicoService;

    protected function setUp(): void {
        $this->caixaEletronicoModelMock = $this->createMock(ICaixaEletronicoModel::class);
        $this->caixaEletronicoDaoMock = $this->createMock(ICaixaEletronicoDao::class);
        $this->loggerMock = $this->createMock(ILogger::class);
        //Não feito mock pois nesse caso as notificações serão utilizadas para validar os asserts
        $this->notifications = new Notification();

        $this->caixaEletronicoService = new CaixaEletronicoService(
            $this->caixaEletronicoModelMock,
            $this->caixaEletronicoDaoMock,
            $this->loggerMock,
            $this->notifications
        );
    }

    public function testValidarSaqueCaixaEletronico(){
        $regraSaque = 0; // PADRÃO
        $valorSaque = 100;
        $this->caixaEletronicoModelMock->method('setEstrategia');
        $this->caixaEletronicoModelMock->method('getValorTotal')->willReturn(10000);
        $this->caixaEletronicoModelMock->method('getCedulasParaSaque')->willReturn([100 => 1]);
        $this->caixaEletronicoDaoMock->method('salvaQTDCedulasNoBanco');
        $this->caixaEletronicoModelMock->method('setCedulas');

        $this->caixaEletronicoService->saqueCaixaEletronico($valorSaque, $regraSaque);

        $notifications = $this->notifications->getNotifications();
        $this->assertCount(1, $notifications, "Numero de notificação não bate com o estipulado no teste");
        $this->assertEquals('Saque no Caixa no valor de R$ ' . number_format($valorSaque, 2) . ' realizado com sucesso.', $notifications[0]['message'], "Mensagem da notificação não bate com o estipulado no teste");
        $this->assertEquals('success', $notifications[0]['type'], "tipo da notificação não bate com o estipulado no teste");
        
    } 
    
}