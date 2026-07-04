<?php

class FrontendController
{
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

    // Agora redireciona para a mesma página de atendimentos integrada
    public function tiposAtendimentos(): void
    {
        $this->render('atendimentos/index');
    }

    public function atendimentos(): void
    {
        $this->render('atendimentos/index');
    }

    public function usuarios(): void
    {
        if (($_SESSION['usuario']['perfil'] ?? '') !== 'admin') {
            echo "Acesso negado.";
            return;
        }
        $this->render('usuarios/index');
    }
}