<?php
// Carrega o middleware de autenticação
require_once __DIR__ . '/app/Middleware/auth.php';

// Carrega todos os controllers da aplicação
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';

// Define controller e action por query string (O padrão agora é auth/login)
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

// -------------------------------------------------------------
// ROTA: AUTENTICAÇÃO (LOGIN, LOGOUT E DASHBOARD)
// -------------------------------------------------------------
if ($controller == 'auth') {
    $authController = new AuthController();
    
    switch ($action) {
        case 'login':
            $authController->exibirLogin();
            break;
        case 'entrar':
            $authController->entrar();
            break;
        case 'dashboard':
            $authController->dashboard();
            break;
        case 'logout':
            $authController->logout();
            break;
        default:
            http_response_code(404);
            echo 'Ação de autenticação não encontrada.';
            break;
    }
}
// -------------------------------------------------------------
// ROTA: USUÁRIOS
// -------------------------------------------------------------
elseif ($controller == 'usuarios') {
    exigirAutenticacao(); // Protege a rota exigindo sessão ativa
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
            http_response_code(404);
            echo 'Ação de usuários não encontrada.';
            break;
    }
} 
// -------------------------------------------------------------
// ROTA: PESSOAS
// -------------------------------------------------------------
elseif ($controller == 'pessoas') {
    exigirAutenticacao(); // Protege a rota exigindo sessão ativa
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
            http_response_code(404);
            echo 'Ação de pessoas não encontrada.';
            break;
    }
}
// -------------------------------------------------------------
// ROTA: TIPOS DE ATENDIMENTOS
// -------------------------------------------------------------
elseif ($controller == 'tipos_atendimentos') {
    exigirAutenticacao(); // Protege a rota exigindo sessão ativa
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
            http_response_code(404);
            echo 'Ação de tipos de atendimentos não encontrada.';
            break;
    }
}
// -------------------------------------------------------------
// ROTA: ATENDIMENTOS
// -------------------------------------------------------------
elseif ($controller == 'atendimentos') {
    exigirAutenticacao(); // Protege a rota exigindo sessão ativa
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
            http_response_code(404);
            echo 'Ação de atendimentos não encontrada.';
            break;
    }
} 
// -------------------------------------------------------------
// ROTA PADRÃO (ERRO 404)
// -------------------------------------------------------------
else {
    http_response_code(404);
    echo 'Controller não encontrado.';
}