<?php
namespace app\controllers;
use app\contracts\models\ICaixaEletronicoModel;
use app\contracts\dao\ICaixaEletronicoDao;
use app\contracts\core\ILogger;
use app\contracts\core\INotification;
use app\contracts\controllers\ICaixaEletronicoController;
use app\helpers\UrlHelper;
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

    public function getCaixaEletronicoModel(){
        return $this->CaixaEletronicoModel;
    }

    /**
     * Recebe a requisição para carregar o caixa eletrônico com cédulas e moedas, faz o redirecionamento para a view de estoque do caixa.
     * @throws \Exception Em caso de erro, trata os erros das funções chamadas e lança a notificação apropriada
     */
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
        // Redireciona para view
        header('Location:'. UrlHelper::baseUrl('caixa/estoqueCaixaView'));
    }

    /**
     * Recebe a requisição para descarregar o caixa eletrônico com cédulas e moedas, faz o redirecionamento para a view de estoque do caixa.
     * @throws \Exception Em caso de erro, trata os erros das funções chamadas e lança a notificação apropriada
     */    
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
        // Redireciona para view
        header('Location:'. UrlHelper::baseUrl('caixa/estoqueCaixaView'));
    }

    /**
     * Executa as ações necessárias para realizar um saque no caixa eletrônico.
     * @param float  $valor Float define o valor a ser sacado
     * @param int  $regra Inteiro define qual a regra de saque
     * @throws \Exception Em caso de erro, trata os erros das funções chamadas e lança a notificação apropriada
     */ 
    public function saqueCaixaEletronicoAction($valor, $regra = 0) {
        $this->CaixaEletronicoModel->setEstrategia($regra);
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
        $this->notification->add('Saque no Caixa no valor de R$ ' . number_format($valor, 2) . ' realizado com sucesso.', 'success');        $this->logger->log("Saque no Caixa no valor de R$ " . number_format($valor, 2) . " realizado com sucesso."); 
    }

    public function depositoCaixaEletronicoAction($cedulas) {
        $CedulasNoCaixa = $this->CaixaEletronicoModel->calculaDepositoCaixa($cedulas);
        $totalDepositado = $this->CaixaEletronicoModel->CalculaTotalByCedulas($cedulas);
        $this->CaixaEletronicoDao->salvaQTDCedulasNoBanco($CedulasNoCaixa);
        $this->CaixaEletronicoModel->setCedulas($CedulasNoCaixa);
        $this->notification->add('Depósito realizado com sucesso.', 'success');
        $this->logger->log("Deposito de R$ " . number_format($totalDepositado, 2) . " realizado com sucesso no caixa.");
    }

    public function valorTotalCaixaEletronicoAction() {
        $total = $this->CaixaEletronicoModel->getValorTotal();
        // Exibe o valor total na view
        return $total;
    }

    public function getQtdTotalCedulas(){
        $qtdNotas = 0;
        $estoque = $this->CaixaEletronicoDao->getQtdCadaCedula();
        $notas = [
            ['valor' => 200, 'quantidade' => $estoque['200'] ?? 0],
            ['valor' => 100, 'quantidade' => $estoque['100'] ?? 0],
            ['valor' => 50,  'quantidade' => $estoque['50']  ?? 0],
            ['valor' => 20,  'quantidade' => $estoque['20']  ?? 0],
            ['valor' => 10,  'quantidade' => $estoque['10']  ?? 0],
            ['valor' => 2,   'quantidade' => $estoque['2']   ?? 0]
        ];
        foreach($notas as $nota){
            $qtdNotas += $nota['quantidade'];
        }
        return $qtdNotas;
    }

    public function getQtdTotalMoedas(){
        $qtdMoedas = 0;
        $estoque = $this->CaixaEletronicoDao->getQtdCadaCedula();
        $moedas = [
            ['valor' => 1,    'quantidade' => $estoque['1']    ?? 0],
            ['valor' => 0.50, 'quantidade' => $estoque['0.50'] ?? 0],
            ['valor' => 0.25, 'quantidade' => $estoque['0.25'] ?? 0],
            ['valor' => 0.10, 'quantidade' => $estoque['0.10'] ?? 0],
            ['valor' => 0.05, 'quantidade' => $estoque['0.05'] ?? 0],
        ];
        foreach($moedas as $moeda){
            $qtdMoedas += $moeda['quantidade'];
        }
        return $qtdMoedas;
    }

    public function estoqueCaixaView(){
        try{
            $estoque        = $this->CaixaEletronicoDao->getQtdCadaCedula();
            $valor_total    = $this->valorTotalCaixaEletronicoAction();
            $total_cedulas  = $this->getQtdTotalCedulas();
            $total_moedas   = $this->getQtdTotalMoedas();
            include __DIR__ . '/../views/caixa/estoquecaixa.php';
        }catch(\Exception $e){
            $this->notification->add("Erro na busca de estoque", "error");
            header('Location:'. UrlHelper::baseUrl('conta/selecionar'));
        }
    }
}