import { test, expect } from '@playwright/test';

test('Validar tela de Saque Conta', async ({ page }) => {
  // Acessa a página inicial
  await page.goto('http://localhost/caixaeletronico/public/');
  
  // Localiza e clica no botão "Selecionar Conta"
  const selecionarContaLink = page.locator('a.nav-link', { hasText: 'Selecionar Conta' });
  await expect(selecionarContaLink).toBeVisible();
  await selecionarContaLink.click();

  // Verifica se foi redirecionado para a tela de login
  await expect(page).toHaveURL(/\/conta\/login/);
  
  // Verifica elementos visíveis na tela de login
  await expect(page.locator('h1.page-title')).toHaveText('Selecionar Conta');
  
  // Verifica todos os campos do formulário
  await expect(page.locator('label[for="conta_email"]')).toHaveText('E-mail:');
  await expect(page.locator('#conta_email')).toBeVisible();
  
  await expect(page.locator('label[for="conta_senha"]')).toHaveText('Senha:');
  await expect(page.locator('#conta_senha')).toBeVisible();
  
  await expect(page.locator('button[type="submit"]')).toHaveText('Fazer Login');


  // Testa preenchimento e submissão do formulário
  await page.fill('#conta_email', 'joao.melao@email.com');
  await page.fill('#conta_senha', '123');

  // Submete o formulário
  await page.click('button[type="submit"]');

  // Verifica se foi redirecionado para a ação de criação
  await expect(page).toHaveURL(/\/conta\/menuCaixaView/);

  // Verifica se existe um elemento de notificação com a classe 'success' e o texto esperado
  const notificacaoLogin = page.locator('.notification.success', { hasText: 'Login realizado com sucesso' });
  
  // Aguarda a notificação aparecer (timeout de 5 segundos)
  await expect(notificacaoLogin).toBeVisible({ timeout: 5000 });
  
  // Verifica se o texto está correto
  await expect(notificacaoLogin).toHaveText('Login realizado com sucesso');
  
  // Verifica o título da página
    await expect(page.locator('h1.page-title')).toHaveText('Operações do Caixa Eletrônico');

    // ========== VALIDAÇÃO DO CARD DE SAQUE ==========
    await expect(page.locator('.card-header:has-text("Saque")')).toBeVisible();
    
    // Campos do saque
    await expect(page.locator('label[for="valor_saque"]')).toHaveText('Valor do Saque (R$)');
    await expect(page.locator('#valor_saque')).toBeVisible();
    await expect(page.locator('#valor_saque')).toHaveAttribute('type', 'number');
    await expect(page.locator('#valor_saque')).toHaveAttribute('step', '0.01');
    await expect(page.locator('#valor_saque')).toHaveAttribute('min', '0.05');
    await expect(page.locator('#valor_saque')).toHaveAttribute('required', '');
    
    // Switcher de regras
    await expect(page.locator('label:has-text("Regra de Saque")')).toBeVisible();
    await expect(page.locator('#regra_padrao')).toBeChecked();
    await expect(page.locator('#regra_alternativa')).not.toBeChecked();
    await expect(page.locator('#descricao_padrao')).toBeVisible();
    await expect(page.locator('#descricao_alternativa')).not.toBeVisible();

    // Botão de realizar saque
    await expect(page.locator('button:has-text("Realizar Saque")')).toBeVisible();


    // Preenche o valor do saque
    await page.fill('#valor_saque', '5.00');
    
    // Seleciona regra padrão (já está selecionada por padrão)
    
    // Submete o formulário
    await page.click('button:has-text("Realizar Saque")');
    
    // Verifica se existe um elemento de notificação com a classe 'success' e o texto esperado
    const notificacaoSucesso = page.locator('.notification.success', { hasText: 'Saque de R$ 5.00 realizado com sucesso.' });
    
    // Aguarda a notificação aparecer 
    await expect(notificacaoSucesso).toBeVisible({ timeout: 5000 });
    
    // Verifica se o texto está correto
    await expect(notificacaoSucesso).toHaveText('Saque de R$ 5.00 realizado com sucesso.');
    
    // Verifica se tem a classe CSS correta para notificação de sucesso
    await expect(notificacaoSucesso).toHaveClass(/notification/);
    await expect(notificacaoSucesso).toHaveClass(/success/);
});



test('Validar tela de Depósito Conta', async ({ page }) => {
  // Acessa a página inicial
  await page.goto('http://localhost/caixaeletronico/public/');
  
  // Localiza e clica no botão "Selecionar Conta"
  const selecionarContaLink = page.locator('a.nav-link', { hasText: 'Selecionar Conta' });
  await expect(selecionarContaLink).toBeVisible();
  await selecionarContaLink.click();

  // Verifica se foi redirecionado para a tela de login
  await expect(page).toHaveURL(/\/conta\/login/);
  
  // Verifica elementos visíveis na tela de login
  await expect(page.locator('h1.page-title')).toHaveText('Selecionar Conta');
  
  // Verifica todos os campos do formulário
  await expect(page.locator('label[for="conta_email"]')).toHaveText('E-mail:');
  await expect(page.locator('#conta_email')).toBeVisible();
  
  await expect(page.locator('label[for="conta_senha"]')).toHaveText('Senha:');
  await expect(page.locator('#conta_senha')).toBeVisible();
  
  await expect(page.locator('button[type="submit"]')).toHaveText('Fazer Login');


  // Testa preenchimento e submissão do formulário
  await page.fill('#conta_email', 'joao.melao@email.com');
  await page.fill('#conta_senha', '123');

  // Submete o formulário
  await page.click('button[type="submit"]');

  // Verifica se foi redirecionado para a ação de criação
  await expect(page).toHaveURL(/\/conta\/menuCaixaView/);

  // Verifica se existe um elemento de notificação com a classe 'success' e o texto esperado
  const notificacaoLogin = page.locator('.notification.success', { hasText: 'Login realizado com sucesso' });
  
  // Aguarda a notificação aparecer (timeout de 5 segundos)
  await expect(notificacaoLogin).toBeVisible({ timeout: 5000 });
  
  // Verifica se o texto está correto
  await expect(notificacaoLogin).toHaveText('Login realizado com sucesso');
  
  // Verifica o título da página
    await expect(page.locator('h1.page-title')).toHaveText('Operações do Caixa Eletrônico');

    // ========== VALIDAÇÃO DO CARD DE DEPÓSITO ==========
    await expect(page.locator('.card-header:has-text("Depósito")')).toBeVisible();
    
    // Campo de valor do depósito
    await expect(page.locator('label[for="valor_deposito"]')).toHaveText('Valor do Depósito (R$)');
    await expect(page.locator('#valor_deposito')).toBeVisible();
    await expect(page.locator('#valor_deposito')).toHaveAttribute('readonly', '');
    await expect(page.locator('#valor_deposito')).toHaveAttribute('disabled', '');

    // Botões de controle
    await expect(page.locator('button:has-text("Limpar")')).toBeVisible();
    await expect(page.locator('button:has-text("Realizar Depósito")')).toBeVisible();


    // Adiciona cédulas para depósito
    await page.click('.cedula-item:has-text("R$ 100") .btn-counter-plus');
    await page.click('.cedula-item:has-text("R$ 50") .btn-counter-plus');
    
    // Verifica valor total
    await expect(page.locator('#valor_deposito')).toHaveValue('150.00');
    
    // Realiza o depósito
    await page.click('button:has-text("Realizar Depósito")');
            
    // Verifica se existe um elemento de notificação com a classe 'success' e o texto esperado
    const notificacaoSucesso = page.locator('.notification.success', { hasText: 'Depósito de R$ 150.00 realizado com sucesso.' });
    
    // Aguarda a notificação aparecer 
    await expect(notificacaoSucesso).toBeVisible({ timeout: 5000 });
    
    // Verifica se o texto está correto
    await expect(notificacaoSucesso).toHaveText('Depósito de R$ 150.00 realizado com sucesso.');
    
    // Verifica se tem a classe CSS correta para notificação de sucesso
    await expect(notificacaoSucesso).toHaveClass(/notification/);
    await expect(notificacaoSucesso).toHaveClass(/success/);
});