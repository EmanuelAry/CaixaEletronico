<?php
namespace app\contracts\models;

interface ICaixaEletronicoModel {
    // Métodos herdados de ContratoModel
    public function setEstrategia($estrategia);
    public function getEstrategia();
    public function getComposicaoCedulasSaque($valor, $cedula);

    // Métodos específicos do CaixaEletronicoModel
    public function getCedulas();
    public function setCedulas($cedula);
    public function getStatus();
    public function calcularCarregamento($qtdNotas = 10);
    public function calcularDescarregamento();
    public function getCedulasParaSaque($valor);
    public function calculaRemocaoCedulasSaque($cedulasSaque);
    public function calculaDepositoCaixa($cedulasDepositadas);
    public function getValorTotal();
    public function CalculaTotalByCedulas($cedulas);
}