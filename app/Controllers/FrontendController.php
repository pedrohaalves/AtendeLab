<?php

class FrontendController
{
    // Método privado para evitar repetição de código
    private function render(string $view, array $data = []): void
    {
        extract($data);
        require __DIR__ . '/../Views/' . $view . '.php';
    }

    public function dashboard(): void
    {
        $this->render('dashboard/index', ['usuario' => $_SESSION['usuario']]);
    }

    public function pessoas(): void
    {
        $this->render('pessoas/index');
    }

    public function tiposAtendimentos(): void
    {
        $this->render('tipos_atendimentos/index');
    }

    public function atendimentos(): void
    {
        $this->render('atendimentos/index');
    }

    public function usuarios(): void
    {
        // Verifica se o usuário é admin para acessar usuários
        if (($_SESSION['usuario']['perfil'] ?? '') !== 'admin') {
            echo "Acesso negado.";
            return;
        }
        $this->render('usuarios/index');
    }
}