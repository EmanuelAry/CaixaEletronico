<?php

namespace App\Services;
use app\contracts\models\IContaModel;
use app\contracts\dao\IContaDao;
use app\contracts\services\ICaixaEletronicoService;
use app\contracts\core\ILogger;
use app\contracts\core\INotification;
use app\contracts\services\IContaService;
use Exception;

class ContaService implements IContaService {

    private $ContaModel;
    private $ContaDao;
    private $logger;
    private $notifications;
    private $CaixaEletronicoService;

    public function __construct(IContaModel $contaModel, IContaDao $contaDao, ILogger $logger, INotification $notifications, ICaixaEletronicoService $caixaEletronicoService) {
        $this->ContaModel = $contaModel;
        $this->ContaDao = $contaDao;
        $this->logger = $logger;
        $this->notifications = $notifications;
        $this->CaixaEletronicoService = $caixaEletronicoService;
    }

    public function loginConta($contaId, $contaSenha) {
        try{
            if(!isset($contaId, $contaSenha) || empty($contaId) || empty($contaSenha)){
                throw new \Exception('ID da conta ou senha inválidos');
            }
            $conta = $this->ContaDao->getContaById($contaId);
            if ($conta && password_verify($contaSenha, $conta['conta_senha'])) {
                $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo'], $conta['conta_email']);
                $_SESSION['conta_id'] = $conta['conta_id'];
                $this->logger->log("Conta logada com ID: " . $contaId);
                $this->notifications->add("Login realizado com sucesso", "success");
            } else {
                $this->notifications->add("ID da conta ou senha inválidos", "error");
                throw new \Exception('ID da conta ou senha inválidos');
            }
        }catch(\Exception $e){
            $this->logger->log("Erro ao fazer login: " . $e->getMessage());
            $this->notifications->add("Erro ao fazer login", "error");
            throw new \Exception('Erro ao fazer login');
        }
    }

    public function alternarConta($contaId) {
        try{
            if(!isset($contaId) || empty($contaId)){
                throw new \Exception('ID da conta inválido');
            }
            $conta = $this->ContaDao->getContaById($contaId);
            if ($conta) {
                $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo'], $conta['conta_email']);
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

    public function criarConta($contaNome, $contaEmail, $contaSenha, $saldoInicial) {
        $dadosConta = [
            'conta_nome' => $contaNome,
            'conta_saldo' => $saldoInicial,
            'conta_email' => $contaEmail,
            'conta_senha' => password_hash($contaSenha, PASSWORD_BCRYPT)
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


    public function getInfoCaixaMenuByConta($contaId){
        try{
            if(!isset($contaId) || empty($contaId)){
                throw new \Exception('ID da conta inválido');
            }
            $conta = $this->ContaDao->getContaById($contaId);
            if($conta){
                $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo'], $conta['conta_email']);
                return [
                    'conta_id'      => $this->ContaModel->getId(),
                    'conta_nome'    => $this->ContaModel->getContaNome(),
                    'conta_saldo'   => $this->ContaModel->getSaldo(),
                    'conta_email'   => $this->ContaModel->getContaEmail()
                ];
            }else{
                $this->notifications->add("Conta não encontrada", "error");
                throw new \Exception('Conta não encontrada');
            }
        }catch(\Exception $e){
            $this->logger->log("Erro ao obter informações da conta: " . $e->getMessage());
            $this->notifications->add("Erro ao obter informações da conta", "error");
            throw new \Exception('Erro ao obter informações da conta');
        }
    }

    public function listarContas() {
        try{
            $contas = $this->ContaDao->getAllContas();
            return $contas;
        }catch(\Exception $e){
            $this->logger->log("Erro ao listar contas: " . $e->getMessage());
            $this->notifications->add("Erro ao listar contas", "error");
            throw new \Exception('Erro ao listar contas');
        }
    }


    public function valorTotalConta() {
        return $this->ContaModel->getSaldo();
    }

    public function depositoConta($contaId, $cedulas) {
        try{
            $conta = $this->ContaDao->getContaById($contaId);
            $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo'], $conta['conta_email']);
            $_SESSION['conta_id'] = $conta['conta_id'];
            $this->CaixaEletronicoService->depositoCaixaEletronico($cedulas);
            $valor = $this->CaixaEletronicoService->getCaixaEletronicoModel()->CalculaTotalByCedulas($cedulas);
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

    public function saqueConta($conta_id, $valor, $regraSaque) {
        try {
            $conta = $this->ContaDao->getContaById($conta_id);
            $this->ContaModel->loadDataConta($conta['conta_id'], $conta['conta_nome'], $conta['conta_saldo'], $conta['conta_email']);
            $_SESSION['conta_id'] = $conta['conta_id'];
            if (!$this->ContaModel->isSaquePermitidoConta($valor)) {
                $this->notifications->add("Saldo insuficiente na conta ID: " . $this->ContaModel->getId() . " para saque de R$ " . number_format($valor, 2), "error");
                $this->logger->log("Tentativa de saque na conta ID: " . $this->ContaModel->getId() . " no valor de R$ " . number_format($valor, 2) . " falhou: saldo insuficiente.");
                return;
            }
            $this->CaixaEletronicoService->saqueCaixaEletronico($valor, $regraSaque);
            $this->ContaModel->SaqueConta($valor);
            $novoSaldo = $this->ContaModel->getSaldo();
            $this->ContaDao->updateSaldo($this->ContaModel->getId(), $novoSaldo);
            $this->notifications->add("Saque de R$ " . number_format($valor, 2) . " realizado com sucesso.", "success");
            $this->logger->log("Saque de R$ " . number_format($valor, 2) . " na conta ID:" . $this->ContaModel->getId() . " realizado com sucesso.");
        } catch (\Exception $e) {
            $this->logger->log("Erro durante saque na conta ID:" . $this->ContaModel->getId() . ": " . $e->getMessage());
            $this->notifications->add("Erro ao realizar saque:". $e->getMessage(), "error");
        }
    }    
}