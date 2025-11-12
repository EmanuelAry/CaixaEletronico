<?php
namespace app\contracts\dao;

interface IContaDao {
    public function getAllContas();
    public function getContaById($id);
    public function getContaByEmail($email);
    public function updateSaldo($id, $novoSaldo);
    public function createConta($dadosConta);
}