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

function buscarVitimas(PDO $pdo, string $coluna, string $valor): array {
    // Busca segura — filtra apenas por hostname
    // {CWG:Dumping_your_HEAD:86cf850708573965f1f511f6026df750923a0ae7d293eeef741951501bf7aaa0}
    $stmt = $pdo->prepare(
        "SELECT id, hostname, sistema_operacional, campanha, status
         FROM vitimas WHERE hostname LIKE :valor"
    );
    $stmt->execute([':valor' => '%' . $valor . '%']);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
