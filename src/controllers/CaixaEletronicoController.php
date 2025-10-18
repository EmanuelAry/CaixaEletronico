<?php
namespace app\controllers;
use app\contracts\models\ICaixaEletronicoModel;
use app\contracts\dao\ICaixaEletronicoDao;
use app\contracts\core\ILogger;
use app\contracts\core\INotification;
use app\contracts\controllers\ICaixaEletronicoController;
class CaixaEletronicoController implements ICaixaEletronicoController {
    private $CaixaEletronicoModel;
    private $CaixaEletronicoDao;
    private $logger;
    private $notification;

    public function __construct(ICaixaEletronicoModel $caixaEletronicoModel, ICaixaEletronicoDao $caixaEletronicoDao, ILogger $logger, INotification $notification) {
        $this->CaixaEletronicoModel = $caixaEletronicoModel;
        $this->CaixaEletronicoDao = $caixaEletronicoDao;
        $this->logger = $logger;
        $this->notification = $notification;
    }

    public function carregarCaixaEletronicoAction() {
        $novasCedulas = $this->CaixaEletronicoModel->calcularCarregamento();
        try {
            $this->CaixaEletronicoDao->salvaQTDCedulasNoBanco($novasCedulas);
            // Se salvar com sucesso, atualiza o model
            $this->CaixaEletronicoModel->setCedulas($novasCedulas);
            $this->notification->add('Caixa carregado com sucesso.', 'success');
            $this->logger->log("Caixa carregado com 10 unidades de cada cédula e moeda.");
        } catch (\Exception $e) {
            $this->notification->add('Erro ao carregar o caixa. Tente novamente.', 'error');
            $this->logger->log("Erro ao carregar caixa: " . $e->getMessage());
        }
        // Redireciona ou exibe a view
    }

    public function descarregarCaixaEletronicoAction() {
        $novasCedulas = $this->CaixaEletronicoModel->calcularDescarregamento();
        try {
            $this->CaixaEletronicoDao->salvaQTDCedulasNoBanco($novasCedulas);
            // Se salvar com sucesso, atualiza o model
            $this->CaixaEletronicoModel->setCedulas($novasCedulas);
            $this->notification->add('Caixa descarregado com sucesso.', 'success');
            $this->logger->log("Caixa descarregado completamente.");
        } catch (\Exception $e) {
            $this->notification->add('Erro ao descarregar o caixa. Tente novamente.', 'error');
            $this->logger->log("Erro ao descarregar caixa: " . $e->getMessage());
        }
        // Redireciona ou exibe a view
    }

    public function saqueCaixaEletronicoAction($valor) {
        $valorTotalCaixa = $this->CaixaEletronicoModel->getValorTotal();
        if ($valor <= 0) {
            $this->notification->add('Erro ao realizar o saque. Valor deve ser positivo.', 'error');
            $this->logger->log("Erro ao realizar saque: valor deve ser positivo.");
            throw new \Exception("Valor de saque inválido.");
        }
        if ($valor > $valorTotalCaixa) {
            $this->notification->add("Saldo insuficiente no caixa para o saque de R$ $valor.", 'error');
            $this->logger->log("Tentativa de saque de R$ $valor falhou: saldo insuficiente no caixa.");
            throw new \Exception("Saldo insuficiente no caixa.");
            return;
        }

        $CedulasSaque = $this->CaixaEletronicoModel->getCedulasParaSaque($valor);
        $this->CaixaEletronicoDao->salvaQTDCedulasNoBanco($CedulasSaque);
        $this->CaixaEletronicoModel->setCedulas($CedulasSaque);
        $this->notification->add('Saque no Caixa no valor de R$ ' . number_format($valor, 2) . ' realizado com sucesso.', 'success');
        $this->logger->log("Saque no Caixa no valor de R$ " . number_format($valor, 2) . " realizado com sucesso.");
        
        // Redireciona ou exibe a view
    }

    public function depositoCaixaEletronicoAction($cedulas) {
        $CedulasNoCaixa = $this->CaixaEletronicoModel->calculaDepositoCaixa($cedulas);
        $totalDepositado = $this->CaixaEletronicoModel->CalculaTotalByCedulas($cedulas);
        $this->CaixaEletronicoDao->salvaQTDCedulasNoBanco($CedulasNoCaixa);
        $this->CaixaEletronicoModel->setCedulas($CedulasNoCaixa);
        $this->notification->add('Depósito realizado com sucesso.', 'success');
        //EMANUEL REVISAR REGRA NO CASO DE CENTÁVOS
        $this->logger->log("Deposito de R$ " . number_format($totalDepositado, 2) . " realizado com sucesso no caixa.");
        // Redireciona ou exibe a view
    }

    public function valorTotalCaixaEletronicoAction() {
        $total = $this->CaixaEletronicoModel->getValorTotal();
        // Exibe o valor total na view
        return $total;
    }

    // EMANUEL REVISAR POSTERIORMENTE
    // Método para exibir a view do caixa
    // public function indexAction() {
    //     $cedulas = $this->CaixaEletronicoModel->getCedulas();
    //     $total = $this->CaixaEletronicoModel->getValorTotal();
    //     // Inclui a view
    //     include 'views/caixa/index.php';
    // }
}