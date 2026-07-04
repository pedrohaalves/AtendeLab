<?php
$titulo = "Gerenciar Tipos de Atendimento";
require __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Tipos de Atendimento</h1>
        <button class="btn btn-success" onclick="document.getElementById('modal-tipo').style.display='block'">+ Novo Tipo</button>
    </div>

    <div id="alerta"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-tipos">
                    <tr><td colspan="3" class="text-center">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-tipo" class="modal" style="display:none; background: rgba(0,0,0,0.5); position:fixed; top:0; left:0; width:100%; height:100%; z-index:1050;">
    <div class="modal-dialog">
        <div class="modal-content p-4">
            <h5 id="modal-titulo">Novo Tipo</h5>
            <form id="form-tipo">
                <input type="hidden" name="id" id="tipo-id">
                <div class="mb-3">
                    <label>Descrição (Obrigatório)</label>
                    <input type="text" name="descricao" id="descricao" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
            </form>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('form-tipo');

    async function carregarTipos() {
        const dados = await AtendeLabApi.get('tipos_atendimentos', 'listar');
        document.getElementById('tabela-tipos').innerHTML = AtendeLabApi.toList(dados).map(t => `
            <tr>
                <td>${t.id}</td>
                <td>${AtendeLabApi.escape(t.descricao)}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editarTipo(${t.id}, '${AtendeLabApi.escape(t.descricao)}')">Editar</button>
                </td>
            </tr>
        `).join('');
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const acao = document.getElementById('tipo-id').value ? 'atualizar' : 'criar';
        try {
            await AtendeLabApi.post('tipos_atendimentos', acao, new FormData(form));
            fecharModal();
            carregarTipos();
            AtendeLabApi.showAlert('alerta', 'Operação realizada com sucesso!');
        } catch (e) {
            AtendeLabApi.showAlert('alerta', e.message, 'danger');
        }
    });

    function editarTipo(id, descricao) {
        document.getElementById('modal-titulo').innerText = 'Editar Tipo';
        document.getElementById('tipo-id').value = id;
        document.getElementById('descricao').value = descricao;
        document.getElementById('modal-tipo').style.display = 'block';
    }

    function fecharModal() {
        form.reset();
        document.getElementById('tipo-id').value = '';
        document.getElementById('modal-tipo').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', carregarTipos);
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>