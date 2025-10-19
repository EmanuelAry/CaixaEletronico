<?php

namespace app\contracts\controllers;

interface IContaController {
    public function alternarConta($contaId);
    public function listarContas();
    public function criarConta($contaNome, $saldoInicial);
    public function saqueContaAction($valor);
    public function depositoContaAction($valor);
    public function valorTotalContaAction();
    public function selecionarContaAction();
    public function criarContaView();
    public function entrarContaAction();
    public function listarContasView();
}
