<?php
$titulo = "Gerenciar Atendimentos";
require __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Atendimentos</h1>
        <button class="btn btn-success" onclick="abrirFormulario()">Novo Atendimento</button>
    </div>

    <div id="alerta"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-atendimentos">
                    <tr><td colspan="5" class="text-center">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-atendimento" class="modal" style="display:none; background: rgba(0,0,0,0.5); position:fixed; top:0; left:0; width:100%; height:100%; z-index:1050;">
    <div class="modal-dialog">
        <div class="modal-content p-4">
            <h5>Registrar Atendimento</h5>
            <form id="form-atendimento">
                <input type="hidden" name="id" id="atendimento-id">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Cliente</label>
                        <select name="pessoa_id" id="pessoa_id" class="form-control" required></select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Tipo</label>
                        <select name="tipo_atendimento_id" id="tipo_atendimento_id" class="form-control" required></select>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Data</label>
                        <input type="date" name="data_atendimento" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Horário</label>
                        <input type="time" name="horario_atendimento" class="form-control">
                    </div>
                </div>

                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" class="form-control" required></textarea>
                </div>
                
                <div class="mb-3">
                    <label>Observação Final</label>
                    <textarea name="observacao_final" class="form-control"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharFormulario()">Cancelar</button>
            </form>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('form-atendimento');

    async function carregarSelects() {
        const [pessoas, tipos] = await Promise.all([
            AtendeLabApi.get('pessoas', 'listar'),
            AtendeLabApi.get('tipos_atendimentos', 'listar')
        ]);

        document.getElementById('pessoa_id').innerHTML = '<option value="">Selecione...</option>' + 
            AtendeLabApi.toList(pessoas).map(p => `<option value="${p.id}">${AtendeLabApi.escape(p.nome)}</option>`).join('');

        document.getElementById('tipo_atendimento_id').innerHTML = '<option value="">Selecione...</option>' + 
            AtendeLabApi.toList(tipos).map(t => `<option value="${t.id}">${AtendeLabApi.escape(t.descricao)}</option>`).join('');
    }

    async function carregarAtendimentos() {
        const dados = await AtendeLabApi.get('atendimentos', 'listar');
        document.getElementById('tabela-atendimentos').innerHTML = AtendeLabApi.toList(dados).map(a => `
            <tr>
                <td>${a.id}</td>
                <td>${AtendeLabApi.escape(a.pessoa_nome || 'N/A')}</td>
                <td>${AtendeLabApi.escape(a.tipo_servico || 'N/A')}</td>
                <td><span class="badge bg-info">${a.status}</span></td>
                <td>
                    <button class="btn btn-sm btn-danger" onclick="inativarAtendimento(${a.id})">Inativar</button>
                </td>
            </tr>
        `).join('');
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await AtendeLabApi.post('atendimentos', 'criar', new FormData(form));
            fecharFormulario();
            await carregarAtendimentos();
        } catch (e) {
            alert('Erro ao salvar: ' + e.message);
        }
    });

    async function inativarAtendimento(id) {
        if (!confirm('Deseja realmente inativar este atendimento?')) return;
        try {
            await AtendeLabApi.post('atendimentos', 'inativar', { id });
            await carregarAtendimentos();
        } catch (e) {
            alert('Erro: ' + e.message);
        }
    }

    function abrirFormulario() { carregarSelects(); document.getElementById('modal-atendimento').style.display = 'block'; }
    function fecharFormulario() { form.reset(); document.getElementById('modal-atendimento').style.display = 'none'; }

    document.addEventListener('DOMContentLoaded', carregarAtendimentos);
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>