<?php
$titulo = "Dashboard";
require __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="card-title mb-3">Resumo dos atendimentos</h5>
            <div id="estatisticas" class="row">
                <div class="col text-center py-4">Carregando resumo...</div>
            </div>
        </div>
    </div>
</div>

<script>
    // Seu script aqui exatamente como você colou antes
    document.addEventListener('DOMContentLoaded', async () => {
        // ... lógica do AtendeLabApi.get ...
    });
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>