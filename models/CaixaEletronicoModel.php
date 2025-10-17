<?php
namespace models;
use core\Logger;
use core\Database;
class CaixaEletronicoModel {


    private $db;
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
        $this->db = (new Database())->getConnection();
        $this->especie = $this->getQtdCadaEspecie();
    }

    public function getQtdCadaEspecie() {
        // Retorna a quantidade de cada espécie disponível no caixa
        try {
            $stmt = $this->db->query("SELECT * FROM qtd_especie_caixa ORDER BY qtd_especie_caixa_id DESC LIMIT 1");
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $qtd_by_especie = [
                    200 => $resultado['qtd_especie_200_real'] ?? 0,
                    100 => $resultado['qtd_especie_100_real'] ?? 0,
                    50 => $resultado['qtd_especie_50_real'] ?? 0,
                    20 => $resultado['qtd_especie_20_real'] ?? 0,
                    10 => $resultado['qtd_especie_10_real'] ?? 0,
                    5 => $resultado['qtd_especie_5_real'] ?? 0,
                    2 => $resultado['qtd_especie_2_real'] ?? 0,
                    1 => $resultado['qtd_especie_1_real'] ?? 0,
                    0.50 => $resultado['qtd_especie_50_cents'] ?? 0,
                    0.25 => $resultado['qtd_especie_25_cents'] ?? 0,
                    0.10 => $resultado['qtd_especie_10_cents'] ?? 0,
                    0.05 => $resultado['qtd_especie_5_cents'] ?? 0
                ];
                return $qtd_by_especie;
            }
        } catch (\Exception $e) {
            Logger::log("Erro ao carregar dados do banco: " . $e->getMessage());
        }
    }

    public function salvarEspecieNoBanco() {
        try {
            // Verifica se já existe um registro
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM qtd_especie_caixa");
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                // Atualiza registro existente
                $sql = "UPDATE qtd_especie_caixa SET 
                        qtd_especie_200_real = :qtd_200,
                        qtd_especie_100_real = :qtd_100,
                        qtd_especie_50_real = :qtd_50,
                        qtd_especie_20_real = :qtd_20,
                        qtd_especie_10_real = :qtd_10,
                        qtd_especie_5_real = :qtd_5,
                        qtd_especie_2_real = :qtd_2,
                        qtd_especie_1_real = :qtd_1,
                        qtd_especie_50_cents = :qtd_050,
                        qtd_especie_25_cents = :qtd_025,
                        qtd_especie_10_cents = :qtd_010,
                        qtd_especie_5_cents = :qtd_005";
            } else {
                // Insere novo registro
                $sql = "INSERT INTO qtd_especie_caixa (
                        qtd_especie_200_real, qtd_especie_100_real, qtd_especie_50_real,
                        qtd_especie_20_real, qtd_especie_10_real, qtd_especie_5_real,
                        qtd_especie_2_real, qtd_especie_1_real, qtd_especie_50_sents,
                        qtd_especie_25_sents, qtd_especie_10_sents, qtd_especie_5_sents
                    ) VALUES (
                        :qtd_200, :qtd_100, :qtd_50, :qtd_20, :qtd_10, :qtd_5,
                        :qtd_2, :qtd_1, :qtd_050, :qtd_025, :qtd_010, :qtd_005
                    )";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':qtd_200' => $this->especie[200],
                ':qtd_100' => $this->especie[100],
                ':qtd_50' => $this->especie[50],
                ':qtd_20' => $this->especie[20],
                ':qtd_10' => $this->especie[10],
                ':qtd_5' => $this->especie[5],
                ':qtd_2' => $this->especie[2],
                ':qtd_1' => $this->especie[1],
                ':qtd_050' => $this->especie[0.50],
                ':qtd_025' => $this->especie[0.25],
                ':qtd_010' => $this->especie[0.10],
                ':qtd_005' => $this->especie[0.05]
            ]);

            Logger::log("Quantidade de espécies salva no banco de dados");
            return true;

        } catch (\Exception $e) {
            Logger::log("Erro ao salvar espécies no banco: " . $e->getMessage());
            throw new \Exception("Erro ao salvar no banco de dados: " . $e->getMessage());
        }
    }

    public function carregar() {
        // Adiciona 10 unidades de cada cédula e moeda
        foreach ($this->especie as $valor => $quantidade) {
            $this->especie[$valor] += 10;
        }

        $this->salvarEspecieNoBanco();    

        Logger::log("Caixa eletrônico carregado com 10 unidades de cada cédula e moeda");
        return true;
    }

    public function descarregar() {
        foreach ($this->especie as $valor => $quantidade) {
            $this->especie[$valor] = 0;
        }
        
        $this->salvarEspecieNoBanco(); 

        Logger::log("Caixa eletrônico descarregado completamente");
        return true;
    }

    public function sacar($valor) {
        if ($valor <= 0) {
            throw new \Exception("Valor de saque deve ser positivo");
        }

        if ($valor > $this->getValorTotal()) {
            throw new \Exception("Saldo insuficiente no caixa para realizar o saque");
        }

        $valorRestante = $valor;
        $especieParaSacar = [];

        // Processa cédulas em ordem decrescente
        krsort($this->especie);
        foreach ($this->especie as $cedula => $quantidade) {
            if ($valorRestante >= $cedula && $quantidade > 0) {
                $numespecie = min(floor($valorRestante / $cedula), $quantidade);
                if ($numespecie > 0) {
                    $especieParaSacar[$cedula] = $numespecie;
                    $valorRestante -= $numespecie * $cedula;
                }
            }
        }
        // Verifica se conseguiu compor o valor exato
        if ($valorRestante > 0.01) { // Considera diferença de ponto flutuante
            throw new \Exception("Não é possível compor o valor R$ " . number_format($valor, 2) . " com as cédulas disponíveis");
        }

        // Remove as cédulas do caixa
        foreach ($especieParaSacar as $cedula => $quantidade) {
            $this->especie[$cedula] -= $quantidade;
        }

        $resultado = array_merge($especieParaSacar);
        
        Logger::log("Saque de R$ " . number_format($valor, 2) . " realizado. Composição: " . json_encode($resultado));
        return $resultado;
    }

    public function depositar($especieDepositadas) {
        $totalDepositado = 0;

        // Processa cédulas
        foreach ($especieDepositadas as $cedula => $quantidade) {
            if (array_key_exists($cedula, $this->especie)) {
                if ($quantidade < 0) {
                    throw new \Exception("Quantidade não pode ser negativa");
                }
                $this->especie[$cedula] += $quantidade;
                $totalDepositado += $cedula * $quantidade;
            } else {
                throw new \Exception("Cédula de R$ " . number_format($cedula, 2) . " não é aceita");
            }
        }

        Logger::log("Depósito de R$ " . number_format($totalDepositado, 2) . " realizado");
        return $totalDepositado;
    }

    public function getValorTotal() {
        $total = 0;

        foreach ($this->especie as $cedula => $quantidade) {
            $total += $cedula * $quantidade;
        }

        return $total;
    }

    public function getespecie() {
        return $this->especie;
    }

    public function getStatus() {
        return [
            'especie' => $this->especie,
            'total' => $this->getValorTotal()
        ];
    }
}
?>