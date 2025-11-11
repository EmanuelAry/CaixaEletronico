<?php

namespace app\contracts\services;

interface ICaixaEletronicoService {
    public function getCaixaEletronicoModel();
    public function carregarCaixaEletronico();
    public function descarregarCaixaEletronico();
    public function saqueCaixaEletronico($valorSaque, $regra);
    public function depositoCaixaEletronico($cedulas);
    public function valorTotalCaixaEletronico();
    public function getQtdTotalCedulas();
    public function getQtdTotalMoedas();
    public function estoqueCaixaView();
}