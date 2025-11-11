<?php

namespace app\contracts\services;

interface IContaService {
    public function loginConta($contaId, $contaSenha);
    public function alternarConta($contaId);
    public function criarConta($contaNome, $contaEmail, $contaSenha, $saldoInicial);
    public function getInfoCaixaMenuByConta($contaId);
    public function listarContas();
    public function valorTotalConta();
    public function depositoConta($contaId, $cedulas);
    public function saqueConta($conta_id, $valor, $regraSaque);
}
