<?php
namespace app\models;
use app\models\ContratoModel;
use app\contracts\models\ICaixaEletronicoModel;
use app\contracts\dao\ICaixaEletronicoDao;
use app\dao\CaixaEletronicoDao;

class CaixaEletronicoModel extends ContratoModel implements ICaixaEletronicoModel {

    private CaixaEletronicoDao $caixaDao;
    private $cedula = [
        '200'  => 0,
        '100'  => 0,
        '50'   => 0,
        '20'   => 0,
        '10'   => 0,
        '5'    => 0,
        '2'    => 0,
        '1'    => 0,
        '0.50' => 0,
        '0.25' => 0,
        '0.10' => 0,
        '0.05' => 0
    ];

    public function __construct(ICaixaEletronicoDao $caixaDao) {
        $this->caixaDao = $caixaDao;
        $this->cedula = $this->caixaDao->getQtdCadaCedula();
    }

    public function getCedulas() {
        return $this->cedula;
    }

    public function setCedulas($cedula) {
        $this->cedula = $cedula;
    }

    public function getStatus() {
        return [
            'cedula' => $this->cedula,
            'total' => $this->getValorTotal()
        ];
    }

    // Por padrão ao carregar insere 10 unidades de cada cédula e moeda
    public function calcularCarregamento($qtdNotas = 10) {
        $copia = $this->cedula;
        foreach ($copia as $valor => $quantidade) {
            $copia[$valor] += $qtdNotas;
        }
        return $copia;
    }

    public function calcularDescarregamento() {
        // Zera todas as céduas e moedas do caixa
        $copia = $this->cedula;
        foreach ($copia as $valor => $quantidade) {
            $copia[$valor] = 0;
        }
        return $copia;
    }

    public function getCedulasParaSaque($valor) {
        $valorRestante = $valor;
        $cedulasParaSacar = [];
        try{
            $cedulasParaSacar = $this->getComposicaoCedulasSaque($valor, $this->cedula);
            return $cedulasParaSacar;
        } catch (\Exception $e) {
            throw new \Exception("Erro ao obter cédulas para saque: " . $e->getMessage());
        }
    }

    public function calculaRemocaoCedulasSaque($cedulasSaque) {
        //EMANUEL NECESSÁRIO REALIZAR REVISÃO DE REGRA
        $copia = $this->cedula;
        foreach ($cedulasSaque as $cedula => $quantidade) {
            $copia[$cedula] -= $quantidade;
        }

        $resultado = array_merge($cedulasSaque);
        return $resultado;
    }

    public function calculaDepositoCaixa($cedulasDepositadas) {
        $copia = $this->cedula;
        // Processa cédulas
        foreach ($cedulasDepositadas as $cedula => $quantidade) {
            if (array_key_exists($cedula, $this->cedula)) {
                if ($quantidade < 0) {
                    throw new \Exception("Quantidade não pode ser negativa");
                }
                $copia[$cedula] += $quantidade;
            } else {
                //EMANUEL REVISAR REGRA NO CASO DE CENTÁVOS
                throw new \Exception("Cédula de R$ " . number_format($cedula, 2) . " não é aceita");
            }
        }
        return $copia;
    }

    public function getValorTotal() {
        $total = 0;
        foreach ($this->cedula as $cedula => $quantidade) {
            $total += $cedula * $quantidade;
        }
        return $total;
    }

    public function CalculaTotalByCedulas($cedulas) {
        //EMANUEL NECESSÁRIO REALIZAR REVISÃO DE REGRA
        $total = 0;
        foreach ($cedulas as $denominacao => $quantidade) {
            $total += (float)$denominacao * $quantidade;
        }
        return round($total, 2);
    }
}
?>