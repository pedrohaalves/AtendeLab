<?php

class TiposAtendimentosController
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
        
        $sql = 'SELECT id, descricao, status, criado_em FROM tipos_atendimentos ORDER BY id DESC';
        $stmt = $this->pdo->query($sql);
        $tipos = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($tipos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function buscarPorId(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        $sql = 'SELECT id, descricao, status, criado_em FROM tipos_atendimentos WHERE id=:id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $tipo = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$tipo) {
            http_response_code(404);
            echo json_encode(['erro' => 'Tipo de atendimento não encontrado.']);
            return;
        }
        
        echo json_encode($tipo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $descricao = trim($_POST['descricao'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

        if ($descricao === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'A descrição é obrigatória.']);
            return;
        }

        try {
            $sql = 'INSERT INTO tipos_atendimentos (descricao, status) VALUES (:descricao, :status)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->execute();
            
            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Tipo de atendimento cadastrado com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar tipo de atendimento.']);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : false;
        $descricao = trim($_POST['descricao'] ?? '');
        $status = trim($_POST['status'] ?? 'ativo');

        if (!$id || $descricao === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e descrição são obrigatórios.']);
            return;
        }

        try {
            $sql = 'UPDATE tipos_atendimentos SET descricao = :descricao, status = :status WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':descricao', $descricao);
            $stmt->bindValue(':status', $status);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            echo json_encode(['mensagem' => 'Tipo de atendimento atualizado.'], JSON_UNESCAPED_UNICODE);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar tipo de atendimento.']);
        }
    }

    public function inativar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : false;
        
        if (!$id) {
            http_response_code(400);
            echo json_encode(['erro' => 'ID inválido.']);
            return;
        }

        try {
            $sql = 'UPDATE tipos_atendimentos SET status = "inativo" WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            echo json_encode(['mensagem' => 'Tipo de atendimento inativado com sucesso.'], JSON_UNESCAPED_UNICODE);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar tipo de atendimento.']);
        }
    }
}