<?php
// Carrega o middleware de autenticação
require_once __DIR__ . '/app/Middleware/auth.php';

// Carrega todos os controllers da aplicação
require_once __DIR__ . '/app/Controllers/AuthController.php';
require_once __DIR__ . '/app/Controllers/UsuariosController.php';
require_once __DIR__ . '/app/Controllers/PessoasController.php';
require_once __DIR__ . '/app/Controllers/TiposAtendimentosController.php';
require_once __DIR__ . '/app/Controllers/AtendimentosController.php';
require_once __DIR__ . '/app/Controllers/DashboardController.php';
require_once __DIR__ . '/app/Controllers/FrontendController.php';
// Define controller e action por query string
$controller = $_GET['controller'] ?? 'auth';
$action = $_GET['action'] ?? 'login';

// -------------------------------------------------------------
// ROTA: AUTENTICAÇÃO
// -------------------------------------------------------------
if ($controller == 'auth') {
    $authController = new AuthController();
    
    switch ($action) {
        case 'login': $authController->exibirLogin(); break;
        case 'entrar': $authController->entrar(); break;
        case 'dashboard': $authController->dashboard(); break;
        case 'logout': $authController->logout(); break;
        default:
            http_response_code(404);
            echo 'Ação de autenticação não encontrada.';
            break;
    }
}
// -------------------------------------------------------------
// ROTA: DASHBOARD (API de Estatísticas)
// -------------------------------------------------------------
elseif ($controller == 'dashboard') {
    exigirAutenticacao();
    $dashboardController = new DashboardController();
    
    switch ($action) {
        case 'estatisticas': 
            $dashboardController->estatisticas(); 
            break;
        default:
            http_response_code(404);
            echo 'Ação de dashboard não encontrada.';
            break;
    }
}
// -------------------------------------------------------------
// ROTA: USUÁRIOS
// -------------------------------------------------------------
elseif ($controller == 'usuarios') {
    exigirAutenticacao();
    $usuariosController = new UsuariosController();
    
    switch ($action) {
        case 'listar': $usuariosController->listar(); break;
        case 'buscar': $usuariosController->buscarPorId(); break;
        case 'criar': $usuariosController->criar(); break;
        case 'atualizar': $usuariosController->atualizar(); break;
        case 'inativar': $usuariosController->excluir(); break; // Mantendo o método excluir, mas rota inativar
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
    exigirAutenticacao();
    $pessoasController = new PessoasController();
    
    switch ($action) {
        case 'listar': $pessoasController->listar(); break;
        case 'buscarPorId': $pessoasController->buscarPorId(); break; 
        case 'criar': $pessoasController->criar(); break;
        case 'atualizar': $pessoasController->atualizar(); break;
        case 'inativar': $pessoasController->inativar(); break;
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
    exigirAutenticacao();
    $tiposController = new TiposAtendimentosController();
    
    switch ($action) {
        case 'listar': $tiposController->listar(); break;
        case 'buscar': $tiposController->buscarPorId(); break;
        case 'criar': $tiposController->criar(); break;
        case 'atualizar': $tiposController->atualizar(); break;
        case 'inativar': $tiposController->excluir(); break; // Alterado de excluir para inativar
        default:
            http_response_code(404);
            echo 'Ação de tipos de atendimentos não encontrada.';
            break;
    }
}
elseif ($controller == 'atendimentos') {
    exigirAutenticacao();
    $atendimentosController = new AtendimentosController();
    
    switch ($action) {
        case 'listar':    $atendimentosController->listar(); break;
        case 'buscar':    $atendimentosController->visualizar(); break; // Adicione esta linha
        case 'criar':     $atendimentosController->criar(); break;
        case 'atualizar': $atendimentosController->atualizar(); break;
        case 'inativar':  $atendimentosController->inativar(); break;
        default: /* ... */ }
}
// -------------------------------------------------------------
// ROTA: FRONTEND (Renderização de Views)
// -------------------------------------------------------------
elseif ($controller == 'frontend') {
    exigirAutenticacao();
    
    // Certifique-se de que o FrontendController já foi carregado no topo
    require_once __DIR__ . '/app/Controllers/FrontendController.php';
    $frontendController = new FrontendController();
    
    switch ($action) {
        case 'dashboard':          $frontendController->dashboard();          break;
        case 'pessoas':            $frontendController->pessoas();            break;
        case 'tipos_atendimentos': $frontendController->tiposAtendimentos(); break;
        case 'atendimentos':       $frontendController->atendimentos();       break;
        case 'usuarios':           $frontendController->usuarios();           break;
        default:
            http_response_code(404);
            echo 'Página não encontrada.';
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