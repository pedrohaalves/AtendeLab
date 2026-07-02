<?php
$titulo = "Gerenciar Pessoas";
require __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Pessoas</h1>
        <button class="btn btn-success" onclick="abrirFormulario()">Nova Pessoa</button>
    </div>

    <div id="alerta"></div>

    <div class="card shadow-sm">
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody id="tabela-pessoas">
                    </tbody>
            </table>
        </div>
    </div>
</div>

<div id="modal-pessoa" class="modal" style="display:none; background: rgba(0,0,0,0.5); position:fixed; top:0; left:0; width:100%; height:100%;">
    <div class="modal-dialog">
        <div class="modal-content p-4">
            <h5 id="modal-titulo">Cadastrar Pessoa</h5>
            <form id="form-pessoa">
                <input type="hidden" name="id" id="pessoa-id">
                <div class="mb-3">
                    <label>Nome</label>
                    <input type="text" name="nome" id="pessoa-nome" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label>E-mail</label>
                    <input type="email" name="email" id="pessoa-email" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Salvar</button>
                <button type="button" class="btn btn-secondary" onclick="fecharFormulario()">Cancelar</button>
            </form>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('form-pessoa');

    async function carregarPessoas() {
        const dados = await AtendeLabApi.get('pessoas', 'listar');
        const tbody = document.getElementById('tabela-pessoas');
        tbody.innerHTML = AtendeLabApi.toList(dados).map(p => `
            <tr>
                <td>${p.id}</td>
                <td>${AtendeLabApi.escape(p.nome)}</td>
                <td>${AtendeLabApi.escape(p.email)}</td>
                <td>${p.status}</td>
                <td>
                    <button class="btn btn-sm btn-warning" onclick="editarPessoa(${p.id})">Editar</button>
                    <button class="btn btn-sm btn-danger" onclick="inativarPessoa(${p.id})">Inativar</button>
                </td>
            </tr>
        `).join('');
    }

    async function inativarPessoa(id) {
        if (!confirm('Deseja realmente inativar?')) return;
        try {
            await AtendeLabApi.post('pessoas', 'inativar', { id });
            AtendeLabApi.showAlert('alerta', 'Pessoa inativada!', 'success');
            await carregarPessoas();
        } catch (e) {
            AtendeLabApi.showAlert('alerta', e.message, 'danger');
        }
    }

    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        const formData = new FormData(form);
        const action = formData.get('id') ? 'atualizar' : 'criar';
        
        try {
            await AtendeLabApi.post('pessoas', action, formData);
            fecharFormulario();
            await carregarPessoas();
        } catch (e) {
            alert(e.message);
        }
    });

    function abrirFormulario(pessoa = null) {
        document.getElementById('modal-pessoa').style.display = 'block';
        if (pessoa) {
            document.getElementById('pessoa-id').value = pessoa.id;
            document.getElementById('pessoa-nome').value = pessoa.nome;
            document.getElementById('pessoa-email').value = pessoa.email;
        }
    }

    function fecharFormulario() {
        form.reset();
        document.getElementById('modal-pessoa').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', carregarPessoas);
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>