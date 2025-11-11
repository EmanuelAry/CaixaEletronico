<?php
namespace app\models;
use app\contracts\models\IContaModel;
use app\contracts\dao\IContaDao;

class ContaModel implements IContaModel {
    private $id;
    private $conta_nome;
    private $conta_email;
    private $saldo;

    public function __construct($id, $conta_nome, $saldo, $conta_email) {
        $this->setId($id);
        $this->setContaNome($conta_nome);
        $this->setSaldo($saldo);
        $this->setContaEmail($conta_email);
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setSaldo($saldo) {
        $this->saldo = (float) round($saldo, 2);
    }

    public function setContaNome($conta_nome) {
        $this->conta_nome = $conta_nome;
    }

    public function setContaEmail($conta_email) {
        $this->conta_email = $conta_email;
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

    public function getContaEmail() {
        return $this->conta_email;
    }

    public function loadDataConta($id, $conta_nome, $saldo, $conta_email) {
        $this->setId($id);
        $this->setContaNome($conta_nome);
        $this->setSaldo($saldo);
        $this->setContaEmail($conta_email);
    }

    public function SaqueConta($valor) {
        $this->saldo -= $valor;
    }

    public function DepositoConta($valor) {
        $this->saldo += (float) round($valor, 2);
    }

    public function isSaquePermitidoConta($valor) {
        if ($valor > $this->saldo) {
            return false;
        }
        return true;
    }
}