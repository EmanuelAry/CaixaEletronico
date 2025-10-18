<?php
namespace app\models;
class ContratoModel {

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

    public function getComposicaoCedulasSaque($valor, $especie) {
        try {
            if ($this->estrategia == self::ESTATEGIA_PADRAO) {
                return $this->composicaoPadrao($valor, $especie);
            } else {
                return $this->composicaoAlternativa($valor, $especie);
            }
        } catch (\Exception $e) {
            throw new \Exception("Erro ao obter composição de cédulas para saque: " . $e->getMessage());
        }
    }

    private function composicaoPadrao($valor, $especie) {
        //EMANUEL NECESSÁRIO REALIZAR REVISÃO DE REGRA 
        krsort($especie);
        $valorRestante = $valor;
        $composicao = [];
        foreach ($especie as $denominacao => $quantidadeDisponivel) {
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

    public function composicaoAlternativa($valor, $especie) {
        //EMANUEL NECESSÁRIO REALIZAR REVISÃO DE REGRA 
        // Implementação de uma estratégia alternativa de composição
        // Por exemplo, tentar usar mais cédulas de menor valor primeiro
        asort($especie);
        $valorRestante = $valor;
        $composicao = [];
        foreach ($especie as $denominacao => $quantidadeDisponivel) {
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
}