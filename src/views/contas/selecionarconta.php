<?php 
use app\helpers\UrlHelper;
include __DIR__ . '/../templates/header.php';
?>

<h1 class="page-title">Selecionar Conta</h1>

<?php include __DIR__ . '/../templates/notifications.php'; ?>

<div style="margin-bottom: 4rem">
    <h2 class="subtitle">Entrar em uma conta existente</h2>
    <form class="form-group" method="POST" action="<?php echo UrlHelper::baseUrl('/conta/alternarContaAction'); ?>">
        <label class="form-label" for="conta_id">ID da Conta:</label>
        <input class="form-input" type="number" id="conta_id" name="conta_id" required>
        <!-- <div style="float: right; margin-top: 1rem"> -->
            <button style="float: right; margin-top: 1rem" class="btn btn-primary" type="submit">Entrar</button>
        <!-- </div> -->
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
                    onclick="window.location.href='<?php echo UrlHelper::baseUrl('/conta/alternarContaAction/?conta_id=' . $conta['conta_id']); ?>'+''">
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