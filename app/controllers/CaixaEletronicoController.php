<?php
use app\models\CaixaEletronicoModel;
use app\core\Logger;
use app\core\Notification;
class CaixaEletronicoController {
    private $model;
    private $logger;

    public function __construct() {
        $this->model = new CaixaEletronicoModel();
        $this->logger = new Logger();
    }

    public function carregarAction() {

        $this->model->carregar();
        Notification::add('Caixa carregado com sucesso.', 'success');
        $this->logger->log("Caixa carregado com 10 unidades de cada cédula e moeda.");
        // Redireciona ou exibe a view
    }

    public function descarregarAction() {
        $this->model->descarregar();
        Notification::add('Caixa descarregado.', 'info');
        $this->logger->log("Caixa descarregado.");
        // Redireciona ou exibe a view
    }

    public function saqueAction($valor) {
        try {
            $resultado = $this->model->sacar($valor);
            if ($resultado !== false) {
                $mensagem = "Saque de R$ $valor realizado. Composição: ";
                foreach ($resultado as $denominacao => $quantidade) {
                    $mensagem .= "$quantidade x R$ $denominacao, ";
                }
                $mensagem = rtrim($mensagem, ', ');
                Notification::add($mensagem, 'success');
                $this->logger->log("Saque de R$ $valor realizado. Composição: " . json_encode($resultado));
            } else {
                Notification::add("Não foi possível realizar o saque de R$ $valor. Valor não pode ser composto com as cédulas e moedas disponíveis.", 'error');
                $this->logger->log("Tentativa de saque de R$ $valor falhou: valor não pode ser composto.");
            }
        } catch (Exception $e) {
            Notification::add($e->getMessage(), 'error');
            $this->logger->log("Erro durante saque: " . $e->getMessage());
        }
        // Redireciona ou exibe a view
    }

    public function depositoAction($cedulas, $moedas) {
        try {
            $this->model->depositar($cedulas, $moedas);
            Notification::add('Depósito realizado com sucesso.', 'success');
            $this->logger->log("Depósito realizado. Cédulas: " . json_encode($cedulas) . " Moedas: " . json_encode($moedas));
        } catch (Exception $e) {
            Notification::add($e->getMessage(), 'error');
            $this->logger->log("Erro durante depósito: " . $e->getMessage());
        }
        // Redireciona ou exibe a view
    }

    public function valorTotalAction() {
        $total = $this->model->getValorTotal();
        // Exibe o valor total na view
        return $total;
    }

    // Método para exibir a view do caixa
    public function indexAction() {
        $especie = $this->model->getEspecie();
        $total = $this->model->getValorTotal();
        // Inclui a view
        include 'views/caixa/index.php';
    }
}