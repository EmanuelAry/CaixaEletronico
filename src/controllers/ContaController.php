<?php
namespace app\controllers;
use app\contracts\models\IContaModel;
use app\contracts\dao\IContaDao;
use app\contracts\core\ILogger;
use app\contracts\core\INotification;
use app\contracts\controllers\IContaController;
use app\contracts\controllers\ICaixaEletronicoController;
class ContaController implements IContaController {

    private $ContaModel;
    private $ContaDao;
    private $CaixaEletronicoController;
    private $logger;
    private $notifications;

    public function __construct(IContaModel $contaModel, IContaDao $contaDao, ICaixaEletronicoController $caixaEletronicoController, ILogger $logger, INotification $notifications) {
        $this->ContaModel = $contaModel;
        $this->ContaDao = $contaDao;
        $this->CaixaEletronicoController = $caixaEletronicoController;
        $this->logger = $logger;
        $this->notifications = $notifications;
    }

    public function alternarConta($contaId) {
        try{
            $conta = $this->ContaDao->getContaById($contaId);
            if ($conta) {
                $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo']);
                $this->logger->log("Conta alternada para ID: " . $contaId);
                $this->notifications->add("Conta alternada com sucesso", "success");
            } else {
                // Notificar erro
                $this->notifications->add("Conta não encontrada", "error");
            }
        }catch(\Exception $e){
            $this->logger->log("Erro ao alternar conta: " . $e->getMessage());
            $this->notifications->add("Erro ao alternar conta", "error");
        }   
    }

    public function listarContas() {
        return $this->ContaDao->getAllContas();
    }

    public function criarConta($contaNome, $saldoInicial) {
        $dadosConta = [
            'conta_nome' => $contaNome,
            'conta_saldo' => $saldoInicial
        ];
        try{
            $resultado = $this->ContaDao->createConta($dadosConta);
            if ($resultado) {
                $this->logger->log("Nova conta criada: " . $contaNome);
                $this->notifications->add("Conta criada com sucesso", "success");
            } else {
                $this->logger->log("Erro ao criar conta");
                $this->notifications->add("Erro ao criar conta", "error");
            }
        }catch(\Exception $e){
            $this->logger->log("Erro ao criar conta: " . $e->getMessage());
            $this->notifications->add("Erro ao criar conta", "error");
        }
    }

    public function saqueContaAction($valor){
        try {
            if (!$this->ContaModel->isSaquePermitidoConta($valor)) {
                $this->notifications->add("Saldo insuficiente na conta ID: " . $this->ContaModel->getId() . " para saque de R$ " . number_format($valor, 2), "error");
                $this->logger->log("Tentativa de saque na conta ID: " . $this->ContaModel->getId() . " no valor de R$ " . number_format($valor, 2) . " falhou: saldo insuficiente.");
                return;
            }
            $this->CaixaEletronicoController->saqueCaixaEletronicoAction($valor);
            $this->ContaModel->SaqueConta($valor);
            $novoSaldo = $this->ContaModel->getSaldo();
            $this->ContaDao->updateSaldo($this->ContaModel->getId(), $novoSaldo);
            $this->notifications->add("Saque de R$ " . number_format($valor, 2) . " realizado com sucesso.", "success");
            $this->logger->log("Saque de R$ " . number_format($valor, 2) . " na conta ID:" . $this->ContaModel->getId() . " realizado com sucesso.");
        } catch (\Exception $e) {
            $this->logger->log("Erro durante saque na conta ID:" . $this->ContaModel->getId() . ": " . $e->getMessage());
            $this->notifications->add("Erro ao realizar saque", "error");
        }
    }

    public function depositoContaAction($cedulas){
        try {
            $this->CaixaEletronicoController->depositoCaixaEletronicoAction($cedulas);
            $valor = $this->CaixaEletronicoController->CaixaEletronicoModel->CalculaTotalByCedulas($cedulas);
            $this->ContaModel->DepositoConta($valor);
            $novoSaldo = $this->ContaModel->getSaldo();
            $this->ContaDao->updateSaldo($this->ContaModel->getId(), $novoSaldo);
            $this->notifications->add("Depósito de R$ " . number_format($valor, 2) . " realizado com sucesso.", "success");
            $this->logger->log("Depósito de R$ " . number_format($valor, 2) . " na conta ID:" . $this->ContaModel->getId() . " realizado com sucesso.");
        } catch (\Exception $e) {
            $this->logger->log("Erro durante depósito na conta ID:" . $this->ContaModel->getId() . ": " . $e->getMessage());
            $this->notifications->add("Erro ao realizar depósito", "error");
        }
    }

    public function valorTotalContaAction() {
        return $this->ContaModel->getSaldo();
    }

    public function selecionarContaAction() {
        $contas = $this->listarContas();
        $notifications = $this->notifications->getNotifications();
        // Inclui a view de seleção de conta
        include __DIR__ . '/../views/contas/selecionarconta.php';
    }

    public function criarContaView() {
        include __DIR__ . '/../views/contas/criarconta.php';
    }

    public function entrarContaAction() {
        $contaId = $_POST['conta_id'] ?? null;
        if ($contaId) {
            $this->alternarConta($contaId);
            // Redireciona para a tela do caixa eletrônico (ou outra tela) após entrar
            header('Location: /caixa');
            exit;
        } else {
            $this->notifications->add("ID da conta é obrigatório", "error");
            header('Location: /');
            exit;
        }
    }

    public function alternarContaAction($id) {
        $this->alternarConta($id);
        // Redireciona para a tela do caixa eletrônico (ou outra tela) após entrar
        header('Location: /caixa');
        exit;
    }

    public function criarContaAction() {
        $contaNome = $_POST['conta_nome'] ?? null;
        $saldoInicial = $_POST['saldo_inicial'] ?? 0;

        if ($contaNome) {
            $this->criarConta($contaNome, $saldoInicial);
            // Redireciona para a tela de seleção de conta após criar
            header('Location: /');
            exit;
        } else {
            $this->notifications->add("Nome da conta é obrigatório", "error");
            header('Location: /conta/criarconta.php');
            exit;
        }
    }

    public function listarContasView() {
        $contas = $this->listarContas();
        // Inclui a view de seleção de contas, passando $contas para a view
        include __DIR__ . '/../views/contas/selecionarconta.php';
    }
}