<?php
$titulo = "Dashboard";
require __DIR__ . '/../layouts/header.php';
?>

<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Dashboard</h1>
    </div>

    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h5 class="mb-0">Nossa Equipe (Staff Ativo)</h5>
        </div>
        <div class="card-body">
            <div class="row" id="quadro-staff">
                <div class="col-12 text-center text-muted">Carregando equipe...</div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm mb-5">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <h5 class="mb-0">Últimos Atendimentos Registrados</h5>
            <a href="?controller=frontend&action=atendimentos" class="btn btn-sm btn-outline-primary">Ver Todos</a>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4">ID</th>
                            <th>Cliente</th>
                            <th>Tipo de Serviço</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-resumo">
                        <tr><td colspan="4" class="text-center py-4 text-muted">Carregando resumo...</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    
    function getStatusBadge(status) {
        switch(status.toLowerCase()) {
            case 'aberto': return 'bg-primary';
            case 'em andamento': return 'bg-warning text-dark';
            case 'concluido': return 'bg-success';
            case 'cancelado': return 'bg-danger';
            default: return 'bg-secondary';
        }
    }

    async function carregarDashboard() {
        try {
            // Fazemos duas requisições simultâneas para as APIs que já existem!
            const [atendimentos, pessoas] = await Promise.all([
                AtendeLabApi.get('atendimentos', 'listar'),
                AtendeLabApi.get('pessoas', 'listar')
            ]);


            const listaPessoas = AtendeLabApi.toList(pessoas);
            
            // Filtra: Tem que estar 'ativo' E ser 'admin' ou 'atendente'
            const staffAtivo = listaPessoas.filter(p => 
                p.status !== 'inativo' && (p.perfil === 'admin' || p.perfil === 'atendente')
            );

            const containerStaff = document.getElementById('quadro-staff');
            
            if (staffAtivo.length === 0) {
                containerStaff.innerHTML = '<div class="col-12 text-muted text-center py-3">Nenhum membro da equipe ativo no momento.</div>';
            } else {
                containerStaff.innerHTML = staffAtivo.map(s => {
                    let badgeColor = s.perfil === 'admin' ? 'bg-dark' : 'bg-info text-dark';
                    let perfilNome = s.perfil.charAt(0).toUpperCase() + s.perfil.slice(1);
                    
                    return `
                    <div class="col-md-3 mb-3">
                        <div class="card border-0 bg-light h-100">
                            <div class="card-body text-center p-3 d-flex flex-column justify-content-center align-items-center">
                                <!-- Ícone genérico de usuário -->
                                <div class="bg-white rounded-circle d-flex align-items-center justify-content-center shadow-sm mb-2" style="width: 50px; height: 50px;">
                                    <h5 class="m-0 text-secondary">${s.nome.charAt(0).toUpperCase()}</h5>
                                </div>
                                <h6 class="mb-1 text-truncate w-100" title="${AtendeLabApi.escape(s.nome)}">
                                    ${AtendeLabApi.escape(s.nome)}
                                </h6>
                                <span class="badge ${badgeColor}">${perfilNome}</span>
                            </div>
                        </div>
                    </div>`;
                }).join('');
            }

           
            const listaAtendimentos = AtendeLabApi.toList(atendimentos);
            
            const ultimosAtendimentos = listaAtendimentos.slice(0, 5);
            const tabelaResumo = document.getElementById('tabela-resumo');

            if (ultimosAtendimentos.length === 0) {
                tabelaResumo.innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">Nenhum atendimento registrado no sistema.</td></tr>';
            } else {
                tabelaResumo.innerHTML = ultimosAtendimentos.map(a => `
                    <tr>
                        <td class="ps-4 fw-bold text-secondary">#${a.id}</td>
                        <td>${AtendeLabApi.escape(a.pessoa_nome || 'N/A')}</td>
                        <td>${AtendeLabApi.escape(a.tipo_servico || 'N/A')}</td>
                        <td><span class="badge ${getStatusBadge(a.status)}">${a.status}</span></td>
                    </tr>
                `).join('');
            }

        } catch (e) {
            console.error(e);
            document.getElementById('quadro-staff').innerHTML = '<div class="col-12 text-danger text-center">Erro ao carregar a equipe.</div>';
            document.getElementById('tabela-resumo').innerHTML = `<tr><td colspan="4" class="text-center text-danger">Erro ao carregar dados: ${e.message}</td></tr>`;
        }
    }

    // Carrega o painel assim que a página terminar de renderizar
    document.addEventListener('DOMContentLoaded', carregarDashboard);
</script>

<?php require __DIR__ . '/../layouts/footer.php'; ?>