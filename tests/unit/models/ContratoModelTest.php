<?php
namespace tests\unit\models;

use PHPUnit\Framework\TestCase;
use app\models\ContratoModel;

// Mock concreto para testar a classe abstrata
class ContratoModelConcreto extends ContratoModel{
    private $cedulas = [];
    private $status = true;

    public function getCedulas()
    {
        return $this->cedulas;
    }

    public function setCedulas($cedula)
    {
        $this->cedulas = $cedula;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function calcularCarregamento($qtdNotas = 10)
    {
        $this->cedulas = array_fill_keys([200, 100, 50, 20, 10, 5], $qtdNotas);
        return $this->cedulas;
    }

    public function calcularDescarregamento()
    {
        return array_sum($this->cedulas);
    }

    public function getCedulasParaSaque($valor)
    {
        return $this->getComposicaoCedulasSaque($valor, $this->cedulas);
    }

    public function calculaRemocaoCedulasSaque($cedulasSaque)
    {
        foreach ($cedulasSaque as $cedula => $quantidade) {
            $this->cedulas[$cedula] -= $quantidade;
        }
        return $this->cedulas;
    }

    public function calculaDepositoCaixa($cedulasDepositadas)
    {
        foreach ($cedulasDepositadas as $cedula => $quantidade) {
            $this->cedulas[$cedula] += $quantidade;
        }
        return $this->cedulas;
    }

    public function getValorTotal()
    {
        $total = 0;
        foreach ($this->cedulas as $cedula => $quantidade) {
            $total += $cedula * $quantidade;
        }
        return $total;
    }

    public function CalculaTotalByCedulas($cedulas)
    {
        return array_sum(array_map(function($denominacao, $quantidade) {
            return $denominacao * $quantidade;
        }, array_keys($cedulas), $cedulas));
    }
}

class ContratoModelTest extends TestCase
{
    private $model;

    protected function setUp(): void
    {
        $this->model = new ContratoModelConcreto();
    }

    public function testEstrategiaPadraoInicial()
    {
        $this->assertEquals(ContratoModel::ESTATEGIA_PADRAO, $this->model->getEstrategia());
    }

    public function testSetEstrategia()
    {
        $this->model->setEstrategia(ContratoModel::ESTATEGIA_ALTERNATIVA);
        $this->assertEquals(ContratoModel::ESTATEGIA_ALTERNATIVA, $this->model->getEstrategia());
    }

    public function testComposicaoPadraoComCedulasExatas()
    {
        $cedulas = [100 => 2, 50 => 1];
        $resultado = $this->invokePrivateMethod($this->model, 'composicaoPadrao', [150, $cedulas]);
        
        $this->assertEquals([100 => 1, 50 => 1], $resultado);
    }

    public function testComposicaoPadraoComValorImpossivel()
    {
        $this->expectException(\Exception::class);
        $cedulas = [100 => 1];
        $this->invokePrivateMethod($this->model, 'composicaoPadrao', [150, $cedulas]);
    }

    public function testComposicaoAlternativaSemCedulasAltas()
    {
        $cedulas = [200 => 2, 50 => 3, 20 => 10];
        $resultado = $this->invokePrivateMethod($this->model, 'composicaoAlternativa', [90, $cedulas]);
        
        $this->assertEquals([50 => 1, 20 => 2], $resultado);
    }

    public function testComposicaoAlternativaRetornaPadraoQuandoNecessario()
    {
        $cedulas = [200 => 2, 100 => 1];
        $resultado = $this->invokePrivateMethod($this->model, 'composicaoAlternativa', [300, $cedulas]);
        
        $this->assertEquals([200 => 1, 100 => 1], $resultado);
    }

    public function testGetComposicaoCedulasSaqueComEstrategiaPadrao()
    {
        $this->model->setCedulas([100 => 2, 50 => 1]);
        $resultado = $this->model->getCedulasParaSaque(150);
        
        $this->assertEquals([100 => 1, 50 => 1], $resultado);
    }

    public function testGetComposicaoCedulasSaqueComEstrategiaAlternativa()
    {
        $this->model->setEstrategia(ContratoModel::ESTATEGIA_ALTERNATIVA);
        $this->model->setCedulas([200 => 2, 50 => 3, 20 => 10]);
        $resultado = $this->model->getCedulasParaSaque(90);
        
        $this->assertEquals([50 => 1, 20 => 2], $resultado);
    }

    public function testEncontrarProximoValorPossivelPadrao()
    {
        $cedulas = [100 => 1, 50 => 1];
        $resultado = $this->invokePrivateMethod($this->model, 'encontrarProximoValorPossivelPadrao', [160, $cedulas]);
        
        $this->assertEquals(150, $resultado);
    }

    // Método auxiliar para acessar métodos privados
    private function invokePrivateMethod($object, $methodName, array $parameters = [])
    {
        $reflection = new \ReflectionClass(get_class($object));
        $method = $reflection->getMethod($methodName);
        $method->setAccessible(true);
        return $method->invokeArgs($object, $parameters);
    }

    // Testes para os métodos da implementação concreta
    public function testCalcularCarregamento()
    {
        $resultado = $this->model->calcularCarregamento(5);
        $esperado = [200 => 5, 100 => 5, 50 => 5, 20 => 5, 10 => 5, 5 => 5];
        $this->assertEquals($esperado, $resultado);
    }

    public function testCalculaRemocaoCedulasSaque()
    {
        $this->model->setCedulas([100 => 5, 50 => 5]);
        $resultado = $this->model->calculaRemocaoCedulasSaque([100 => 2, 50 => 1]);
        $this->assertEquals([100 => 3, 50 => 4], $resultado);
    }

    public function testGetValorTotal()
    {
        $this->model->setCedulas([100 => 2, 50 => 3]);
        $this->assertEquals(350, $this->model->getValorTotal());
    }

    public function testCalculaTotalByCedulas()
    {
        $cedulas = [100 => 2, 50 => 1];
        $this->assertEquals(250, $this->model->CalculaTotalByCedulas($cedulas));
    }
}