<?php
$titulo = "Gerenciar Atendimentos";
require __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Atendimentos</h1>
        
        <div class="d-flex gap-2">
            <button class="btn btn-secondary" onclick="document.getElementById('modal-tipo').style.display='block'">+ Novo Tipo</button>
            <button class="btn btn-success" onclick="abrirFormulario()">Novo Atendimento</button>
        </div>
    </div>

    <div id="modal-tipo" class="modal" style="display:none; background: rgba(0,0,0,0.5); position:fixed; top:0; left:0; width:100%; height:100%; z-index:1100;">
        <div class="modal-dialog">
            <div class="modal-content p-4">
                <h5>Cadastrar Novo Tipo de Atendimento</h5>
                <form id="form-tipo">
                    <div class="mb-3">
                        <label>Descrição do Tipo</label>
                        <input type="text" name="descricao" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-secondary" onclick="document.getElementById('modal-tipo').style.display='none'">Fechar</button>
                        <button type="submit" class="btn btn-primary">Salvar Tipo</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div id="alerta"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Cliente</th>
                        <th>Tipo de atendimento</th>
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

<div id="modal-atendimento" class="modal" style="display:none; background: rgba(0,0,0,0.5); position:fixed; top:0; left:0; width:100%; height:100%; z-index:1050; overflow-y: auto;">
    <div class="modal-dialog">
        <div class="modal-content p-4">
            <h5 id="modal-titulo">Editar Atendimento</h5>
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
                        <input type="date" name="data_atendimento" id="data_atendimento" class="form-control" value="<?= date('Y-m-d') ?>" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Horário</label>
                        <input type="time" name="horario_atendimento" id="horario_atendimento" class="form-control" required>
                    </div>
                </div>
                <div class="mb-3" id="div-status" style="display: none;">
                    <label>Status</label>
                    <select name="status" id="status" class="form-control">
                        <option value="aberto">Aberto</option>
                        <option value="em andamento">Em andamento</option>
                        <option value="concluido">Concluído</option>
                        <option value="cancelado">Cancelado</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label>Descrição</label>
                    <textarea name="descricao" id="descricao" class="form-control" rows="3" required></textarea>
                </div>
                <div class="mb-3">
                    <label>Observação Final</label>
                    <textarea name="observacao_final" id="observacao_final" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharFormulario()">Cancelar</button>
            </form>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('form-atendimento');
    const formTipo = document.getElementById('form-tipo');

    // Cadastro de novo tipo
    formTipo.addEventListener('submit', async (e) => {
        e.preventDefault();
        try {
            await AtendeLabApi.post('tipos_atendimentos', 'criar', new FormData(formTipo));
            alert('Tipo cadastrado com sucesso!');
            document.getElementById('modal-tipo').style.display = 'none';
            formTipo.reset();
            await carregarSelects();
        } catch (e) {
            alert('Erro ao salvar tipo: ' + e.message);
        }
    });

    async function carregarSelects() {
        const [pessoas, tipos] = await Promise.all([
            AtendeLabApi.get('pessoas', 'listar'),
            AtendeLabApi.get('tipos_atendimentos', 'listar')
        ]);
        document.getElementById('pessoa_id').innerHTML = '<option value="">Selecione...</option>' + AtendeLabApi.toList(pessoas).map(p => `<option value="${p.id}">${AtendeLabApi.escape(p.nome)}</option>`).join('');
        document.getElementById('tipo_atendimento_id').innerHTML = '<option value="">Selecione...</option>' + AtendeLabApi.toList(tipos).map(t => `<option value="${t.id}">${AtendeLabApi.escape(t.descricao)}</option>`).join('');
    }

    function getStatusBadge(status) {
        switch(status.toLowerCase()) {
            case 'aberto': return 'bg-primary';
            case 'em andamento': return 'bg-warning text-dark';
            case 'concluido': return 'bg-success';
            case 'cancelado': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }

    async function carregarAtendimentos() {
        const dados = await AtendeLabApi.get('atendimentos', 'listar');
        document.getElementById('tabela-atendimentos').innerHTML = AtendeLabApi.toList(dados).map(a => `
            <tr>
                <td>${a.id}</td>
                <td>${AtendeLabApi.escape(a.pessoa_nome || 'N/A')}</td>
                <td>${AtendeLabApi.escape(a.tipo_servico || 'N/A')}</td>
                <td><span class="badge ${getStatusBadge(a.status)}">${a.status}</span></td>
                <td><button class="btn btn-sm btn-primary" onclick="editarAtendimento(${a.id})">Editar</button></td>
            </tr>
        `).join('');
    }

    async function editarAtendimento(id) {
        try {
            await carregarSelects();
            const dados = await AtendeLabApi.get('atendimentos', 'buscar', { id: id });
            document.getElementById('atendimento-id').value = dados.id;
            document.getElementById('pessoa_id').value = dados.pessoa_id;
            document.getElementById('tipo_atendimento_id').value = dados.tipo_atendimento_id;
            document.getElementById('data_atendimento').value = dados.data_atendimento;
            document.getElementById('horario_atendimento').value = dados.horario_atendimento;
            document.getElementById('descricao').value = dados.descricao;
            document.getElementById('observacao_final').value = dados.observacao_final;
            document.getElementById('status').value = dados.status;
            document.getElementById('div-status').style.display = 'block';
            document.getElementById('modal-atendimento').style.display = 'block';
        } catch (e) {
            alert('Erro ao carregar edição: ' + e.message);
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const acao = formData.get('id') ? 'atualizar' : 'criar';
        try {
            await AtendeLabApi.post('atendimentos', acao, formData);
            fecharFormulario();
            await carregarAtendimentos();
        } catch (e) { alert('Erro: ' + e.message); }
    });

    function abrirFormulario() {
        form.reset();
        document.getElementById('modal-titulo').innerText = 'Novo Atendimento';
        document.getElementById('atendimento-id').value = '';
        document.getElementById('div-status').style.display = 'none';
        carregarSelects();
        document.getElementById('modal-atendimento').style.display = 'block';
    }

    function fecharFormulario() { document.getElementById('modal-atendimento').style.display = 'none'; }

    document.addEventListener('DOMContentLoaded', carregarAtendimentos);
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>