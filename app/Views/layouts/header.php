<?php
// Garante que a baseUrl esteja disponível para todos os arquivos
$baseUrl = '/atendelab/public/';
?>
<!doctype html>
<html lang="pt-br">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $titulo ?? 'AtendeLab' ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?= $baseUrl ?>assets/css/style.css">
</head>
<body class="bg-light">

    <nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
        <div class="container">
            <a class="navbar-brand" href="<?= $baseUrl ?>?controller=frontend&action=dashboard">AtendeLab</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>?controller=frontend&action=pessoas">Pessoas</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>?controller=frontend&action=tipos_atendimentos">Tipos</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= $baseUrl ?>?controller=frontend&action=atendimentos">Atendimentos</a>
                    </li>
                </ul>
                <div class="d-flex">
                    <a class="btn btn-outline-light btn-sm" href="<?= $baseUrl ?>?controller=auth&action=logout">Sair</a>
                </div>
            </div>
        </div>
    </nav>