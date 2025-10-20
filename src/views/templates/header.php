<?php   
use app\helpers\UrlHelper;
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Caixa Eletrônico</title>
    <link rel="stylesheet" href="<?= UrlHelper::asset('css/style.css') ?>">
</head>
<body>
    <div class="container">
        <header class="header">
            <h1>Caixa Eletrônico</h1>
            <nav class="nav">
                <a href="<?= UrlHelper::baseUrl('conta/selecionar') ?>" class="nav-link">Selecionar Conta</a>
                <a href="<?= UrlHelper::baseUrl('conta/criar') ?>" class="nav-link">Nova Conta</a>
                <a href="<?= UrlHelper::baseUrl('caixa/estoqueCaixaView') ?>" class="nav-link">Estoque Caixa</a>
            </nav>
            <br>
            <?php if (isset($contaSelecionada) && $contaSelecionada): ?>
                <div class="conta-selecionada-info">
                    <div class="conta-info-card">
                        <div class="conta-info-header">
                            <strong>Conta Selecionada</strong>
                        </div>
                        <div class="conta-info-body">
                            <div class="conta-info-item">
                                <span class="conta-info-label">ID:</span>
                                <span class="conta-info-value"><?= htmlspecialchars($contaSelecionada['conta_id']) ?></span>
                            </div>
                            <div class="conta-info-item">
                                <span class="conta-info-label">Nome:</span>
                                <span class="conta-info-value"><?= htmlspecialchars($contaSelecionada['conta_nome']) ?></span>
                            </div>
                            <div class="conta-info-item">
                                <span class="conta-info-label">Saldo:</span>
                                <span class="conta-info-value saldo">R$ <?= number_format($contaSelecionada['conta_saldo'], 2, ',', '.') ?></span>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
        </header>
        <main class="main-content"> 