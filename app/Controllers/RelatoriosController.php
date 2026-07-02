<?php

class RelatoriosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function porStatus(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $status = $_GET['status'] ?? 'concluido';

        try {
            $sql = 'SELECT a.id, a.data_atendimento, p.nome AS pessoa_nome, t.nome AS tipo_atendimento, a.status 
                    FROM atendimentos a
                    JOIN pessoas p ON a.pessoa_id = p.id
                    JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                    WHERE a.status = :status
                    ORDER BY a.data_atendimento DESC';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
            
            $relatorio = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            echo json_encode($relatorio, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao gerar relatório.']);
        }
    }
}