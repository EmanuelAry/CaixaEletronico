<?php include __DIR__ . '/../templates/header.php'; ?>

<h1>Criar Nova Conta</h1>

<?php include __DIR__ . '/../templates/notifications.php'; ?>

<form method="POST" action="/conta">
    <div>
        <label for="conta_nome">Nome da Conta:</label>
        <input type="text" id="conta_nome" name="conta_nome" required>
    </div>
    <div>
        <label for="saldo_inicial">Saldo Inicial (R$):</label>
        <input type="number" id="saldo_inicial" name="saldo_inicial" step="0.01" min="0" value="0.00" required>
    </div>
    <button type="submit">Criar Conta</button>
</form>

<div>
    <a href="/">Voltar para a seleção de contas</a>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>