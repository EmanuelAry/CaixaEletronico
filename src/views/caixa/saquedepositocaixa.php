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
                <input 
                    type="hidden" 
                    id="conta_id" 
                    name="conta_id" 
                    value="<?= $contaSelecionada['conta_id'] ?>"
                >
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
                            min="0.05" 
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
                            <input type="radio" id="regra_padrao" name="regra_saque" value=0 class="switcher-input" checked>
                            <label for="regra_padrao" class="switcher-option">
                                <span class="switcher-text">Regra Padrão</span>
                            </label>
                            
                            <input type="radio" id="regra_alternativa" name="regra_saque" value=1 class="switcher-input">
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
                    <input 
                        type="hidden" 
                        id="conta_id" 
                        name="conta_id" 
                        type="number" 
                        value="<?= $contaSelecionada['conta_id'] ?>"
                    >
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
    // 1. Switcher de Regras
    initRegraSwitcher();
    
    // 2. Sistema de Valores Sugeridos (unificado)
    initSuggestedValues();
    
    // 3. Sistema de Depósito
    initDepositoSystem();
    
    // 4. Formatação Monetária Global
    initMoneyFormatting();
});

// FUNÇÕES MODULARIZADAS
function initRegraSwitcher() {
    const regraInputs = document.querySelectorAll('.switcher-input');
    const descricaoPadrao = document.getElementById('descricao_padrao');
    const descricaoAlternativa = document.getElementById('descricao_alternativa');
    
    regraInputs.forEach(input => {
        input.addEventListener('change', function() {
            const showPadrao = this.id === 'regra_padrao' && this.checked;
            descricaoPadrao.style.display = showPadrao ? 'block' : 'none';
            descricaoAlternativa.style.display = showPadrao ? 'none' : 'block';
        });
    });
}

function initSuggestedValues() {
    const suggestedButtons = document.querySelectorAll('.suggested-value');
    
    suggestedButtons.forEach(button => {
        button.addEventListener('click', function() {
            const value = this.getAttribute('data-value');
            const form = this.closest('.operation-form');
            const input = form?.querySelector('input[type="number"]') || 
                         document.getElementById('valor_saque');
            
            if (input) {
                input.value = value;
                input.focus();
            }
        });
    });
}

function initDepositoSystem() {
    const valorDepositoInput = document.getElementById('valor_deposito');
    if (!valorDepositoInput) return; // Safety check
    
    const cedulaCounts = document.querySelectorAll('.cedula-count');
    const btnCountersPlus = document.querySelectorAll('.btn-counter-plus');
    const btnCountersMinus = document.querySelectorAll('.btn-counter-minus');
    const btnLimpar = document.getElementById('limpar-deposito');
    const depositoForm = document.getElementById('depositoForm');
    
    // Event listeners para contadores
    btnCountersPlus.forEach(btn => {
        btn.addEventListener('click', () => handleCounterChange(btn, 1));
    });
    
    btnCountersMinus.forEach(btn => {
        btn.addEventListener('click', () => handleCounterChange(btn, -1));
    });
    
    // Botão limpar
    btnLimpar?.addEventListener('click', limparDeposito);
    
    // Submit handler
    depositoForm?.addEventListener('submit', handleDepositoSubmit);
    
    // Inicializar
    atualizarValorTotal();
}

function initMoneyFormatting() {
    const moneyInputs = document.querySelectorAll('input[type="number"]');
    
    moneyInputs.forEach(input => {
        input.addEventListener('blur', formatMoneyInput);
        input.addEventListener('input', ensurePositiveValue);
    });
}

// FUNÇÕES AUXILIARES
function handleCounterChange(button, change) {
    const valor = button.getAttribute('data-value');
    const countElement = document.querySelector(`.cedula-count[data-value="${valor}"]`);
    let count = parseInt(countElement.textContent);
    
    const newCount = count + change;
    if (newCount >= 0) {
        countElement.textContent = newCount;
        atualizarValorTotal();
    }
}

function limparDeposito() {
    document.querySelectorAll('.cedula-count').forEach(countElement => {
        countElement.textContent = '0';
    });
    atualizarValorTotal();
}

function handleDepositoSubmit(e) {
    e.preventDefault();
    
    const cedulasData = {};
    document.querySelectorAll('.cedula-count').forEach(countElement => {
        const valor = countElement.getAttribute('data-value');
        const quantidade = parseInt(countElement.textContent);
        
        if (quantidade > 0) {
            cedulasData[valor] = quantidade;
        }
    });
    
    document.getElementById('cedulasInput').value = JSON.stringify(cedulasData);
    this.submit();
}

function formatMoneyInput() {
    if (this.value) {
        this.value = Math.max(0, parseFloat(this.value) || 0).toFixed(2);
    }
}

function ensurePositiveValue() {
    if (parseFloat(this.value) < 0) {
        this.value = Math.abs(parseFloat(this.value)) || 0;
    }
}

function atualizarValorTotal() {
    const valorDepositoInput = document.getElementById('valor_deposito');
    const cedulaCounts = document.querySelectorAll('.cedula-count');
    const submitBtn = document.querySelector('#depositoForm button[type="submit"]');
    
    if (!valorDepositoInput || !submitBtn) return;
    
    let total = 0;
    
    cedulaCounts.forEach(countElement => {
        const valor = parseFloat(countElement.getAttribute('data-value'));
        const quantidade = parseInt(countElement.textContent);
        
        if (!isNaN(quantidade) && quantidade > 0) {
            total += valor * quantidade;
        }
    });
    
    valorDepositoInput.value = total.toFixed(2);
    submitBtn.disabled = total <= 0;
}
</script>

<?php include __DIR__ . '/../templates/footer.php'; ?>