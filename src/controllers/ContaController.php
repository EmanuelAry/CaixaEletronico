<?php
namespace app\controllers;
use app\contracts\services\IContaService;
use app\contracts\controllers\IContaController;
use app\helpers\UrlHelper;

class ContaController implements IContaController {

    private $ContaService;

    public function __construct(IContaService $contaService) {
        $this->ContaService = $contaService;
    }


    // ---- ACTIONS ---- //

    public function saqueContaAction(){
        $regra          = $_POST['regra_saque'] ?? null;// 0 = padrao; 1 = alternativa;
        $valor_saque    = $_POST['valor_saque'] ?? null;
        $conta_id       = $_POST['conta_id'] ?? null;
        //FORMATA O VALOR DO SAQUE
        $valor          = (float) round($valor_saque, 2);
        $this->ContaService->saqueConta($conta_id, $valor, $regra);
        header('Location:'. UrlHelper::baseUrl('conta/menuCaixaView'));
    }

    public function depositoContaAction(){
        $sCedulas = $_POST['cedulas'] ?? null;
        $conta_id = $_POST['conta_id'] ?? null;
        $aCedulas = json_decode($sCedulas, true);
        $cedulas = [];
        //tratamento dos dados do json para formato correto 
        foreach ($aCedulas as $cedula => $quantidade) {
            //remove as aspas, tratamento necessário pela trasnação em json
            $cedulaFormat = str_replace(["'",'"'], "", $cedula);
            if($cedulaFormat == "0.5" || $cedulaFormat == "0.1"){
                $cedulaFormat .= "0";
            }
            $cedulas[$cedulaFormat] = $quantidade;
        }
        $this->ContaService->depositoConta($conta_id, $cedulas);
        header('Location:'. UrlHelper::baseUrl('conta/menuCaixaView'));
    }  

    public function valorTotalContaAction() {
        $valor = $this->ContaService->valorTotalConta();
        return $valor;
    }
    
    public function logoutContaAction(){
        session_destroy();
        header('Location:'. UrlHelper::baseUrl('conta/login')); 
    }

    public function loginContaAction(){
       if(isset($_POST['conta_email']) && isset($_POST['conta_senha'])){
            $contaEmail = $_POST['conta_email'] ?? null;
            $contaSenha = $_POST['conta_senha'] ?? null;
        }
        if(!isset($contaEmail, $contaSenha) || empty($contaEmail) || empty($contaSenha)){
            header('Location:'. UrlHelper::baseUrl('conta/login'));
            exit; 
        }
        try{
            $this->ContaService->loginConta($contaEmail, $contaSenha);
            // Redireciona para a tela do caixa eletrônico após entrar
            header('Location:'. UrlHelper::baseUrl('conta/menuCaixaView'));
        }catch(\Exception $e){
            header('Location:'. UrlHelper::baseUrl('conta/login'));
        }    
        exit; 
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
            $this->ContaService->alternarConta($contaId);
            // Redireciona para a tela do caixa eletrônico após entrar
            header('Location:'. UrlHelper::baseUrl('conta/menuCaixaView'));
        }catch(\Exception $e){
            header('Location:'. UrlHelper::baseUrl('conta/login'));
        }    
        exit;
    }

    public function criarContaAction() {
        $contaNome = $_POST['conta_nome'] ?? null;
        $saldoInicial = $_POST['saldo_inicial'] ?? 0;
        $contaEmail = $_POST['conta_email'] ?? null;
        $contaSenha = $_POST['conta_senha'] ?? null;

        if ($contaNome && $contaEmail && $contaSenha) {
            $this->ContaService->criarConta($contaNome, $contaEmail, $contaSenha, $saldoInicial);
            // Redireciona para a tela de seleção de conta após criar
            header('Location:'. UrlHelper::baseUrl('conta/login'));
            exit;
        } else {
            header('Location:'. UrlHelper::baseUrl('conta/criar'));
            exit;
        }
    }
    
    // ---- VIEWS ---- //

    public function menuCaixaView() {
        try{
            if(isset($_SESSION['conta_id'])){
                $contaSelecionada = $this->ContaService->getInfoCaixaMenuByConta($_SESSION['conta_id']);
                $_SESSION['conta_id']    = $contaSelecionada['conta_id'];
                $_SESSION['conta_nome']  = $contaSelecionada['conta_nome'];
                $_SESSION['conta_saldo'] = $contaSelecionada['conta_saldo'];
                $_SESSION['conta_email'] = $contaSelecionada['conta_email'];
                // Inclui a view do menu do caixa eletrônico com base nos dados da conta logada
                include __DIR__ . '/../views/caixa/saquedepositocaixa.php';
            }else{
                throw new \Exception;
            }
        }catch(\Exception $e){
            header('Location:'. UrlHelper::baseUrl('conta/login'));
        }
    }

    public function loginContaView() {
        try{
            if(isset($_SESSION['conta_id'])){
                $contaSelecionada = $this->ContaService->getInfoCaixaMenuByConta($_SESSION['conta_id']);
                $_SESSION['conta_id']    = $contaSelecionada['conta_id'];
                $_SESSION['conta_nome']  = $contaSelecionada['conta_nome'];
                $_SESSION['conta_saldo'] = $contaSelecionada['conta_saldo'];
                $_SESSION['conta_email'] = $contaSelecionada['conta_email'];
            }
            $contas = $this->ContaService->listarContas();
            // Inclui a view de login de conta
            include __DIR__ . '/../views/contas/selecionarconta.php';
        }catch(\Exception $e){
            // Inclui a view de login de conta
            include __DIR__ . '/../views/contas/selecionarconta.php';
        }
    }
    
    public function criarContaView() {
        if(isset($_SESSION['conta_id'])){
            $contaSelecionada = $this->ContaService->getInfoCaixaMenuByConta($_SESSION['conta_id']);
            $_SESSION['conta_id']    = $contaSelecionada['conta_id'];
            $_SESSION['conta_nome']  = $contaSelecionada['conta_nome'];
            $_SESSION['conta_saldo'] = $contaSelecionada['conta_saldo'];
            $_SESSION['conta_email'] = $contaSelecionada['conta_email'];
        }
        // Inclui a view de criação de conta
        include __DIR__ . '/../views/contas/criarconta.php';
    }
    
}