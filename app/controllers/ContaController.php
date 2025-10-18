<?php
namespace app\controllers;
use app\models\CaixaEletronicoModel;
use app\dao\CaixaEletronicoDao;
use app\core\Logger;
use app\core\Notification;
class ContaController {
    private $id;
    private $conta_nome;
    private $saldo;

    public function __construct($id, $conta_nome, $saldo) {
        $this->setId($id);
        $this->setContaNome($conta_nome);
        $this->setSaldo($saldo);
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setSaldo($saldo) {
        $this->saldo = $saldo;
    }

    public function setContaNome($conta_nome) {
        $this->conta_nome = $conta_nome;
    }

    public function getSaldo() {
        return $this->saldo;
    }

    public function getContaNome() {
        return $this->conta_nome;
    }

    public function getId() {
        return $this->id;
    }

    public function Saque($valor) {
        // Lógica de saque
    }

    public function Deposito($valor) {
        // Lógica de depósito
    }


    public function isSaquePermitido($valor) {
        if ($valor > $this->saldo) {
            return false;
        }
        return true;
    }
}