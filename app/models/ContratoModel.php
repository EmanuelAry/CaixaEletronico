<?php
namespace app\models;
use app\contracts\models\ICaixaEletronicoModel;

abstract class ContratoModel implements ICaixaEletronicoModel {
    const ESTATEGIA_PADRAO = 0;
    const ESTATEGIA_ALTERNATIVA = 1;
    private $estrategia;

    public function __construct() {
        $this->estrategia = self::ESTATEGIA_PADRAO;
    }

    public function setEstrategia($estrategia) {
        $this->estrategia = $estrategia;
    }
    public function getEstrategia() {
        return $this->estrategia;
    }

    public function getComposicaoCedulasSaque($valor, $cedula) {
        try {
            if ($this->estrategia == self::ESTATEGIA_PADRAO) {
                return $this->composicaoPadrao($valor, $cedula);
            } else {
                return $this->composicaoAlternativa($valor, $cedula);
            }
        } catch (\Exception $e) {
            throw new \Exception("Erro ao obter composição de cédulas para saque: " . $e->getMessage());
        }
    }

    private function composicaoPadrao($valor, $cedula) {
        //EMANUEL NECESSÁRIO REALIZAR REVISÃO DE REGRA 
        krsort($cedula);
        $valorRestante = $valor;
        $composicao = [];
        foreach ($cedula as $denominacao => $quantidadeDisponivel) {
            if ($valorRestante <= 0) {
                break;
            }
            $quantidadeNecessaria = floor($valorRestante / $denominacao);
            $quantidadeASacar = min($quantidadeNecessaria, $quantidadeDisponivel);
            if ($quantidadeASacar > 0) {
                $composicao[$denominacao] = $quantidadeASacar;
                $valorRestante -= $quantidadeASacar * $denominacao;
            }
        }
        if ($valorRestante > 0) {
            throw new \Exception("Não foi possível compor o valor solicitado");
        }
        return $composicao;
    }

    public function composicaoAlternativa($valor, $cedula) {
        //EMANUEL NECESSÁRIO REALIZAR REVISÃO DE REGRA 
        // Implementação de uma estratégia alternativa de composição
        // Por exemplo, tentar usar mais cédulas de menor valor primeiro
        asort($cedula);
        $valorRestante = $valor;
        $composicao = [];
        foreach ($cedula as $denominacao => $quantidadeDisponivel) {
            if ($valorRestante <= 0) {
                break;
            }
            $quantidadeNecessaria = floor($valorRestante / $denominacao);
            $quantidadeASacar = min($quantidadeNecessaria, $quantidadeDisponivel);
            if ($quantidadeASacar > 0) {
                $composicao[$denominacao] = $quantidadeASacar;
                $valorRestante -= $quantidadeASacar * $denominacao;
            }
        }
        if ($valorRestante > 0) {
             throw new \Exception("Não foi possível compor o valor solicitado"); // Não foi possível compor o valor solicitado
        }
        return $composicao;
    }

    // Métodos abstratos que devem ser implementados pelas classes filhas
    abstract public function getCedulas();
    abstract public function setCedulas($cedula);
    abstract public function getStatus();
    abstract public function calcularCarregamento($qtdNotas = 10);
    abstract public function calcularDescarregamento();
    abstract public function getCedulasParaSaque($valor);
    abstract public function calculaRemocaoCedulasSaque($cedulasSaque);
    abstract public function calculaDepositoCaixa($cedulasDepositadas);
    abstract public function getValorTotal();
    abstract public function CalculaTotalByCedulas($cedulas);
}