<?php
namespace app\controllers;
use app\contracts\models\IContaModel;
use app\contracts\dao\IContaDao;
use app\contracts\core\ILogger;
use app\contracts\core\INotification;
use app\contracts\controllers\IContaController;
use app\contracts\controllers\ICaixaEletronicoController;
use app\helpers\UrlHelper;

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
                $_SESSION['conta_id'] = $conta['conta_id'];
                $this->logger->log("Conta alternada para ID: " . $contaId);
                $this->notifications->add("Conta alternada com sucesso", "success");
            } else {
                // Notificar erro
                $this->notifications->add("Conta não encontrada", "error");
                throw new \Exception('Não foi possível alternar entre contas');
            }
        }catch(\Exception $e){
            $this->logger->log("Erro ao alternar conta: " . $e->getMessage());
            $this->notifications->add("Erro ao alternar conta", "error");
            throw new \Exception('Não foi possível alternar entre contas');
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

    public function saqueContaAction(){
        $regra          = $_POST['regra_saque'] ?? null;// 0 = padrao; 1 = alternativa;
        $valor_saque    = $_POST['valor_saque'] ?? null;
        $conta_id       = $_POST['conta_id'] ?? null;
        $valor          = (float) round($valor_saque, 2);
        try {
            $conta = $this->ContaDao->getContaById($conta_id);
            $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo']);
            $_SESSION['conta_id'] = $conta['conta_id'];
            if (!$this->ContaModel->isSaquePermitidoConta($valor)) {
                $this->notifications->add("Saldo insuficiente na conta ID: " . $this->ContaModel->getId() . " para saque de R$ " . number_format($valor, 2), "error");
                $this->logger->log("Tentativa de saque na conta ID: " . $this->ContaModel->getId() . " no valor de R$ " . number_format($valor, 2) . " falhou: saldo insuficiente.");
                header('Location:'. UrlHelper::baseUrl('conta/menuCaixaView'));
                return;
            }
            $this->CaixaEletronicoController->saqueCaixaEletronicoAction($valor, $regra);
            $this->ContaModel->SaqueConta($valor);
            $novoSaldo = $this->ContaModel->getSaldo();
            $this->ContaDao->updateSaldo($this->ContaModel->getId(), $novoSaldo);
            $this->notifications->add("Saque de R$ " . number_format($valor, 2) . " realizado com sucesso.", "success");
            $this->logger->log("Saque de R$ " . number_format($valor, 2) . " na conta ID:" . $this->ContaModel->getId() . " realizado com sucesso.");
        } catch (\Exception $e) {
            $this->logger->log("Erro durante saque na conta ID:" . $this->ContaModel->getId() . ": " . $e->getMessage());
            $this->notifications->add("Erro ao realizar saque:". $e->getMessage(), "error");
        }
        header('Location:'. UrlHelper::baseUrl('conta/menuCaixaView'));
    }

    public function depositoContaAction(){
        $sCedulas = $_POST['cedulas'] ?? null;
        $conta_id = $_POST['conta_id'] ?? null;
        $aCedulas = json_decode($sCedulas, true);
        $cedulas = [];
        //TRATAMENTO DOS DADOS DO JSON 
        foreach ($aCedulas as $cedula => $quantidade) {
            //REMOVE AS ASPAS TRATAMENTO NECESSÁIRO PELA TRANSAÇÃO EM JSON
            $cedulaFormat = str_replace(["'",'"'], "", $cedula);
            if($cedulaFormat == "0.5" || $cedulaFormat == "0.1"){
                $cedulaFormat .= "0";
            }
            $cedulas[$cedulaFormat] = $quantidade;
        }
        try {
            $conta = $this->ContaDao->getContaById($conta_id);
            $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo']);
            $_SESSION['conta_id'] = $conta['conta_id'];
            $this->CaixaEletronicoController->depositoCaixaEletronicoAction($cedulas);
            $valor = $this->CaixaEletronicoController->getCaixaEletronicoModel()->CalculaTotalByCedulas($cedulas);
            $this->ContaModel->DepositoConta($valor);
            $novoSaldo = $this->ContaModel->getSaldo();
            $this->ContaDao->updateSaldo($this->ContaModel->getId(), $novoSaldo);
            $this->notifications->add("Depósito de R$ " . number_format($valor, 2) . " realizado com sucesso.", "success");
            $this->logger->log("Depósito de R$ " . number_format($valor, 2) . " na conta ID:" . $this->ContaModel->getId() . " realizado com sucesso.");
        } catch (\Exception $e) {
            $this->logger->log("Erro durante depósito na conta ID:" . $this->ContaModel->getId() . ": " . $e->getMessage());
            $this->notifications->add("Erro ao realizar depósito", "error");
        }
        header('Location:'. UrlHelper::baseUrl('conta/menuCaixaView'));
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
    
    public function alternarContaAction() {
        if(isset($_POST['conta_id'])){
            $contaId = $_POST['conta_id'] ?? null;
        }else if(isset($_GET['conta_id'])){
            $contaId = $_GET['conta_id'] ?? null;
        }else{
            $contaId = null;
        }
        try{
            $this->alternarConta($contaId);
            // Redireciona para a tela do caixa eletrônico (ou outra tela) após entrar
            header('Location:'. UrlHelper::baseUrl('conta/menuCaixaView'));
        }catch(\Exception $e){
            $this->notifications->add("Conta não encontrada", "error");
            header('Location:'. UrlHelper::baseUrl('conta/selecionar'));

        }    
        exit;
    }

    public function criarContaAction() {
        $contaNome = $_POST['conta_nome'] ?? null;
        $saldoInicial = $_POST['saldo_inicial'] ?? 0;
        
        if ($contaNome) {
            $this->criarConta($contaNome, $saldoInicial);
            // Redireciona para a tela de seleção de conta após criar
            header('Location:'. UrlHelper::baseUrl('conta/selecionar'));
            exit;
        } else {
            $this->notifications->add("Erro na criação da conta", "error");
            header('Location:'. UrlHelper::baseUrl('conta/criar'));
            exit;
        }
    }
    
    public function menuCaixaView() {
        try{
            $conta = $this->ContaDao->getContaById($_SESSION['conta_id']);
            if($conta){
                $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo']);
                $contaSelecionada = [
                    'conta_id'      => $this->ContaModel->getId(),
                    'conta_nome'    => $this->ContaModel->getContaNome(),
                    'conta_saldo'   =>$this->ContaModel->getSaldo()
                ];
                include __DIR__ . '/../views/caixa/saquedepositocaixa.php';
            }else{
                $this->notifications->add("Conta não encontrada", "error");
                header('Location:'. UrlHelper::baseUrl('conta/selecionar'));
            }
        }catch(\Exception $e){
            $this->notifications->add("Conta não encontrada", "error");
            header('Location:'. UrlHelper::baseUrl('conta/selecionar'));
        }
    }
    public function listarContasView() {
        $contas = $this->listarContas();
        // Inclui a view de seleção de contas, passando $contas para a view
        include __DIR__ . '/../views/contas/selecionarconta.php';
    }
    
    public function criarContaView() {
        include __DIR__ . '/../views/contas/criarconta.php';
    }
    
}