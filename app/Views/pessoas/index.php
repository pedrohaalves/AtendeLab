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
                        <th>CPF</th>
                        <th>Contato (E-mail/Tel)</th>
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

<div id="modal-pessoa" class="modal" style="display:none; background: rgba(0,0,0,0.5); position:fixed; top:0; left:0; width:100%; height:100%; z-index: 1050; overflow-y: auto;">
    <div class="modal-dialog modal-lg">
        <div class="modal-content p-4">
            <h5 id="modal-titulo" class="mb-4">Cadastrar Pessoa</h5>
            <div id="alerta-modal"></div>
            <form id="form-pessoa">
                <input type="hidden" name="id" id="pessoa-id">
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Nome *</label>
                        <input type="text" name="nome" id="pessoa-nome" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Documento (CPF)</label>
                        <input type="text" name="cpf" id="pessoa-cpf" class="form-control" placeholder="000.000.000-00" maxlength="14" oninput="mascaraCPF(this)">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>E-mail</label>
                        <input type="email" name="email" id="pessoa-email" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Telefone</label>
                        <input type="text" name="telefone" id="pessoa-telefone" class="form-control" placeholder="(00) 00000 0000" maxlength="15" oninput="mascaraTelefone(this)">
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label>Data de Criação</label>
                        <input type="date" name="data_criacao" id="pessoa-data" class="form-control">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label>Horário de Criação</label>
                        <input type="time" name="hora_criacao" id="pessoa-hora" class="form-control">
                    </div>
                </div>
                <small class="text-muted d-block mb-3">Deixe a data e o horário em branco para usar o momento atual.</small>

                <div class="mb-4">
                    <label>Observação</label>
                    <textarea name="observacao" id="pessoa-observacao" class="form-control" rows="3"></textarea>
                </div>

                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Salvar</button>
                    <button type="button" class="btn btn-secondary" onclick="fecharFormulario()">Cancelar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const form = document.getElementById('form-pessoa');

    // Máscaras de Input
    function mascaraCPF(i) {
        let v = i.value.replace(/\D/g, "");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        i.value = v;
    }

    function mascaraTelefone(i) {
        let v = i.value.replace(/\D/g, "");
        v = v.replace(/^(\d{2})(\d)/g, "($1) $2");
        v = v.replace(/(\d{5})(\d)/, "$1 $2");
        i.value = v;
    }

    // Carregar a Tabela
    async function carregarPessoas() {
        const dados = await AtendeLabApi.get('pessoas', 'listar');
        const tbody = document.getElementById('tabela-pessoas');
        
        tbody.innerHTML = AtendeLabApi.toList(dados).map(p => {
            // Exibe o email, se não tiver exibe o telefone
            let contato = p.email ? p.email : (p.telefone ? p.telefone : '<span class="text-muted">Sem contato</span>');
            
            // Define o visual do Status
            let statusClass = p.status === 'inativo' ? 'bg-danger' : 'bg-success';
            let statusText = p.status === 'inativo' ? 'Inativo' : 'Ativo';
            
            return `
            <tr>
                <td>${p.id}</td>
                <td>${AtendeLabApi.escape(p.nome)}</td>
                <td>${p.cpf ? p.cpf : '-'}</td>
                <td>${contato}</td>
                <td><span class="badge ${statusClass}">${statusText}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="editarPessoa(${p.id})">Editar</button>
                </td>
            </tr>
            `;
        }).join('');
    }

   async function editarPessoa(id) {
    try {
        // Ação alterada para buscarPorId para bater com a rota
        const pessoa = await AtendeLabApi.get('pessoas', 'buscarPorId', { id: id });
        
        // Ajustado para o ID correto do seu input hidden (pessoa-id)
        document.getElementById('pessoa-id').value = pessoa.id; 
        document.getElementById('pessoa-nome').value = pessoa.nome;
        document.getElementById('pessoa-cpf').value = pessoa.cpf || '';
        document.getElementById('pessoa-email').value = pessoa.email || '';
        document.getElementById('pessoa-telefone').value = pessoa.telefone || '';
        document.getElementById('pessoa-observacao').value = pessoa.observacao || '';
        
        // O restante da função segue igual...
        const modal = new bootstrap.Modal(document.getElementById('modal-pessoa'));
        modal.show();
    } catch (error) {
        AtendeLabApi.showAlert('alerta', error.message, 'danger');
    }
}

    // Submeter o Formulário
    form.addEventListener('submit', async (e) => {
        e.preventDefault();
        
        const email = document.getElementById('pessoa-email').value.trim();
        const telefone = document.getElementById('pessoa-telefone').value.trim();
        const cpf = document.getElementById('pessoa-cpf').value.trim();

        if (!email && !telefone) {
            AtendeLabApi.showAlert('alerta-modal', 'Você deve preencher o E-mail ou o Telefone.', 'warning');
            return;
        }

        if (cpf && cpf.length !== 14) {
            AtendeLabApi.showAlert('alerta-modal', 'O CPF deve estar no formato 000.000.000-00', 'warning');
            return;
        }

        const formData = new FormData(form);
        
        // Junta a data e a hora novamente se ambas foram preenchidas para mandar pro banco
        const dataStr = document.getElementById('pessoa-data').value;
        const horaStr = document.getElementById('pessoa-hora').value;
        if (dataStr && horaStr) {
            formData.append('criado_em', `${dataStr} ${horaStr}:00`);
        }

        const action = formData.get('id') ? 'atualizar' : 'criar';
        
        try {
            await AtendeLabApi.post('pessoas', action, formData);
            fecharFormulario();
            AtendeLabApi.showAlert('alerta', 'Dados salvos com sucesso!', 'success');
            await carregarPessoas();
        } catch (e) {
            AtendeLabApi.showAlert('alerta-modal', e.message, 'danger');
        }
    });

    // Controle do Modal
    function abrirFormulario(pessoa = null) {
        document.getElementById('alerta-modal').innerHTML = ''; 
        document.getElementById('modal-pessoa').style.display = 'block';
        
        if (pessoa) {
            document.getElementById('modal-titulo').innerText = 'Editar Pessoa';
            document.getElementById('pessoa-id').value = pessoa.id;
            document.getElementById('pessoa-nome').value = pessoa.nome;
            document.getElementById('pessoa-cpf').value = pessoa.cpf || '';
            document.getElementById('pessoa-email').value = pessoa.email || '';
            document.getElementById('pessoa-telefone').value = pessoa.telefone || '';
            document.getElementById('pessoa-observacao').value = pessoa.observacao || '';
            
            // Separando "YYYY-MM-DD HH:MM:SS" em Data e Hora
            if(pessoa.criado_em) {
                const partes = pessoa.criado_em.split(' ');
                if (partes.length === 2) {
                    document.getElementById('pessoa-data').value = partes[0]; // YYYY-MM-DD
                    document.getElementById('pessoa-hora').value = partes[1].substring(0, 5); // HH:MM
                }
            }
        } else {
            document.getElementById('modal-titulo').innerText = 'Cadastrar Pessoa';
        }
    }

    function fecharFormulario() {
        form.reset();
        document.getElementById('pessoa-id').value = '';
        document.getElementById('modal-pessoa').style.display = 'none';
    }

    document.addEventListener('DOMContentLoaded', carregarPessoas);
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>