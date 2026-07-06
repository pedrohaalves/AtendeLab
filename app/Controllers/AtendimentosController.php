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
        
        $sql = 'SELECT a.id, a.data_atendimento, a.horario_atendimento, a.descricao, a.observacao_final, a.status, 
                       a.pessoa_id, a.tipo_atendimento_id,
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
        $descricao = trim($_POST['descricao'] ?? '');
        $observacao_final = trim($_POST['observacao_final'] ?? '');
        
        $usuario_id = $_SESSION['usuario']['id'] ?? null;
        $status = 'aberto'; 

       
        if (!$usuario_id || !$pessoa_id || !$tipo_atendimento_id || empty($descricao) || empty($observacao_final)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Todos os campos, incluindo a observação final, são obrigatórios.']);
            return;
        }

        try {
            $sql = 'INSERT INTO atendimentos (usuario_id, pessoa_id, tipo_atendimento_id, data_atendimento, horario_atendimento, descricao, observacao_final, status) 
                    VALUES (:usuario_id, :pessoa_id, :tipo_atendimento_id, :data_atendimento, :horario_atendimento, :descricao, :observacao_final, :status)';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':horario_atendimento', $horario_atendimento);
            $stmt->bindValue(':descricao', $descricao);
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

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
        $pessoa_id = filter_input(INPUT_POST, 'pessoa_id', FILTER_VALIDATE_INT);
        $tipo_atendimento_id = filter_input(INPUT_POST, 'tipo_atendimento_id', FILTER_VALIDATE_INT);
        $data_atendimento = $_POST['data_atendimento'] ?? null;
        $horario_atendimento = $_POST['horario_atendimento'] ?? null;
        $descricao = trim($_POST['descricao'] ?? '');
        $observacao_final = trim($_POST['observacao_final'] ?? '');
        $status = $_POST['status'] ?? 'aberto';

        if (!$id || !$pessoa_id || !$tipo_atendimento_id || empty($descricao) || empty($observacao_final)) {
            http_response_code(400);
            echo json_encode(['erro' => 'Todos os campos são obrigatórios para edição.']);
            return;
        }

        try {
            $sql = 'UPDATE atendimentos 
                    SET pessoa_id = :pessoa_id, 
                        tipo_atendimento_id = :tipo_atendimento_id, 
                        data_atendimento = :data_atendimento, 
                        horario_atendimento = :horario_atendimento, 
                        descricao = :descricao, 
                        observacao_final = :observacao_final, 
                        status = :status 
                    WHERE id = :id';
            
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':pessoa_id', $pessoa_id, PDO::PARAM_INT);
            $stmt->bindValue(':tipo_atendimento_id', $tipo_atendimento_id, PDO::PARAM_INT);
            $stmt->bindValue(':data_atendimento', $data_atendimento);
            $stmt->bindValue(':horario_atendimento', $horario_atendimento);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':observacao_final', $observacao_final);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            echo json_encode(['mensagem' => 'Atendimento atualizado com sucesso.']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar: ' . $e->getMessage()]);
        }
    }
    
    public function visualizar(): void
{
    header("Content-Type: application/json; charset=utf-8");
    $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

    if (!$id) {
        http_response_code(400);
        echo json_encode(['erro' => 'ID inválido.']);
        return;
    }

    $sql = 'SELECT * FROM atendimentos WHERE id = :id';
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindValue(':id', $id, PDO::PARAM_INT);
    $stmt->execute();
    $atendimento = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($atendimento ?: ['erro' => 'Não encontrado']);
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
            echo json_encode(['mensagem' => 'Atendimento inativado (cancelado).']);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar.']);
        }
    }
}
