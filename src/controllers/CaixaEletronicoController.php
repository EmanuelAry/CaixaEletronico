<?php
namespace app\controllers;
use app\helpers\UrlHelper;
use app\contracts\services\ICaixaEletronicoService;
use app\contracts\controllers\ICaixaEletronicoController;

class CaixaEletronicoController implements ICaixaEletronicoController {
    
    private $CaixaEletronicoService;

    public function __construct(ICaixaEletronicoService $caixaEletronicoService) {
        $this->CaixaEletronicoService = $caixaEletronicoService;
    }


    public function carregarCaixaEletronicoAction() {
        $this->CaixaEletronicoService->carregarCaixaEletronico();
        // Redireciona para view
        header('Location:'. UrlHelper::baseUrl('caixa/estoqueCaixaAction'));
    }
   
    public function descarregarCaixaEletronicoAction() {
        $this->CaixaEletronicoService->descarregarCaixaEletronico();
        // Redireciona para view
        header('Location:'. UrlHelper::baseUrl('caixa/estoqueCaixaAction'));
    }

    public function saqueCaixaEletronicoAction($valor, $regra = 0) {
        $this->CaixaEletronicoService->saqueCaixaEletronico($valor, $regra);
    }

    public function depositoCaixaEletronicoAction($cedulas) {
        $this->CaixaEletronicoService->depositoCaixaEletronico($cedulas);
    }

    public function valorTotalCaixaEletronicoAction() {
        $total = $this->CaixaEletronicoService->valorTotalCaixaEletronico();
        // Exibe o valor total na view
        return $total;
    }

    public function estoqueCaixaAction(){
        try{
            $aViewEstoque  = $this->CaixaEletronicoService->estoqueCaixaView();
            $estoque       = $aViewEstoque['estoque'];      
            $valor_total   = $aViewEstoque['valor_total'];
            $total_cedulas = $aViewEstoque['total_cedulas'];
            $total_moedas  = $aViewEstoque['total_moedas'];
            if(isset($_SESSION['conta_id'])){
                $contaSelecionada = [];
                $contaSelecionada['conta_id']    = $_SESSION['conta_id'];
                $contaSelecionada['conta_nome']  = $_SESSION['conta_nome'];
                $contaSelecionada['conta_saldo'] = $_SESSION['conta_saldo'];
                $contaSelecionada['conta_email'] = $_SESSION['conta_email'];
            }
            include __DIR__ . '/../views/caixa/estoquecaixa.php';
        }catch(\Exception $e){
            header('Location:'. UrlHelper::baseUrl('conta/login'));
        }
    }
}