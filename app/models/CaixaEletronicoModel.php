<?php
namespace app\models;
use app\core\Logger;
use app\dao\CaixaEletronicoDao;
class CaixaEletronicoModel extends ContratoModel {

    private CaixaEletronicoDao $caixaDao;
    private $especie = [
        200 => 0,
        100 => 0,
        50 => 0,
        20 => 0,
        10 => 0,
        5 => 0,
        2 => 0,
        1 => 0,
        0.50 => 0,
        0.25 => 0,
        0.10 => 0,
        0.05 => 0
    ];

    public function __construct() {
        $this->caixaDao = new CaixaEletronicoDao();
        $this->especie = $this->caixaDao->getQtdCadaEspecie();
    }

    public function getEspecie() {
        return $this->especie;
    }

    public function setEspecie($especie) {
        $this->especie = $especie;
    }

    public function getStatus() {
        return [
            'especie' => $this->especie,
            'total' => $this->getValorTotal()
        ];
    }

    // Por padrão ao carregar insere 10 unidades de cada cédula e moeda
    public function calcularCarregamento($qtdNotas = 10) {
        $copia = $this->especie;
        foreach ($copia as $valor => $quantidade) {
            $copia[$valor] += $qtdNotas;
        }
        return $copia;
    }

    public function calcularDescarregamento() {
        // Zera todas as céduas e moedas do caixa
        $copia = $this->especie;
        foreach ($copia as $valor => $quantidade) {
            $copia[$valor] = 0;
        }
        return $copia;
    }

    public function getCedulasParaSaque($valor) {
        $valorRestante = $valor;
        $especieParaSacar = [];
        try{
            $especieParaSacar = $this->getComposicaoCedulasSaque($valor, $this->especie);
            return $especieParaSacar;
        } catch (\Exception $e) {
            throw new \Exception("Erro ao obter cédulas para saque: " . $e->getMessage());
        }

        // // Remove as cédulas do caixa
        // foreach ($especieParaSacar as $cedula => $quantidade) {
        //     $this->especie[$cedula] -= $quantidade;
        // }

        // $resultado = array_merge($especieParaSacar);
        
        // Logger::log("Saque de R$ " . number_format($valor, 2) . " realizado. Composição: " . json_encode($resultado));
        // return $resultado;
    }

    public function calculaRemocaoCedulasSaque($cedulasSaque) {
        //EMANUEL NECESSÁRIO REALIZAR REVISÃO DE REGRA
        $copia = $this->especie;
        foreach ($cedulasSaque as $cedula => $quantidade) {
            $copia[$cedula] -= $quantidade;
        }

        $resultado = array_merge($cedulasSaque);
        return $resultado;
    }

    public function calculaDepositoCaixa($especieDepositadas) {
        $copia = $this->especie;
        // Processa cédulas
        foreach ($especieDepositadas as $cedula => $quantidade) {
            if (array_key_exists($cedula, $this->especie)) {
                if ($quantidade < 0) {
                    throw new \Exception("Quantidade não pode ser negativa");
                }
                $copia[$cedula] += $quantidade;
            } else {
                throw new \Exception("Cédula de R$ " . number_format($cedula, 2) . " não é aceita");
            }
        }
        return $copia;
    }

    public function getValorTotal() {
        $total = 0;

        foreach ($this->especie as $cedula => $quantidade) {
            $total += $cedula * $quantidade;
        }

        return $total;
    }
}
?>