<?php
namespace app\contracts\models;

interface IContaModel {
    public function setId($id);
    public function setSaldo($saldo);
    public function setContaNome($conta_nome);
    public function getSaldo();
    public function getContaNome();
    public function getId();
    public function loadDataConta($id, $conta_nome, $saldo);
    public function SaqueConta($valor);
    public function DepositoConta($valor);
    public function isSaquePermitidoConta($valor);
}