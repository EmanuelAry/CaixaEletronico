<?php 
use app\helpers\UrlHelper;
include __DIR__ . '/../templates/header.php';
?>

<h1 class="page-title">Selecionar Conta</h1>

<?php include __DIR__ . '/../templates/notifications.php'; ?>

<div style="margin-bottom: 4rem">
    <h2 class="subtitle">Entrar em uma conta existente</h2>
    <form class="form-group" method="POST" action="<?php echo UrlHelper::baseUrl('/conta/loginAction/?XDEBUG_SESSION=VSCODE'); ?>">
        <div class="form-group">
            <label class="form-label" for="conta_email">Email:</label>
            <input class="form-input" type="email" id="conta_email" name="conta_email" required 
                   placeholder="Digite o email da conta">
        </div>
        
        <div class="form-group">
            <label class="form-label" for="conta_senha">Senha:</label>
            <input class="form-input" type="password" id="conta_senha" name="conta_senha" required 
                   placeholder="Digite a senha">
        </div>
        
        <button style="float: right; margin-top: 1rem" class="btn btn-primary" type="submit">
            Fazer Login
        </button>
    </form>
</div>

<div>
    <h2 class="subtitle">Ou selecione uma conta da lista</h2>
    <?php if (empty($contas)): ?>
        <p class="text-center">Nenhuma conta encontrada.</p>
    <?php else: ?>
        <ul class="conta-list">
            <?php foreach ($contas as $conta): ?>
                <li class="conta-item" 
                    data-email="<?php echo htmlspecialchars($conta['conta_email'] ?? ''); ?>"
                    onclick="selecionarConta(this)">
                    <strong>ID: <?php echo $conta['conta_id']; ?></strong> - 
                    <?php echo htmlspecialchars($conta['conta_nome']); ?> - 
                    Saldo: R$ <?php echo number_format($conta['conta_saldo'], 2, ',', '.'); ?>
                    <input type="hidden" class="conta-email" value="<?php echo htmlspecialchars($conta['conta_email'] ?? ''); ?>">
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div>
    <p>Não tem uma conta? <a href="<?= UrlHelper::baseUrl('conta/criar') ?>">Criar nova conta</a></p>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>