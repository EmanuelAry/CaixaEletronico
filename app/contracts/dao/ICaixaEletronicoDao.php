<?php
namespace app\contracts\dao;

interface ICaixaEletronicoDao {
    /**
     * Retorna a quantidade de cada cédula/moeda disponível no caixa
     * @return array Array com as denominações como chaves e quantidades como valores
     */
    public function getQtdCadaCedula();

    /**
     * Salva as quantidades de cédulas no banco de dados
     * @param array $aCedula Array com as denominações como chaves e quantidades como valores
     * @return bool True em caso de sucesso
     * @throws \Exception Em caso de erro
     */
    public function salvaQTDCedulasNoBanco($aCedula);
}