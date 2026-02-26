<?php
$page = $_GET['page'] ?? 'home';

function renderContent($page) {
    switch ($page) {

        case 'quem-sou':
            return '
            <h2>Quem Sou</h2>
            <p><strong>Juliano Sato</strong> é palestrante e consultor em Segurança da Informação.</p>
            <p>Especialista em <strong>Code Review</strong>, atua ajudando empresas a identificar vulnerabilidades,
            melhorar qualidade de código e elevar o nível técnico de equipes de desenvolvimento.</p>
            <p>Reconhecido por sua didática clara e objetiva, também é conhecido por gostar muito de conversar —
            seja em eventos, mentorias ou discussões técnicas aprofundadas.</p>
            ';

        case 'code-review':
            return '
            <h2>Code Review</h2>
            <p>Juliano é especialista em análise de código com foco em:</p>
            <ul>
                <li>Identificação de vulnerabilidades</li>
                <li>Revisão de arquitetura segura</li>
                <li>Boas práticas de desenvolvimento</li>
                <li>OWASP e padrões de segurança</li>
            </ul>
            <p>Seu trabalho vai além de apontar falhas — ele orienta equipes sobre como
            evoluir maturidade técnica e criar cultura de segurança.</p>
            ';

        case 'aulas':
            return '
            <h2>Aulas e Palestras</h2>
            <p>Como palestrante, Juliano aborda temas como:</p>
            <ul>
                <li>Secure Coding</li>
                <li>Threat Modeling</li>
                <li>Code Review Avançado</li>
                <li>Segurança para Desenvolvedores</li>
            </ul>
            <p>Suas aulas são técnicas, práticas e dinâmicas — e sempre com bastante interação
            e troca de ideias.</p>
            ';

        case 'contato':
            return '
            <h2>Contato</h2>
            <p>Interessado em consultoria, palestra ou treinamento?</p>
            <p>Email: contato@julianosato.com</p>
            <p>LinkedIn: linkedin.com/in/julianosato</p>
            <p>Juliano está sempre aberto para conversar sobre tecnologia, segurança e código.</p>
            ';

        default:
            return '
            <h2>Bem-vindo</h2>
            <p>Este é o site oficial de <strong>Juliano Sato</strong>,
            consultor e palestrante em Segurança da Informação.</p>
            <p>Navegue pelo menu para conhecer mais sobre seu trabalho.</p>
            ';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Juliano Sato | Segurança da Informação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            background: #f4f4f4;
        }
        header {
            background: #111;
            color: white;
            padding: 20px;
            text-align: center;
        }
        nav {
            background: #222;
            padding: 10px;
            text-align: center;
        }
        nav a {
            color: white;
            margin: 0 15px;
            text-decoration: none;
            font-weight: bold;
        }
        nav a:hover {
            color: #00c3ff;
        }
        main {
            padding: 40px;
            max-width: 900px;
            margin: auto;
            background: white;
            min-height: 400px;
        }
        footer {
            text-align: center;
            padding: 15px;
            background: #111;
            color: white;
        }
    </style>
</head>
<body>

<header>
    <h1>Juliano Sato</h1>
    <p>Consultor em Segurança da Informação | Especialista em Code Review | Palestrante</p>
</header>

<nav>
    <a href="?page=home">Home</a>
    <a href="?page=quem-sou">Quem Sou</a>
    <a href="?page=code-review">Code Review</a>
    <a href="?page=aulas">Aulas</a>
    <a href="?page=contato">Contato</a>
</nav>

<main>
    <?php echo renderContent($page); ?>
</main>

<footer>
    © <?php echo date("Y"); ?> Juliano Sato - Todos os direitos reservados
</footer>

</body>
</html>
