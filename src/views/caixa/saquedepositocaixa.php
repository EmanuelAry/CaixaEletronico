<?php 
use app\helpers\UrlHelper;
include __DIR__ . '/../templates/header.php';
?>

<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-4">
    <h1 class="page-title">Operações do Caixa Eletrônico</h1>
</div>

<?php include __DIR__ . '/../templates/notifications.php'; ?>

<div class="operations-grid">
    <!-- Card de Saque -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Saque</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= UrlHelper::baseUrl('conta/saque/?XDEBUG_SESSION=VSCODE') ?>" class="operation-form" id="saqueForm">
                <input hidden name="conta_id" type="number" value="<?= $contaSelecionada['conta_id'] ?>"></input>
                <div class="form-group">
                    <label for="valor_saque" class="form-label">Valor do Saque (R$)</label>
                    <div class="input-group">
                        <span class="input-prefix">R$</span>
                        <input 
                            type="number" 
                            id="valor_saque" 
                            name="valor_saque" 
                            class="form-input" 
                            step="0.01" 
                            min="0.01" 
                            placeholder="0,00" 
                            required
                        >
                    </div>
                    <small class="form-hint">Valor mínimo: R$ 0,05</small>
                </div>

                <!-- Switcher de Regras -->
                <div class="form-group">
                    <label class="form-label">Regra de Saque</label>
                    <div class="regra-switcher">
                        <div class="switcher-container">
                            <input type="radio" id="regra_padrao" name="regra_saque" value="padrao" class="switcher-input" checked>
                            <label for="regra_padrao" class="switcher-option">
                                <span class="switcher-text">Regra Padrão</span>
                            </label>
                            
                            <input type="radio" id="regra_alternativa" name="regra_saque" value="alternativa" class="switcher-input">
                            <label for="regra_alternativa" class="switcher-option">
                                <span class="switcher-text">Regra Alternativa</span>
                            </label>
                            
                            <div class="switcher-slider"></div>
                        </div>
                    </div>
                    
                    <!-- Descrições das Regras -->
                    <div class="regra-descricoes">
                        <div class="regra-descricao" id="descricao_padrao">
                            <h5 class="descricao-title">Regra Padrão</h5>
                            <p class="descricao-text">
                                Estratégia Padrão (Menor Quantidade): Busca a menor quantidade total de cédulas para entregar o valor solicitado.
                            </p>
                        </div>
                        
                        <div class="regra-descricao" id="descricao_alternativa" style="display: none;">
                            <h5 class="descricao-title">Regra Alternativa</h5>
                            <p class="descricao-text">
                                Estratégia Alternativa: Prioriza a preservação das cédulas de maior valor (ex: R$100, R$200), utilizando as cédulas de menor valor o máximo possível para evitar que o caixa fique "preso" com troco pequeno.
                            </p>
                        </div>
                    </div>
                </div>
                
                <div class="suggested-values mb-3">
                    <h4 class="subtitle mb-2">Valores Sugeridos:</h4>
                    <div class="suggested-buttons">
                        <button type="button" class="btn btn-outline btn-sm suggested-value" data-value="20">R$ 20</button>
                        <button type="button" class="btn btn-outline btn-sm suggested-value" data-value="50">R$ 50</button>
                        <button type="button" class="btn btn-outline btn-sm suggested-value" data-value="100">R$ 100</button>
                        <button type="button" class="btn btn-outline btn-sm suggested-value" data-value="200">R$ 200</button>
                    </div>
                </div>
                
                <!-- Informações Adicionais -->
                <div class="saque-info">
                    <div class="info-item">
                        <span>O saque será processado conforme a regra selecionada</span>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-danger btn-block">
                    Realizar Saque
                </button>
            </form>
        </div>
    </div>

    <!-- Card de Depósito -->
    <div class="card">
        <div class="card-header">
            <h2 class="card-title">Depósito</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="<?= UrlHelper::baseUrl('conta/deposito/?XDEBUG_SESSION=VSCODE') ?>" class="operation-form" id="depositoForm">
                <div class="form-group">
                    <input hidden name="conta_id" type="number" value="<?= $contaSelecionada['conta_id'] ?>"></input>
                    <input type="hidden" name="cedulas" id="cedulasInput">
                    <label for="valor_deposito" class="form-label">Valor do Depósito (R$)</label>
                    <div class="input-group">
                        <span class="input-prefix">R$</span>
                        <input 
                            type="number" 
                            id="valor_deposito" 
                            name="valor_deposito" 
                            class="form-input" 
                            step="0.01" 
                            min="0.01" 
                            placeholder="0,00" 
                            required
                            readonly
                            disabled
                        >
                    </div>
                    <small class="form-hint">Selecione as cédulas/moedas do depósito</small>
                </div>
                <!-- Seletor de Cédulas e Moedas -->
                <div class="cedulas-selector">
                    <div class="cedulas-grid">
                        <div class="cedula-items">
                            <!-- R$ 200 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 200</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="200">-</button>
                                    <span class="cedula-count" data-value="200">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="200">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 100 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 100</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="100">-</button>
                                    <span class="cedula-count" data-value="100">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="100">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 50 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 50</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="50">-</button>
                                    <span class="cedula-count" data-value="50">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="50">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 20 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 20</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="20">-</button>
                                    <span class="cedula-count" data-value="20">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="20">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 10 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 10</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="10">-</button>
                                    <span class="cedula-count" data-value="10">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="10">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 2 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 2</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="2">-</button>
                                    <span class="cedula-count" data-value="2">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="2">+</button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="cedula-items">
                            <!-- R$ 1 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 1</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="1">-</button>
                                    <span class="cedula-count" data-value="1">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="1">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 0.50 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 0,50</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="0.5">-</button>
                                    <span class="cedula-count" data-value="0.5">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="0.5">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 0.25 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 0,25</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="0.25">-</button>
                                    <span class="cedula-count" data-value="0.25">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="0.25">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 0.10 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 0,10</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="0.1">-</button>
                                    <span class="cedula-count" data-value="0.1">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="0.1">+</button>
                                </div>
                            </div>
                            
                            <!-- R$ 0.05 -->
                            <div class="cedula-item">
                                <div class="cedula-info">
                                    <span class="cedula-value">R$ 0,05</span>
                                </div>
                                <div class="cedula-controls">
                                    <button type="button" class="btn-counter btn-counter-minus" data-value="0.05">-</button>
                                    <span class="cedula-count" data-value="0.05">0</span>
                                    <button type="button" class="btn-counter btn-counter-plus" data-value="0.05">+</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Botões de Ação -->
                <div class="deposito-actions mt-4">
                    <div class="d-flex gap-2 flex-wrap">
                        <button type="button" class="btn btn-outline" id="limpar-deposito">
                            Limpar
                        </button>
                        <button type="submit" class="btn btn-success flex-grow-1">
                            Realizar Depósito
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Switcher de Regras
    const regraInputs = document.querySelectorAll('.switcher-input');
    const descricaoPadrao = document.getElementById('descricao_padrao');
    const descricaoAlternativa = document.getElementById('descricao_alternativa');
    
    regraInputs.forEach(input => {
        input.addEventListener('change', function() {
            if (this.id === 'regra_padrao' && this.checked) {
                descricaoPadrao.style.display = 'block';
                descricaoAlternativa.style.display = 'none';
            } else if (this.id === 'regra_alternativa' && this.checked) {
                descricaoPadrao.style.display = 'none';
                descricaoAlternativa.style.display = 'block';
            }
        });
    });
    
    // Valores Sugeridos
    const suggestedButtons = document.querySelectorAll('.suggested-value');
    const valorSaqueInput = document.getElementById('valor_saque');
    
    suggestedButtons.forEach(button => {
        button.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            valorSaqueInput.value = value;
            valorSaqueInput.focus();
        });
    });
    
    // Formatação do input de valor
    valorSaqueInput.addEventListener('blur', function() {
        if (this.value) {
            this.value = parseFloat(this.value).toFixed(2);
        }
    });
});

document.addEventListener('DOMContentLoaded', function() {
    const valorDepositoInput = document.getElementById('valor_deposito');
    const cedulaCounts = document.querySelectorAll('.cedula-count');
    const btnCountersPlus = document.querySelectorAll('.btn-counter-plus');
    const btnCountersMinus = document.querySelectorAll('.btn-counter-minus');
    const btnLimpar = document.getElementById('limpar-deposito');
    const totalCedulasSpan = document.getElementById('total-cedulas');
    const totalMoedasSpan = document.getElementById('total-moedas');
    
    // Valores das cédulas e moedas
    const valores = {
        '200': 200,
        '100': 100,
        '50': 50,
        '20': 20,
        '10': 10,
        '2': 2,
        '1': 1,
        '0.5': 0.5,
        '0.25': 0.25,
        '0.1': 0.1,
        '0.05': 0.05
    };
    
    // Função para atualizar o valor total
    function atualizarValorTotal() {
        let total = 0;
        let totalCedulas = 0;
        let totalMoedas = 0;
        
        cedulaCounts.forEach(countElement => {
            const valor = parseFloat(countElement.getAttribute('data-value'));
            const quantidade = parseInt(countElement.textContent);
            
            if (!isNaN(quantidade) && quantidade > 0) {
                const subtotal = valor * quantidade;
                total += subtotal;
                
                // Classificar como cédula ou moeda
                if (valor >= 2) {
                    totalCedulas += quantidade;
                } else {
                    totalMoedas += quantidade;
                }
            }
        });
        
        // Atualizar os campos
        valorDepositoInput.value = total.toFixed(2);
        
        // Validar o formulário
        const submitBtn = document.querySelector('#depositoForm button[type="submit"]');
        if (total > 0) {
            submitBtn.disabled = false;
        } else {
            submitBtn.disabled = true;
        }
    }
    
    // Event listeners para os botões de incremento
    btnCountersPlus.forEach(btn => {
        btn.addEventListener('click', function() {
            const valor = this.getAttribute('data-value');
            const countElement = document.querySelector(`.cedula-count[data-value="${valor}"]`);
            let count = parseInt(countElement.textContent);
            countElement.textContent = count + 1;
            atualizarValorTotal();
        });
    });
    
    // Event listeners para os botões de decremento
    btnCountersMinus.forEach(btn => {
        btn.addEventListener('click', function() {
            const valor = this.getAttribute('data-value');
            const countElement = document.querySelector(`.cedula-count[data-value="${valor}"]`);
            let count = parseInt(countElement.textContent);
            if (count > 0) {
                countElement.textContent = count - 1;
                atualizarValorTotal();
            }
        });
    });
    
    // Event listener para o botão limpar
    btnLimpar.addEventListener('click', function() {
        cedulaCounts.forEach(countElement => {
            countElement.textContent = '0';
        });
        atualizarValorTotal();
    });
    
    // Inicializar o estado do formulário
    atualizarValorTotal();
});

document.addEventListener('DOMContentLoaded', function() {
    // Função para os botões de valores sugeridos
    const suggestedButtons = document.querySelectorAll('.suggested-value');
    
    suggestedButtons.forEach(button => {
        button.addEventListener('click', function() {
            debugger;
            const value = this.getAttribute('data-value');
            const form = this.closest('.operation-form');
            const input = form.querySelector('input[type="number"]');
            
            if (input) {
                input.value = value;
                input.focus();
            }
        });
    });
    
    // Formatação automática dos inputs monetários
    const moneyInputs = document.querySelectorAll('input[type="number"]');
    
    moneyInputs.forEach(input => {
        input.addEventListener('blur', function() {
            if (this.value) {
                this.value = parseFloat(this.value).toFixed(2);
            }
        });
        
        input.addEventListener('input', function() {
            // Garante que o valor seja positivo
            if (parseFloat(this.value) < 0) {
                this.value = Math.abs(parseFloat(this.value));
            }
        });
    });

    document.getElementById('depositoForm').addEventListener('submit', function(e) {
        debugger;
        e.preventDefault();
        
        // Coletar as quantidades de cada cédula/moeda
        const cedulasData = {};
        const cedulaCounts = document.querySelectorAll('.cedula-count');
        
        cedulaCounts.forEach(countElement => {
            const valor = countElement.getAttribute('data-value');
            const quantidade = parseInt(countElement.textContent);
            
            if (quantidade > 0) {
                cedulasData["'" + valor.toString() + "'"] = quantidade;
            }
        });
        
        // Atualizar o campo hidden com os dados das cédulas
        document.getElementById('cedulasInput').value = JSON.stringify(cedulasData);
        
        // Agora enviar o formulário
        this.submit();
    });

});
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>