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
        $this->estrategia = (int) $estrategia;
    }
    public function getEstrategia() {
        return $this->estrategia;
    }

    public function getComposicaoCedulasSaque($valor, $cedula) {
        if ($this->estrategia == self::ESTATEGIA_PADRAO) {
            return $this->composicaoPadrao($valor, $cedula);
        } else {
            return $this->composicaoAlternativa($valor, $cedula);
        }
    }

    private function composicaoPadrao($valor, $cedula) {
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
            throw new \Exception("Não foi possível compor o valor solicitado você pode sacar: R$" . $this->encontrarProximoValorPossivelPadrao($valor, $cedula));
        }
        return $composicao;
    }

    
    private function encontrarProximoValorPossivelPadrao($valor, $cedula) {
        krsort($cedula);
        $valorMaximo = 0;
        
        // Calcula o valor máximo possível com as cédulas disponíveis
        foreach ($cedula as $denominacao => $quantidadeDisponivel) {
            $valorMaximo += $denominacao * $quantidadeDisponivel;
        }
        
        
        // Procura o próximo valor possível a partir do valor original + 0.5
        for ($proximoValor = $valor + 0.5; $proximoValor <= $valorMaximo; $proximoValor++) {
            $valorTeste = $proximoValor;
            $cedulasTemp = $cedula;
            $possivel = true;
            
            foreach ($cedulasTemp as $denominacao => $quantidadeDisponivel) {
                if ($valorTeste <= 0) {
                    break;
                }
                $quantidadeNecessaria = floor($valorTeste / $denominacao);
                $quantidadeASacar = min($quantidadeNecessaria, $quantidadeDisponivel);
                if ($quantidadeASacar > 0) {
                    $valorTeste -= $quantidadeASacar * $denominacao;
                }
            }
            
            if ($valorTeste == 0) {
                return $proximoValor;
            }
        }
        
        return $valorMaximo;
    }

    private function composicaoAlternativa($valor, $cedula) {
        krsort($cedula);
        $cedulaReservada = $cedula;
        //FAZ A COMPOSIÇÃO SEM USAR AS CÉDULAS DE 100 E 200
        unset($cedula[200]);
        unset($cedula[100]);
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
            //se não for possível fazer a composição sem as cedulas de 100 e 200, utiliza a composição padrão;
            return $this->composicaoPadrao($valor, $cedulaReservada);
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