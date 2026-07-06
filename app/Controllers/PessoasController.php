<?php

class PessoasController
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
        
        $sql = 'SELECT id, nome, cpf, email, telefone, observacao, perfil, status, criado_em FROM pessoas ORDER BY id DESC';
        $stmt = $this->pdo->query($sql);
        $pessoas = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo json_encode($pessoas, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
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

        $sql = 'SELECT id, nome, cpf, email, telefone, observacao, perfil, status, criado_em FROM pessoas WHERE id=:id';
        $stmt = $this->pdo->prepare($sql);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        $pessoa = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$pessoa) {
            http_response_code(404);
            echo json_encode(['erro' => 'Pessoa não encontrada.']);
            return;
        }
        
        echo json_encode($pessoa, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }

    public function criar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');
        $perfil = trim($_POST['perfil'] ?? 'cliente');
        $criado_em = !empty($_POST['criado_em']) ? $_POST['criado_em'] : date('Y-m-d H:i:s');

        if ($nome === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'O campo nome é obrigatório.']);
            return;
        }

        if (empty($email) && empty($telefone)) {
            http_response_code(400);
            echo json_encode(['erro' => 'É obrigatório preencher pelo menos o E-mail ou o Telefone.']);
            return;
        }

        try {
            $sql = 'INSERT INTO pessoas (nome, cpf, email, telefone, observacao, perfil, criado_em, status) 
                    VALUES (:nome, :cpf, :email, :telefone, :observacao, :perfil, :criado_em, "ativo")';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':observacao', $observacao);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':criado_em', $criado_em);
            $stmt->execute();
            
            http_response_code(201);
            echo json_encode([
                'mensagem' => 'Pessoa cadastrada com sucesso.',
                'id' => $this->pdo->lastInsertId()
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao cadastrar pessoa.']);
        }
    }

    public function atualizar(): void
    {
        header('Content-Type: application/json; charset=utf-8');
        
        $id = isset($_POST['id']) ? filter_var($_POST['id'], FILTER_VALIDATE_INT) : false;
        $nome = trim($_POST['nome'] ?? '');
        $cpf = trim($_POST['cpf'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $telefone = trim($_POST['telefone'] ?? '');
        $observacao = trim($_POST['observacao'] ?? '');
        $perfil = trim($_POST['perfil'] ?? 'cliente');
        $criado_em = !empty($_POST['criado_em']) ? $_POST['criado_em'] : date('Y-m-d H:i:s');

        if (!$id || $nome === '') {
            http_response_code(400);
            echo json_encode(['erro' => 'ID e nome são obrigatórios.']);
            return;
        }

        if (empty($email) && empty($telefone)) {
            http_response_code(400);
            echo json_encode(['erro' => 'É obrigatório preencher pelo menos o E-mail ou o Telefone.']);
            return;
        }

        try {
            $sql = 'UPDATE pessoas 
                    SET nome = :nome, cpf = :cpf, email = :email, telefone = :telefone, observacao = :observacao, perfil = :perfil, criado_em = :criado_em 
                    WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':nome', $nome);
            $stmt->bindValue(':cpf', $cpf);
            $stmt->bindValue(':email', $email);
            $stmt->bindValue(':telefone', $telefone);
            $stmt->bindValue(':observacao', $observacao);
            $stmt->bindValue(':perfil', $perfil);
            $stmt->bindValue(':criado_em', $criado_em);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            echo json_encode(['mensagem' => 'Dados atualizados com sucesso.'], JSON_UNESCAPED_UNICODE);
            
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao atualizar pessoa.']);
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
            $sql = 'UPDATE pessoas SET status = "inativo" WHERE id = :id';
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            echo json_encode(['mensagem' => 'Pessoa inativada com sucesso.'], JSON_UNESCAPED_UNICODE);
        } catch (PDOException $e) {
            http_response_code(500);
            echo json_encode(['erro' => 'Erro ao inativar pessoa.']);
        }
    }
}