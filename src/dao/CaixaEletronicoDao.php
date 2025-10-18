<?php
namespace app\dao;

use app\contracts\core\IDatabase;
use app\contracts\dao\ICaixaEletronicoDao;

class CaixaEletronicoDao implements ICaixaEletronicoDao {
    private $db;
    
    public function __construct(IDatabase $database) {
        $this->db = $database->getConnection();
    }

    public function getQtdCadaCedula() {
        // Retorna a quantidade de cada cédula/moeda disponível no caixa
        try {
            $stmt = $this->db->query("SELECT * FROM qtd_cedula_caixa ORDER BY qtd_cedula_caixa_id DESC LIMIT 1");
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($resultado) {
                $qtd_by_cedula = [
                    200 => $resultado['qtd_cedula_200_real'] ?? 0,
                    100 => $resultado['qtd_cedula_100_real'] ?? 0,
                    50 => $resultado['qtd_cedula_50_real'] ?? 0,
                    20 => $resultado['qtd_cedula_20_real'] ?? 0,
                    10 => $resultado['qtd_cedula_10_real'] ?? 0,
                    5 => $resultado['qtd_cedula_5_real'] ?? 0,
                    2 => $resultado['qtd_cedula_2_real'] ?? 0,
                    1 => $resultado['qtd_cedula_1_real'] ?? 0,
                    0.50 => $resultado['qtd_cedula_50_cents'] ?? 0,
                    0.25 => $resultado['qtd_cedula_25_cents'] ?? 0,
                    0.10 => $resultado['qtd_cedula_10_cents'] ?? 0,
                    0.05 => $resultado['qtd_cedula_5_cents'] ?? 0
                ];
                return $qtd_by_cedula;
            }
            
            // Retorna array vazio se não encontrar registros
            return [
                200 => 0, 100 => 0, 50 => 0, 20 => 0, 10 => 0, 5 => 0, 
                2 => 0, 1 => 0, 0.50 => 0, 0.25 => 0, 0.10 => 0, 0.05 => 0
            ];
            
        } catch (\Exception $e) {
            throw new \Exception("Erro ao resgatar cedulas no banco: " . $e->getMessage());
        }
    }

    public function salvaQTDCedulasNoBanco($aCedula) {
        try {
            // Verifica se já existe um registro
            $stmt = $this->db->query("SELECT COUNT(*) as total FROM qtd_cedula_caixa");
            $resultado = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            if ($resultado['total'] > 0) {
                // Atualiza registro existente
                $sql = "UPDATE qtd_cedula_caixa SET 
                        qtd_cedula_200_real = :qtd_200,
                        qtd_cedula_100_real = :qtd_100,
                        qtd_cedula_50_real = :qtd_50,
                        qtd_cedula_20_real = :qtd_20,
                        qtd_cedula_10_real = :qtd_10,
                        qtd_cedula_5_real = :qtd_5,
                        qtd_cedula_2_real = :qtd_2,
                        qtd_cedula_1_real = :qtd_1,
                        qtd_cedula_50_cents = :qtd_050,
                        qtd_cedula_25_cents = :qtd_025,
                        qtd_cedula_10_cents = :qtd_010,
                        qtd_cedula_5_cents = :qtd_005";
            } else {
                // Insere novo registro
                $sql = "INSERT INTO qtd_cedula_caixa (
                        qtd_cedula_200_real, qtd_cedula_100_real, qtd_cedula_50_real,
                        qtd_cedula_20_real, qtd_cedula_10_real, qtd_cedula_5_real,
                        qtd_cedula_2_real, qtd_cedula_1_real, qtd_cedula_50_cents,
                    qtd_cedula_25_cents, qtd_cedula_10_cents, qtd_cedula_5_cents
                ) VALUES (
                    :qtd_200, :qtd_100, :qtd_50, :qtd_20, :qtd_10, :qtd_5,
                    :qtd_2, :qtd_1, :qtd_050, :qtd_025, :qtd_010, :qtd_005
                )";
            }

            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':qtd_200' => $aCedula[200],
                ':qtd_100' => $aCedula[100],
                ':qtd_50'  => $aCedula[50],
                ':qtd_20'  => $aCedula[20],
                ':qtd_10'  => $aCedula[10],
                ':qtd_5'   => $aCedula[5],
                ':qtd_2'   => $aCedula[2],
                ':qtd_1'   => $aCedula[1],
                ':qtd_050' => $aCedula[0.50],
                ':qtd_025' => $aCedula[0.25],
                ':qtd_010' => $aCedula[0.10],
                ':qtd_005' => $aCedula[0.05]
            ]);
            return true;

        } catch (\Exception $e) {
            throw new \Exception("Erro ao salvar cedulas no banco de dados: " . $e->getMessage());
        }
    }
}