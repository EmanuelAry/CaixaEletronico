<?php
namespace app\contracts\dao;

interface ICaixaEletronicoDao {
    public function getQtdCadaCedula();
    public function salvaQTDCedulasNoBanco($aCedula);
}