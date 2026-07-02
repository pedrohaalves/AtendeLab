<?php

class DashboardController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function estatisticas(): void
    {
        header('Content-Type: application/json; charset=utf-8');

        try {
            // Conta totais por status
            $sql = "SELECT status, COUNT(*) as total FROM atendimentos GROUP BY status";
            $stmt = $this->pdo->query($sql);
            $dados = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Conta total geral
            $totalGeral = $this->pdo->query("SELECT COUNT(*) FROM atendimentos")->fetchColumn();

            echo json_encode([
                'total_geral' => (int)$totalGeral,
                'detalhado' => $dados
            ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao carregar estatísticas do dashboard.']);
        }
    }
}