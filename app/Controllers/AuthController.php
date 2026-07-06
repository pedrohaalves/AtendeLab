<?php

// Importa a conexão com o banco de dados e o middleware
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../Middleware/auth.php';

class AuthController
{
    // Armazena a conexão PDO
    private PDO $pdo;

    public function __construct()
    {
        // Recupera a conexão criada em database.php
        global $pdo;
        $this->pdo = $pdo;
    }

    public function exibirLogin(): void
    {
        // Se o usuário já estiver logado, redireciona para o dashboard
        if (usuarioAutenticado()) {
            header('Location: ?controller=auth&action=dashboard');
            exit;
        }
        
        // Recupera mensagens temporárias da sessão
        $erro = $_SESSION['erro_login'] ?? null;
        $mensagem = $_SESSION['mensagem'] ?? null;
        
        // Remove as mensagens para que apareçam somente uma vez
        unset($_SESSION['erro_login'], $_SESSION['mensagem']);
        
        // Carrega a tela de login
        require __DIR__ . '/../Views/auth/login.php';
    }

    public function entrar(): void
    {
        // Permite executar o login somente por requisição POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ?controller=auth&action=login');
            exit;
        }
        
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        if ($email === '' || $senha === '') {
            $_SESSION['erro_login'] = 'Informe o e-mail e a senha.';
            header('Location: ?controller=auth&action=login');
            exit;
        }
        
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $_SESSION['erro_login'] = 'Informe um e-mail válido.';
            header('Location: ?controller=auth&action=login');
            exit;
        }
        
        $sql = 'SELECT id, nome, email, senha, perfil, status FROM usuarios WHERE email = :email LIMIT 1';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$usuario || $usuario['status'] !== 'ativo' || !password_verify($senha, $usuario['senha'])) {
            $_SESSION['erro_login'] = 'E-mail ou senha inválidos.';
            header('Location: ?controller=auth&action=login');
            exit;
        }
        
        session_regenerate_id(true);
        
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'nome' => $usuario['nome'],
            'email' => $usuario['email'],
            'perfil' => $usuario['perfil']
        ];
        
        header('Location: ?controller=auth&action=dashboard');
        exit;
    }

    public function dashboard(): void
    {
        exigirAutenticacao();
        $usuario = usuarioAtual();
        require __DIR__ . '/../Views/dashboard/index.php';
    }

    public function logout(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Limpa todas as variáveis globais da sessão
        $_SESSION = [];

        // Destrói os dados da sessão no servidor
        session_destroy();

        // Inicia uma nova sessão curta apenas para disparar o aviso de sucesso na tela de login
        session_start();
        $_SESSION['mensagem'] = 'Sessão encerrada com sucesso.';

        // Redireciona o utilizador de volta para o formulário de login
        header('Location: ?controller=auth&action=login');
        exit;
    }
}