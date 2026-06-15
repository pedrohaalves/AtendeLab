<?php
// Carrega todos os controllers da aplicação
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

// Define controller e action por query string.
$controller = $_GET['controller'] ?? 'home';
$action = $_GET['action'] ?? 'index';

// -------------------------------------------------------------
// ROTA: USUÁRIOS
// -------------------------------------------------------------
if ($controller == 'usuarios') {
    $usuariosController = new UsuariosController();
    
    switch ($action) {
        case 'listar':
            $usuariosController->listar();
            break;
        case 'buscar':
            $usuariosController->buscarPorId();
            break;
        case 'criar':
            $usuariosController->criar();
            break;
        case 'atualizar':
            $usuariosController->atualizar();
            break;
        case 'excluir':
            $usuariosController->excluir();
            break;
        default:
            echo 'Ação de usuários não encontrada.';
            break;
    }
} 
// -------------------------------------------------------------
// ROTA: PESSOAS
// -------------------------------------------------------------
elseif ($controller == 'pessoas') {
    $pessoasController = new PessoasController();
    
    switch ($action) {
        case 'listar':
            $pessoasController->listar();
            break;
        case 'buscar':
            $pessoasController->buscarPorId();
            break;
        case 'criar':
            $pessoasController->criar();
            break;
        case 'atualizar':
            $pessoasController->atualizar();
            break;
        case 'excluir':
            $pessoasController->excluir();
            break;
        default:
            echo 'Ação de pessoas não encontrada.';
            break;
    }
}
// -------------------------------------------------------------
// ROTA: TIPOS DE ATENDIMENTOS
// -------------------------------------------------------------
elseif ($controller == 'tipos_atendimentos') {
    $tiposController = new TiposAtendimentosController();
    
    switch ($action) {
        case 'listar':
            $tiposController->listar();
            break;
        case 'buscar':
            $tiposController->buscarPorId();
            break;
        case 'criar':
            $tiposController->criar();
            break;
        case 'atualizar':
            $tiposController->atualizar();
            break;
        case 'excluir':
            $tiposController->excluir();
            break;
        default:
            echo 'Ação de tipos de atendimentos não encontrada.';
            break;
    }
}
// -------------------------------------------------------------
// ROTA: ATENDIMENTOS
// -------------------------------------------------------------
elseif ($controller == 'atendimentos') {
    $atendimentosController = new AtendimentosController();
    
    switch ($action) {
        case 'listar':
            $atendimentosController->listar();
            break;
        case 'visualizar':
            $atendimentosController->visualizar();
            break;
        case 'criar':
            $atendimentosController->criar();
            break;
        case 'atualizar_status':
            $atendimentosController->atualizarStatus();
            break;
        default:
            echo 'Ação de atendimentos não encontrada.';
            break;
    }
} 
// -------------------------------------------------------------
// ROTA PADRÃO (HOME / ERRO)
// -------------------------------------------------------------
else {
    // Resposta básica para indicar que a aplicação está no ar.
    echo '<h1>AtendeLab</h1>';
    echo '<p>Projeto em execução. Teste as seguintes rotas no navegador (GET):</p>';
    echo '<ul>';
    echo '<li><a href="?controller=usuarios&action=listar">/usuarios/listar</a></li>';
    echo '<li><a href="?controller=pessoas&action=listar">/pessoas/listar</a></li>';
    echo '<li><a href="?controller=tipos_atendimentos&action=listar">/tipos_atendimentos/listar</a></li>';
    echo '<li><a href="?controller=atendimentos&action=listar">/atendimentos/listar</a></li>';
    echo '</ul>';
}