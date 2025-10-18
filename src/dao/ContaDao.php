<?php
namespace app\dao;

use app\contracts\core\IDatabase;
use app\contracts\dao\IContaDao;

class ContaDao implements IContaDao {
    private $db;

    public function __construct(IDatabase $database) {
        $this->db = $database->getConnection();
    }

    public function getAllContas() {
        try {
            $stmt = $this->db->query("SELECT * FROM conta");
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception("Erro ao resgatar contas no banco: " . $e->getMessage());
        }
    }

    public function getContaById($id) {
        try {
            $stmt = $this->db->prepare("SELECT * FROM conta WHERE conta_id = :conta_id");
            $stmt->execute([':conta_id' => $id]);
            return $stmt->fetch(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            throw new \Exception("Erro ao resgatar conta ID: " . $id . " no banco: " . $e->getMessage());
        }
    }

    public function updateSaldo($id, $novoSaldo) {
        try {
            $stmt = $this->db->prepare("UPDATE conta SET conta_saldo = :conta_saldo WHERE conta_id = :conta_id");
            return $stmt->execute([':conta_saldo' => $novoSaldo, ':conta_id' => $id]);
        } catch (\Exception $e) {
            throw new \Exception("Erro ao atualizar saldo da conta ID: " . $id . " no banco: " . $e->getMessage());
        }
    }

    public function createConta($dadosConta) {
        try {
            $stmt = $this->db->prepare("INSERT INTO conta (conta_nome, conta_saldo) VALUES (:conta_nome, :conta_saldo)");
            return $stmt->execute([
            ':conta_nome' => $dadosConta['conta_nome'],
            ':conta_saldo' => $dadosConta['conta_saldo']
            ]);
        } catch (\Exception $e) {
            throw new \Exception("Erro ao criar nova conta no banco: " . $e->getMessage());
        }
    }
}