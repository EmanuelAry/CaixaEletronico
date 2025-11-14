<?php
namespace tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\models\CaixaEletronicoModel;
use app\contracts\dao\ICaixaEletronicoDao;

class CaixaEletronicoModelTest extends TestCase{
    private $caixaEletronicoDaoMock;
    private $model;

    protected function setUp(): void
    {
        $this->caixaEletronicoDaoMock = $this->createMock(ICaixaEletronicoDao::class);
        $this->model = new CaixaEletronicoModel($this->caixaEletronicoDaoMock);
    }

    public function testValidarComposicaoCedulasRegraAlternativa() {
        $cedulas = [
            '200'  => 100, 
            '100'  => 100, 
            '50'   => 100, 
            '20'   => 100, 
            '10'   => 100, 
            '5'    => 100, 
            '2'    => 100, 
            '1'    => 100, 
            '0.50' => 100, 
            '0.25' => 100, 
            '0.10' => 100, 
            '0.05' => 100 
        ];
        $valor = 100.00;
        $regra = 1; // Estratégia Alternativa;
        $this->model->setEstrategia($regra);
        $resultado = $this->model->getComposicaoCedulasSaque($valor, $cedulas);

        $this->assertEquals([50 => 2], $resultado);
    }

    public function testValidarComposicaoCedulasRegraPadrao() {
        $cedulas = [
            '200'  => 100, 
            '100'  => 100, 
            '50'   => 100, 
            '20'   => 100, 
            '10'   => 100, 
            '5'    => 100, 
            '2'    => 100, 
            '1'    => 100, 
            '0.50' => 100, 
            '0.25' => 100, 
            '0.10' => 100, 
            '0.05' => 100 
        ];
        $valor = 100.00;
        $regra = 0; // Estratégia Padrão;
        $this->model->setEstrategia($regra);
        $resultado = $this->model->getComposicaoCedulasSaque($valor, $cedulas);

        $this->assertEquals([100 => 1], $resultado);
    }

    public function testValidarComposicaoDeCedulaRegraAlternativaQuantidadesNotasInsuficientes () {
        $cedulas = [
            '200'  => 0, 
            '100'  => 0, 
            '50'   => 0, 
            '20'   => 0, 
            '10'   => 0, 
            '5'    => 0, 
            '2'    => 0, 
            '1'    => 0, 
            '0.50' => 0, 
            '0.25' => 0, 
            '0.10' => 0, 
            '0.05' => 0 
        ];
        $valor = 100.00;
        $regra = 0; // Estratégia Alternativa;
        $this->model->setEstrategia($regra);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Não foi possível compor o valor solicitado você pode sacar: R$0");
        $resultado = $this->model->getComposicaoCedulasSaque($valor, $cedulas);
    }

    public function testValidarComposicaoDeCedulaRegraPadraoQuantidadesNotasInsuficientes () {
        $cedulas = [
            '200'  => 0, 
            '100'  => 0, 
            '50'   => 0, 
            '20'   => 0, 
            '10'   => 0, 
            '5'    => 0, 
            '2'    => 0, 
            '1'    => 0, 
            '0.50' => 0, 
            '0.25' => 0, 
            '0.10' => 0, 
            '0.05' => 0 
        ];
        $valor = 100.00;
        $regra = 1; // Estratégia Padrão;
        $this->model->setEstrategia($regra);

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage("Não foi possível compor o valor solicitado você pode sacar: R$0");
        $resultado = $this->model->getComposicaoCedulasSaque($valor, $cedulas);
    }

    public function testValidarCalculoValorComBaseNasCedulasInseridas(){
        $cedulas = [
            '200'  => 1, 
            '100'  => 1, 
            '50'   => 1, 
            '20'   => 1, 
            '10'   => 1, 
            '5'    => 1, 
            '2'    => 1, 
            '1'    => 1, 
            '0.50' => 1, 
            '0.25' => 1, 
            '0.10' => 1, 
            '0.05' => 1 
        ];

        $fReturn = $this->model->CalculaTotalByCedulas($cedulas);

        $this->assertEquals(388.90, $fReturn);
    } 

}