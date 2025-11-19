import { test, expect } from '@playwright/test';

test('Validar tela de Cadastro de novas Contas', async ({ page }) => {
  // Acessa a página inicial
  await page.goto('http://localhost/caixaeletronico/public/');
  
  // Localiza e clica no botão "Nova Conta"
  const novaContaLink = page.locator('a.nav-link', { hasText: 'Nova Conta' });
  await expect(novaContaLink).toBeVisible();
  await novaContaLink.click();

  // Verifica se foi redirecionado para a tela de cadastro
  await expect(page).toHaveURL(/\/conta\/criar/);
  
  // Verifica elementos visíveis na tela de cadastro
  await expect(page.locator('h1.page-title')).toHaveText('Criar Nova Conta');
  
  // Verifica todos os campos do formulário
  await expect(page.locator('label[for="conta_nome"]')).toHaveText('Nome da Conta:');
  await expect(page.locator('#conta_nome')).toBeVisible();
  
  await expect(page.locator('label[for="conta_email"]')).toHaveText('E-mail:');
  await expect(page.locator('#conta_email')).toBeVisible();
  
  await expect(page.locator('label[for="conta_senha"]')).toHaveText('Senha:');
  await expect(page.locator('#conta_senha')).toBeVisible();
  
  await expect(page.locator('label[for="saldo_inicial"]')).toHaveText('Saldo Inicial (R$):');
  await expect(page.locator('#saldo_inicial')).toBeVisible();
  
  await expect(page.locator('button[type="submit"]')).toHaveText('Criar Conta');

  // Valida tipos de input e atributos específicos
  await expect(page.locator('#conta_email')).toHaveAttribute('type', 'text');
  await expect(page.locator('#conta_senha')).toHaveAttribute('type', 'password');
  await expect(page.locator('#saldo_inicial')).toHaveAttribute('type', 'number');
  await expect(page.locator('#saldo_inicial')).toHaveAttribute('step', '0.01');
  await expect(page.locator('#saldo_inicial')).toHaveAttribute('min', '0');
  await expect(page.locator('#saldo_inicial')).toHaveValue('0.00');

  // Testa preenchimento e submissão do formulário
  await page.fill('#conta_nome', 'João Melão');
  await page.fill('#conta_email', 'joao.melao@email.com');
  await page.fill('#conta_senha', '123');
  await page.fill('#saldo_inicial', '250.50');

  // Submete o formulário
  await page.click('button[type="submit"]');

  // Verifica se foi redirecionado para tela de login
  await expect(page).toHaveURL(/\/login/);

  // Verifica se existe um elemento de notificação com a classe 'success' e o texto esperado
  const notificacaoSucesso = page.locator('.notification.success', { hasText: 'Conta criada com sucesso' });
  
  // Aguarda a notificação aparecer 
  await expect(notificacaoSucesso).toBeVisible({ timeout: 5000 });
  
  // Verifica se o texto está correto
  await expect(notificacaoSucesso).toHaveText('Conta criada com sucesso');
  
  // Verifica se tem a classe CSS correta para notificação de sucesso
  await expect(notificacaoSucesso).toHaveClass(/notification/);
  await expect(notificacaoSucesso).toHaveClass(/success/);
});
