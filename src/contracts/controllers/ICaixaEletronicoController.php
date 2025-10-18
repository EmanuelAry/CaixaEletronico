<?php

namespace app\contracts\controllers;

interface ICaixaEletronicoController {
    public function carregarCaixaEletronicoAction();
    public function descarregarCaixaEletronicoAction(); 
    public function saqueCaixaEletronicoAction($valor);
    public function depositoCaixaEletronicoAction($cedulas);
    public function valorTotalCaixaEletronicoAction();
}
