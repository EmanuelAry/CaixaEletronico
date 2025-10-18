<?php
use app\models\CaixaEletronicoModel;
use app\dao\CaixaEletronicoDao;
use app\core\Logger;
use app\core\Notification;
class CaixaEletronicoController {
    private $CaixaEletronicoModel;
    private $dao;
    private $logger;

    public function __construct() {
        $this->CaixaEletronicoModel = new CaixaEletronicoModel();
        $this->dao = new CaixaEletronicoDao();
        $this->logger = new Logger();
    }

    public function carregarAction() {
        $novaEspecie = $this->CaixaEletronicoModel->calcularCarregamento();
        try {
            $this->dao->salvaQTDEspecieNoBanco($novaEspecie);
            // Se salvar com sucesso, atualiza o model
            $this->CaixaEletronicoModel->setEspecie($novaEspecie);
            Notification::add('Caixa carregado com sucesso.', 'success');
            $this->logger->log("Caixa carregado com 10 unidades de cada cédula e moeda.");
        } catch (\Exception $e) {
            Notification::add('Erro ao carregar o caixa. Tente novamente.', 'error');
            $this->logger->log("Erro ao carregar caixa: " . $e->getMessage());
        }
        // Redireciona ou exibe a view
    }

    public function descarregarAction() {
        $novaEspecie = $this->CaixaEletronicoModel->calcularDescarregamento();
        try {
            $this->dao->salvaQTDEspecieNoBanco($novaEspecie);
            // Se salvar com sucesso, atualiza o model
            $this->CaixaEletronicoModel->setEspecie($novaEspecie);
            Notification::add('Caixa descarregado com sucesso.', 'success');
            $this->logger->log("Caixa descarregado completamente.");
        } catch (\Exception $e) {
            Notification::add('Erro ao descarregar o caixa. Tente novamente.', 'error');
            $this->logger->log("Erro ao descarregar caixa: " . $e->getMessage());
        }
        // Redireciona ou exibe a view

        // Redireciona ou exibe a view
    }

    public function saqueCaixaEletronicoAction($valor) {
        $valorTotalCaixa = $this->CaixaEletronicoModel->getValorTotal();
        if ($valor <= 0) {
            Notification::add('Erro ao realizar o saque. Valor deve ser positivo.', 'error');
            $this->logger->log("Erro ao realizar saque: valor deve ser positivo.");
        }
        if ($valor > $valorTotalCaixa) {
            Notification::add("Saldo insuficiente no caixa para o saque de R$ $valor.", 'error');
            $this->logger->log("Tentativa de saque de R$ $valor falhou: saldo insuficiente no caixa.");
            // Redireciona ou exibe a view
            return;
        }

        try {
            $CedulasSaque = $this->CaixaEletronicoModel->getCedulasParaSaque($valor);
            $this->dao->salvaQTDEspecieNoBanco($CedulasSaque);
            $this->CaixaEletronicoModel->setEspecie($CedulasSaque);
            Notification::add('Saque no valor de R$ ' . number_format($valor, 2) . ' realizadocom sucesso.', 'success');
            $this->logger->log("Saque no valor de R$ " . number_format($valor, 2) . " realizado com sucesso.");

        } catch (Exception $e) {
            Notification::add($e->getMessage(), 'error');
            $this->logger->log("Erro durante saque: " . $e->getMessage());
        }
        // Redireciona ou exibe a view
    }

    public function depositoCaixaEletronicoAction($cedulas) {
        try {
            $CedulasNoCaixa = $this->CaixaEletronicoModel->calculaDepositoCaixa($cedulas);
            $totalDepositado = $this->CalculaTotalByCedulas($cedulas);
            $this->dao->salvaQTDEspecieNoBanco($CedulasNoCaixa);
            $this->CaixaEletronicoModel->setEspecie($CedulasNoCaixa);
            Notification::add('Depósito realizado com sucesso.', 'success');
            //EMANUEL REVISAR REGRA NO CASO DE CENTÁVOS
            $this->logger->log("Deposito de R$ " . number_format($totalDepositado, 2) . " realizado com sucesso.");
        } catch (Exception $e) {
            Notification::add($e->getMessage(), 'error');
            $this->logger->log("Erro durante depósito: " . $e->getMessage());
        }
        // Redireciona ou exibe a view
    }

    public function CalculaTotalByCedulas($cedulas) {
        //EMANUEL NECESSÁRIO REALIZAR REVISÃO DE REGRA
        $total = 0;
        foreach ($cedulas as $denominacao => $quantidade) {
            $total += $denominacao * $quantidade;
        }
        return $total;
    }

    public function valorTotalAction() {
        $total = $this->CaixaEletronicoModel->getValorTotal();
        // Exibe o valor total na view
        return $total;
    }

    // Método para exibir a view do caixa
    public function indexAction() {
        $especie = $this->CaixaEletronicoModel->getEspecie();
        $total = $this->CaixaEletronicoModel->getValorTotal();
        // Inclui a view
        include 'views/caixa/index.php';
    }
}