<?php   
use app\helpers\UrlHelper;
include __DIR__ . '/../templates/header.php'; 
?>

<h1 class="page-title">Criar Nova Conta</h1>

<?php include __DIR__ . '/../templates/notifications.php'; ?>

<form method="POST" action="<?php echo UrlHelper::baseUrl('/conta/criarContaAction/?XDEBUG_SESSION=VSCODE'); ?>">
    <div>
        <label class="form-label" for="conta_nome">Nome da Conta:</label>
        <input class="form-input" type="text" id="conta_nome" name="conta_nome" required>
    </div>
    <div>
        <label class="form-label" for="conta_email">E-mail:</label>
        <input class="form-input" type="text" id="conta_email" name="conta_email" required>
    </div>
    <div>
        <label class="form-label" for="conta_senha">Senha:</label>
        <input class="form-input" type="password" id="conta_senha" name="conta_senha" required>
    </div>
    <div>
        <label class="form-label" for="saldo_inicial">Saldo Inicial (R$):</label>
        <input class="form-input" type="number" id="saldo_inicial" name="saldo_inicial" step="0.01" min="0" value="0.00" required>
    </div>
    <div style="float: right; margin-top: 1rem">
        <button class="btn btn-primary" type="submit">Criar Conta</button>
    </div>
</form>

<?php include __DIR__ . '/../templates/footer.php'; ?>