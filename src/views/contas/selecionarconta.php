<?php 
use app\helpers\UrlHelper;
include __DIR__ . '/../templates/header.php';
?>

<h1>Selecionar Conta</h1>

<?php include __DIR__ . '/../templates/notifications.php'; ?>

<div>
    <h2>Entrar em uma conta existente</h2>
    <form method="POST" action="<?php echo UrlHelper::baseUrl('/conta/alternar'); ?>">
        <label for="conta_id">ID da Conta:</label>
        <input type="number" id="conta_id" name="conta_id" required>
        <button type="submit">Entrar</button>
    </form>
</div>

<div>
    <h2>Ou selecione uma conta da lista</h2>
    <?php if (empty($contas)): ?>
        <p>Nenhuma conta encontrada.</p>
    <?php else: ?>
        <ul class="conta-list">
            <?php foreach ($contas as $conta): ?>
                <li class="conta-item" 
                    onclick="window.location.href='<?php echo UrlHelper::baseUrl('/conta/' . $conta['conta_id']); ?>'">
                    <strong>ID: <?php echo $conta['conta_id']; ?></strong> - 
                    <?php echo htmlspecialchars($conta['conta_nome']); ?> - 
                    Saldo: R$ <?php echo number_format($conta['conta_saldo'], 2, ',', '.'); ?>
                </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>

<div>
    <p>Não tem uma conta? <a href="<?= UrlHelper::baseUrl('conta/criar') ?>">Criar nova conta</a></p>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>