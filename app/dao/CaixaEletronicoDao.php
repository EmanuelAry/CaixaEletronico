<?php
namespace app\dao;
use app\core\Database;
use app\core\Logger;
class CaixaEletronicoDao {
    private $db;
    
    public function __construct() {
        $this->db = (new Database())->getConnection();
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

        public function salvaQTDEspecieNoBanco($aEspecie) {
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
                            qtd_especie_2_real, qtd_especie_1_real, qtd_especie_50_cents,
                        qtd_especie_25_cents, qtd_especie_10_cents, qtd_especie_5_cents
                    ) VALUES (
                        :qtd_200, :qtd_100, :qtd_50, :qtd_20, :qtd_10, :qtd_5,
                        :qtd_2, :qtd_1, :qtd_050, :qtd_025, :qtd_010, :qtd_005
                    )";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':qtd_200' => $aEspecie[200],
                ':qtd_100' => $aEspecie[100],
                ':qtd_50'  => $aEspecie[50],
                ':qtd_20'  => $aEspecie[20],
                ':qtd_10'  => $aEspecie[10],
                ':qtd_5'   => $aEspecie[5],
                ':qtd_2'   => $aEspecie[2],
                ':qtd_1'   => $aEspecie[1],
                ':qtd_050' => $aEspecie[0.50],
                ':qtd_025' => $aEspecie[0.25],
                ':qtd_010' => $aEspecie[0.10],
                ':qtd_005' => $aEspecie[0.05]
            ]);

            Logger::log("Quantidade de espécies salva no banco de dados");
            return true;

        } catch (\Exception $e) {
            Logger::log("Erro ao salvar espécies no banco: " . $e->getMessage());
            throw new \Exception("Erro ao salvar no banco de dados: " . $e->getMessage());
        }
    }
}
?>