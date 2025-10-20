<?php 
use app\helpers\UrlHelper;
include __DIR__ . '/../templates/header.php';
?>

<div class="page-header d-flex justify-between align-center flex-wrap gap-2 mb-4">
    <h1 class="page-title">Estoque do Caixa Eletrônico</h1>
</div>

<?php include __DIR__ . '/../templates/notifications.php'; ?>

<div class="estoque-content">
    <!-- Cards de Resumo -->
    <div class="resumo-cards mb-4">
        <div class="card">
            <div class="card-body">
                <div class="resumo-grid">
                    <div class="resumo-item">
                        <span class="resumo-label">Valor Total em Estoque</span>
                        <span class="resumo-value total">R$ <?= isset($valor_total) ? number_format($valor_total, 2, ',', '.') : '0,00' ?></span>
                    </div>
                    <div class="resumo-item">
                        <span class="resumo-label">Total de Cédulas</span>
                        <span class="resumo-value"><?= isset($total_cedulas) ? $total_cedulas : 0 ?></span>
                    </div>
                    <div class="resumo-item">
                        <span class="resumo-label">Total de Moedas</span>
                        <span class="resumo-value"><?= isset($total_moedas) ? $total_moedas : 0 ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="estoque-layout">
        <!-- Tabela de Estoque -->
        <div class="estoque-table-section">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Estoque de Cédulas e Moedas</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Valor</th>
                                    <th>Tipo</th>
                                    <th>Quantidade</th>
                                    <th>Valor Total</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $notas = [
                                    ['valor' => 200, 'tipo' => 'Cédula', 'quantidade' => $estoque['200'] ?? 0],
                                    ['valor' => 100, 'tipo' => 'Cédula', 'quantidade' => $estoque['100'] ?? 0],
                                    ['valor' => 50, 'tipo' => 'Cédula', 'quantidade' => $estoque['50'] ?? 0],
                                    ['valor' => 20, 'tipo' => 'Cédula', 'quantidade' => $estoque['20'] ?? 0],
                                    ['valor' => 10, 'tipo' => 'Cédula', 'quantidade' => $estoque['10'] ?? 0],
                                    ['valor' => 2, 'tipo' => 'Cédula', 'quantidade' => $estoque['2'] ?? 0],
                                    ['valor' => 1, 'tipo' => 'Moeda', 'quantidade' => $estoque['1'] ?? 0],
                                    ['valor' => 0.50, 'tipo' => 'Moeda', 'quantidade' => $estoque['0.50'] ?? 0],
                                    ['valor' => 0.25, 'tipo' => 'Moeda', 'quantidade' => $estoque['0.25'] ?? 0],
                                    ['valor' => 0.10, 'tipo' => 'Moeda', 'quantidade' => $estoque['0.10'] ?? 0],
                                    ['valor' => 0.05, 'tipo' => 'Moeda', 'quantidade' => $estoque['0.05'] ?? 0],
                                ];

                                foreach ($notas as $nota):
                                    $valor_total_nota = $nota['valor'] * $nota['quantidade'];
                                    $status_class = $nota['quantidade'] == 0 ? 'status-empty' : ($nota['quantidade'] < 10 ? 'status-low' : 'status-ok');
                                    $status_text = $nota['quantidade'] == 0 ? 'Esgotado' : ($nota['quantidade'] < 10 ? 'Baixo' : 'OK');
                                ?>
                                <tr>
                                    <td class="valor-cell">
                                        <span class="valor-display">R$ <?= number_format($nota['valor'], 2, ',', '.') ?></span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $nota['tipo'] === 'Cédula' ? 'badge-cedula' : 'badge-moeda' ?>">
                                            <?= $nota['tipo'] ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="quantidade <?= $status_class ?>">
                                            <?= $nota['quantidade'] ?>
                                        </span>
                                    </td>
                                    <td class="valor-total">
                                        R$ <?= number_format($valor_total_nota, 2, ',', '.') ?>
                                    </td>
                                    <td>
                                        <span class="status-indicator <?= $status_class ?>">
                                            <?= $status_text ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Painel de Operações -->
        <div class="operacoes-section">
            <div class="card">
                <div class="card-header">
                    <h2 class="card-title">Operações de Estoque</h2>
                </div>
                <div class="card-body">
                    <div class="operations-grid">
                        
                        <div class="card-body">
                            <form method="POST" action="<?= UrlHelper::baseUrl('caixa/descarregar') ?>" class="operation-form">
                                <button type="submit" class="btn btn-danger btn-block">
                                    Descarregar
                                </button>
                                <small class="form-hint">Zera as quantidades de todas as cédulas/moedas</small>
                            </form>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="<?= UrlHelper::baseUrl('caixa/carregar') ?>" class="operation-form">
                                <button type="submit" class="btn btn-success btn-block">
                                    Carregar
                                </button>
                                <small class="form-hint">Adiciona 10 itens em cada cédula/moeda</small>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>