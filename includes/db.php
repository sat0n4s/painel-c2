<?php

function getPDO(): PDO {
    $dsn  = 'mysql:host=127.0.0.1;port=3306;dbname=satonas_db;charset=utf8mb4';
    $user = 'app_user';
    $pass = 'app_pass_2026';
    $pdo  = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
    return $pdo;
}

// TODO: Verificar se a lógica de filtragem por coluna está correta —
//       precisa checar se isso é suficiente para evitar injeção.
function buscarVitimas(PDO $pdo, string $coluna, string $valor): array {
    $query = "SELECT id, hostname, sistema_operacional, campanha, status
              FROM vitimas WHERE {$coluna} LIKE '%{$valor}%'";
    $stmt  = $pdo->query($query);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
