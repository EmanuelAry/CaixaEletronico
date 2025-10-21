<?php

namespace app\contracts\controllers;

interface ICaixaEletronicoController {
    public function getCaixaEletronicoModel();
    public function carregarCaixaEletronicoAction();
    public function descarregarCaixaEletronicoAction(); 
    public function saqueCaixaEletronicoAction($valor, $regra);
    public function depositoCaixaEletronicoAction($cedulas);
    public function valorTotalCaixaEletronicoAction();
}
