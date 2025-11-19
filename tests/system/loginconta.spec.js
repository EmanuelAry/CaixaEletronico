import { test, expect } from '@playwright/test';

test('Validar tela de Login em Conta', async ({ page }) => {
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

  // 6. Valida obrigatoriedade dos campos
  const requiredFields = ['#conta_email', '#conta_senha'];
  for (const field of requiredFields) {
    await expect(page.locator(field)).toHaveAttribute('required');
  }

  // Testa preenchimento e submissão do formulário
  await page.fill('#conta_email', 'joao.melao@email.com');
  await page.fill('#conta_senha', '123');

  // Submete o formulário
  await page.click('button[type="submit"]');

  // Verifica se foi redirecionado para a ação de criação
  await expect(page).toHaveURL(/\/conta\/menuCaixaView/);

  // Verifica se existe um elemento de notificação com a classe 'success' e o texto esperado
  const notificacaoSucesso = page.locator('.notification.success', { hasText: 'Login realizado com sucesso' });
  
  // Aguarda a notificação aparecer (timeout de 5 segundos)
  await expect(notificacaoSucesso).toBeVisible({ timeout: 5000 });
  
  // Verifica se o texto está correto
  await expect(notificacaoSucesso).toHaveText('Login realizado com sucesso');
  
  // Verifica se tem a classe CSS correta para notificação de sucesso
  await expect(notificacaoSucesso).toHaveClass(/notification/);
  await expect(notificacaoSucesso).toHaveClass(/success/);
});


test('Validar bloqueio Login em Conta com campos nulos', async ({ page }) => {
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

  // Valida obrigatoriedade dos campos
  const requiredFields = ['#conta_email', '#conta_senha'];
  for (const field of requiredFields) {
    await expect(page.locator(field)).toHaveAttribute('required');
  }

  // Testa preenchimento e submissão do formulário
  await page.fill('#conta_email', '');
  await page.fill('#conta_senha', '');
  
  // Submeter o formulário sem preencher os campos
  await page.click('button[type="submit"]');

  // Verificar se os campos estão com borda vermelha (estilo inline)
  const emailInput = page.locator('#conta_email');
  const senhaInput = page.locator('#conta_senha');

  await expect(emailInput).toHaveCSS('border-color', 'rgb(52, 152, 219)'); // #e74c3c em rgb

});
