<?php
$titulo = "Gerenciar Atendimentos";
require __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Atendimentos</h1>
        <div class="d-flex gap-2">
            <button class="btn btn-secondary" onclick="abrirFormularioTipo()">+ Novo Tipo</button>
            <button class="btn btn-success" onclick="abrirFormulario()">Novo Atendimento</button>
        </div>
    </div>

    <div id="alerta"></div>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white">
            <h5 class="mb-0">Lista de Atendimentos</h5>
        </div>
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

    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h5 class="mb-0">Tipos de Atendimentos Cadastrados</h5>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Descrição</th>
                        <th>Cadastrado por</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-tipos">
                    <tr><td colspan="5" class="text-center">Carregando...</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-atendimento" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
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

                <div class="d-flex justify-content-between">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <button type="button" class="btn btn-secondary" onclick="fecharFormulario()">Cancelar</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="modal-tipo" class="modal fade" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content p-4">
            <h5 id="modal-titulo-tipo">Cadastrar Novo Tipo</h5>
            <form id="form-tipo">
                <input type="hidden" name="id" id="tipo-id">
                
                <div class="mb-3">
                    <label>Descrição do Tipo *</label>
                    <input type="text" name="descricao" id="tipo-descricao" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label>Atendente Responsável (Criador)</label>
                    <select name="atendente_id" id="tipo-atendente" class="form-control"></select>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Data de Criação</label>
                        <input type="date" name="data_criacao" id="tipo-data" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Horário de Criação</label>
                        <input type="time" name="hora_criacao" id="tipo-hora" class="form-control">
                    </div>
                </div>
                <small class="text-muted d-block mb-3">Deixe a data e o horário em branco para usar o momento atual.</small>
                
                <div class="d-flex justify-content-between">
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">Salvar</button>
                        <button type="button" class="btn btn-secondary" onclick="fecharFormularioTipo()">Cancelar</button>
                    </div>
                    <button type="button" class="btn btn-danger" id="btn-inativar-tipo" style="display: none;" onclick="inativarTipo()">Inativar Tipo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const formAtendimento = document.getElementById('form-atendimento');
    const formTipo = document.getElementById('form-tipo');
    
    let modalAtendimentoInstancia;
    let modalTipoInstancia;

    document.addEventListener('DOMContentLoaded', () => {
        modalAtendimentoInstancia = new bootstrap.Modal(document.getElementById('modal-atendimento'));
        modalTipoInstancia = new bootstrap.Modal(document.getElementById('modal-tipo'));
        
        carregarAtendimentos();
        carregarTipos();
    });

    // ==========================================
    // LÓGICA DE TIPOS DE ATENDIMENTOS
    // ==========================================
    async function carregarTipos() {
        const [tipos, pessoas] = await Promise.all([
            AtendeLabApi.get('tipos_atendimentos', 'listar'),
            AtendeLabApi.get('pessoas', 'listar')
        ]);
        
        const listaPessoas = AtendeLabApi.toList(pessoas);

        document.getElementById('tabela-tipos').innerHTML = AtendeLabApi.toList(tipos).map(t => {
            let statusClass = t.status === 'inativo' ? 'bg-danger' : 'bg-success';
            let statusText = t.status === 'inativo' ? 'Inativo' : 'Ativo';
            
            // Procura o nome da pessoa na lista de pessoas cadastradas
            let nomeAtendente = '-';
            if(t.atendente_id) {
                const criador = listaPessoas.find(p => p.id == t.atendente_id);
                nomeAtendente = criador ? criador.nome : '-';
            }

            return `
                <tr>
                    <td>${t.id}</td>
                    <td>${AtendeLabApi.escape(t.descricao)}</td>
                    <td>${AtendeLabApi.escape(nomeAtendente)}</td>
                    <td><span class="badge ${statusClass}">${statusText}</span></td>
                    <td>
                        <button class="btn btn-sm btn-primary" onclick="editarTipo(${t.id})">Editar</button>
                    </td>
                </tr>
            `;
        }).join('');
    }

    async function abrirFormularioTipo(tipo = null) {
        // Busca as pessoas cadastradas e FILTRA apenas admin ou atendente
        const pessoas = await AtendeLabApi.get('pessoas', 'listar');
        const atendentes = AtendeLabApi.toList(pessoas).filter(p => p.perfil === 'admin' || p.perfil === 'atendente');
        
        document.getElementById('tipo-atendente').innerHTML = '<option value="">Selecione...</option>' + 
            atendentes.map(a => `<option value="${a.id}">${AtendeLabApi.escape(a.nome)}</option>`).join('');

        if (tipo) {
            document.getElementById('modal-titulo-tipo').innerText = 'Editar Tipo';
            document.getElementById('tipo-id').value = tipo.id;
            document.getElementById('tipo-descricao').value = tipo.descricao;
            document.getElementById('tipo-atendente').value = tipo.atendente_id || '';
            document.getElementById('btn-inativar-tipo').style.display = 'block';

            if(tipo.criado_em) {
                const partes = tipo.criado_em.split(' ');
                if (partes.length === 2) {
                    document.getElementById('tipo-data').value = partes[0]; 
                    document.getElementById('tipo-hora').value = partes[1].substring(0, 5); 
                }
            } else {
                document.getElementById('tipo-data').value = '';
                document.getElementById('tipo-hora').value = '';
            }

        } else {
            formTipo.reset();
            document.getElementById('modal-titulo-tipo').innerText = 'Novo Tipo';
            document.getElementById('tipo-id').value = '';
            document.getElementById('tipo-atendente').value = '';
            document.getElementById('tipo-data').value = '';
            document.getElementById('tipo-hora').value = '';
            document.getElementById('btn-inativar-tipo').style.display = 'none';
        }
        modalTipoInstancia.show();
    }

    async function editarTipo(id) {
        try {
            const tipo = await AtendeLabApi.get('tipos_atendimentos', 'buscar', { id: id });
            abrirFormularioTipo(tipo);
        } catch (e) {
            AtendeLabApi.showAlert('alerta', 'Erro ao buscar tipo: ' + e.message, 'danger');
        }
    }

    async function inativarTipo() {
        const id = document.getElementById('tipo-id').value;
        if (!id) return;
        if (!confirm('Deseja realmente inativar este tipo de atendimento?')) return;

        try {
            await AtendeLabApi.post('tipos_atendimentos', 'inativar', { id: id });
            fecharFormularioTipo();
            AtendeLabApi.showAlert('alerta', 'Tipo inativado com sucesso!', 'success');
            await carregarTipos();
        } catch (e) {
            alert('Erro: ' + e.message);
        }
    }

    formTipo.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formTipo);
        
        const dataStr = document.getElementById('tipo-data').value;
        const horaStr = document.getElementById('tipo-hora').value;
        if (dataStr && horaStr) {
            formData.append('criado_em', `${dataStr} ${horaStr}:00`);
        }

        const acao = formData.get('id') ? 'atualizar' : 'criar';

        try {
            await AtendeLabApi.post('tipos_atendimentos', acao, formData);
            fecharFormularioTipo();
            AtendeLabApi.showAlert('alerta', 'Tipo salvo com sucesso!', 'success');
            await carregarTipos(); 
            await carregarSelects(); 
        } catch (e) {
            alert('Erro ao salvar tipo: ' + e.message);
        }
    });

    function fecharFormularioTipo() {
        formTipo.reset();
        document.getElementById('tipo-id').value = '';
        modalTipoInstancia.hide();
    }

    // ==========================================
    // LÓGICA DE ATENDIMENTOS
    // ==========================================
    async function carregarSelects() {
        const [pessoas, tipos] = await Promise.all([
            AtendeLabApi.get('pessoas', 'listar'),
            AtendeLabApi.get('tipos_atendimentos', 'listar')
        ]);

        document.getElementById('pessoa_id').innerHTML = '<option value="">Selecione...</option>' + 
            AtendeLabApi.toList(pessoas).map(p => `<option value="${p.id}">${AtendeLabApi.escape(p.nome)}</option>`).join('');

        const tiposAtivos = AtendeLabApi.toList(tipos).filter(t => t.status !== 'inativo');
        document.getElementById('tipo_atendimento_id').innerHTML = '<option value="">Selecione...</option>' + 
            tiposAtivos.map(t => `<option value="${t.id}">${AtendeLabApi.escape(t.descricao)}</option>`).join('');
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
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editarAtendimento(${a.id})">Editar</button>
                </td>
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
            document.getElementById('modal-titulo').innerText = 'Editar Atendimento';
            
            modalAtendimentoInstancia.show();
        } catch (e) {
            alert('Erro ao carregar edição: ' + e.message);
        }
    }

    formAtendimento.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(formAtendimento);
        const acao = formData.get('id') ? 'atualizar' : 'criar';

        try {
            await AtendeLabApi.post('atendimentos', acao, formData);
            fecharFormulario();
            AtendeLabApi.showAlert('alerta', 'Atendimento salvo com sucesso!', 'success');
            await carregarAtendimentos();
        } catch (e) { 
            alert('Erro: ' + e.message); 
        }
    });

    function abrirFormulario() {
        formAtendimento.reset();
        document.getElementById('modal-titulo').innerText = 'Novo Atendimento';
        document.getElementById('atendimento-id').value = '';
        document.getElementById('div-status').style.display = 'none';
        carregarSelects();
        modalAtendimentoInstancia.show();
    }

    function fecharFormulario() { 
        formAtendimento.reset();
        document.getElementById('atendimento-id').value = '';
        modalAtendimentoInstancia.hide(); 
    }
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>