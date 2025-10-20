<?php

namespace app\contracts\controllers;

interface IContaController {
    public function alternarConta($contaId);
    public function alternarContaAction();
    public function listarContas();
    public function criarConta($contaNome, $saldoInicial);
    public function saqueContaAction();
    public function depositoContaAction();
    public function valorTotalContaAction();
    public function selecionarContaAction();
    public function criarContaView();
    public function listarContasView();
}
