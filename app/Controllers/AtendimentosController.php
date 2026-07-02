<?php

class AtendimentosController
{
    private PDO $pdo;

    public function __construct()
    {
        require __DIR__ . '/../../config/database.php';
        $this->pdo = $pdo;
    }

    public function listar(): void
    {
        header("Content-Type: application/json; charset=utf-8");
        
        // Mude de 'a.observacoes' para 'a.descricao'
        $sql = 'SELECT a.id, a.data_atendimento, a.descricao, a.status, 
                       u.nome AS atendente_nome, 
                       p.nome AS pessoa_nome, 
                       t.descricao AS tipo_servico
                FROM atendimentos a
                JOIN usuarios u ON a.usuario_id = u.id
                JOIN pessoas p ON a.pessoa_id = p.id
                JOIN tipos_atendimentos t ON a.tipo_atendimento_id = t.id
                ORDER BY a.id DESC';
        
        $stmt = $this->pdo->query($sql);
        $atendimentos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($atendimentos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

 public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $data_atendimento = $_POST['data_atendimento'] ?? date('Y-m-d');
        $horario_atendimento = $_POST['horario_atendimento'] ?? null;
        $descricao = trim($_POST['descricao'] ?? ''); // Use 'descricao' aqui
        $observacao_final = trim($_POST['observacao_final'] ?? '');
        
        $usuario_id = $_SESSION['usuario']['id'] ?? null;
        $status = 'aberto'; 

        if (!$usuario_id || !$pessoa_id || !$tipo_atendimento_id || empty($descricao)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Dados obrigatórios ausentes.']);
            return;
        }

        try {
            // Alterado de 'observacoes' para 'descricao' abaixo:
            $sql = 'INSERT INTO atendimentos (usuario_id, pessoa_id, tipo_atendimento_id, data_atendimento, horario_atendimento, descricao, observacao_final, status) 
                    VALUES (:usuario_id, :pessoa_id, :tipo_atendimento_id, :data_atendimento, :horario_atendimento, :descricao, :observacao_final, :status)';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':horario_atendimento', $horario_atendimento);
            $stmt->bindValue(':descricao', $descricao); // Nome do bind igual ao nome da coluna
            $stmt->bindValue(':observacao_final', $observacao_final);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
            
            http_response_code(201);
            echo json_encode(['mensagem' => 'Atendimento registrado com sucesso.']);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao registrar: ' . $e->getMessage()]);
        }
    }

    public function inativar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID obrigatório.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos SET status = "cancelado" WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['mensagem' => 'Atendimento inativado.']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar.']);
        }
    }
}