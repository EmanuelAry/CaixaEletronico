<?php
namespace app\models;
use app\contracts\models\IContaModel;
use app\contracts\dao\IContaDao;

class ContaModel implements IContaModel {
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

    public function loadDataConta($id, $conta_nome, $saldo) {
        $this->setId($id);
        $this->setContaNome($conta_nome);
        $this->setSaldo($saldo);
    }

    public function SaqueConta($valor) {
        $this->saldo -= $valor;
    }

    public function DepositoConta($valor) {
        $this->saldo += $valor;
    }

    public function isSaquePermitidoConta($valor) {
        if ($valor > $this->saldo) {
            return false;
        }
        return true;
    }
}