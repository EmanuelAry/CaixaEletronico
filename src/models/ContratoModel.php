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
        ksort($cedula); 
        $valorRestante = $valor;
        $composicao = [];
        
        // Primeiro, tenta compor o valor usando apenas cédulas menores
        foreach ($cedula as $denominacao => &$quantidadeDisponivel) {
            if ($valorRestante <= 0) {
                break;
            }
            
            // Se não for a última cédula (maior), tenta usar o máximo possível
            if ($denominacao < max(array_keys($cedula))) {
                $quantidadeNecessaria = floor($valorRestante / $denominacao);
                $quantidadeASacar = min($quantidadeNecessaria, $quantidadeDisponivel);
                
                if ($quantidadeASacar > 0) {
                    $composicao[$denominacao] = $quantidadeASacar;
                    $quantidadeDisponivel -= $quantidadeASacar;
                    $valorRestante -= $quantidadeASacar * $denominacao;
                }
            }
        }
        
        // Se ainda sobrou valor, usa as cédulas maiores apenas quando necessário
        if ($valorRestante > 0) {
            krsort($cedula); // Ordena em ordem decrescente para pegar as maiores
            
            foreach ($cedula as $denominacao => &$quantidadeDisponivel) {
                if ($valorRestante <= 0) {
                    break;
                }
                
                // Calcula a quantidade mínima necessária da cédula maior
                $quantidadeMinima = ceil($valorRestante / $denominacao);
                $quantidadeASacar = min($quantidadeMinima, $quantidadeDisponivel);
                
                // Se já temos alguma cédula desta denominação na composição, ajusta
                $quantidadeExistente = $composicao[$denominacao] ?? 0;
                $quantidadeTotal = $quantidadeExistente + $quantidadeASacar;
                
                // Verifica se podemos evitar usar cédulas grandes desnecessariamente
                if ($quantidadeASacar > 0 && $valorRestante >= $denominacao) {
                    $composicao[$denominacao] = $quantidadeTotal;
                    $quantidadeDisponivel -= $quantidadeASacar;
                    $valorRestante -= $quantidadeASacar * $denominacao;
                }
            }
        }
        
        if ($valorRestante > 0) {
            // Encontra o próximo valor possível com a mesma estratégia
            $proximoValor = $this->encontrarProximoValorPossivelPadrao($valor, $cedula);
            if ($proximoValor > $valor) {
                throw new \Exception(
                    "Não foi possível compor o valor solicitado. " .
                    " você pode sacar: R$ " . number_format($proximoValor, 2, ',', '.') . "?"
                );
            } else {
                throw new \Exception("Não foi possível compor o valor solicitado com as cédulas disponívei na regra alternativa.");
            }
        }
        
        // Remove entradas com quantidade zero e retorna
        $composicao = array_filter($composicao, function($quantidade) {
            return $quantidade > 0;
        });
        
        krsort($composicao); // Ordena a composição final por denominação decrescente
        
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