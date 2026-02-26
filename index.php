<?php
require_once __DIR__ . '/includes/db.php';

$page   = $_GET['page'] ?? 'dashboard';
$busca  = $_POST['busca']  ?? '';
$coluna = $_POST['coluna'] ?? 'hostname';
$resultados = [];
$erro       = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $busca !== '') {
    try {
        $pdo = getPDO();
        $resultados = buscarVitimas($pdo, $coluna, $busca);
    } catch (Exception $e) {
        // silently fail
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>SatΩnas C2 :: Painel de Controle</title>
  <style>
    *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }
    :root {
      --bg:     #0a0a0a;
      --bg2:    #111111;
      --bg3:    #1a1a1a;
      --red:    #8b0000;
      --red2:   #cc0000;
      --green:  #00ff41;
      --green2: #00cc33;
      --text:   #c8c8c8;
      --text2:  #777;
      --border: #222;
    }
    html { scroll-behavior: smooth; }
    body {
      font-family: 'Courier New', Courier, monospace;
      background: var(--bg);
      color: var(--text);
      min-height: 100vh;
    }

    /* ── HERO ── */
    .hero {
      position: relative;
      background: var(--bg);
      overflow: hidden;
      padding: 50px 0;
      border-bottom: 1px solid var(--border);
    }
    .hero::before {
      content: '';
      position: absolute;
      inset: 0;
      background: url('../img/digital-matrix-code-tunnel-u3sfspoxuiv61onl.webp') center/cover no-repeat;
      opacity: 0.08;
    }
    .hero-inner { position: relative; z-index: 1; display: flex; align-items: center; gap: 40px; max-width: 1100px; margin: 0 auto; padding: 0 40px; }
    .hero-logo {
      flex-shrink: 0;
    }
    .hero-logo img {
      width: 240px;
      height: 270px;
      object-fit: cover;
      border-radius: 12px;
      border: 2px solid var(--red);
      filter: saturate(0.5) brightness(0.8);
      display: block;
    }
    .hero-content { flex: 1; text-align: center; }
    .hero h1 {
      font-size: 42px;
      font-weight: 900;
      letter-spacing: 4px;
      color: var(--red2);
      text-shadow: 0 0 20px rgba(204,0,0,0.4);
    }
    .hero h1 span { color: var(--green); }
    .hero-sub {
      font-size: 13px;
      letter-spacing: 3px;
      color: var(--text2);
      text-transform: uppercase;
      margin-top: 8px;
    }
    .hero-stats {
      display: flex;
      justify-content: center;
      gap: 40px;
      margin-top: 36px;
      flex-wrap: wrap;
    }
    .stat-box {
      text-align: center;
      padding: 14px 28px;
      border: 1px solid var(--border);
      background: var(--bg2);
      border-radius: 6px;
    }
    .stat-num { font-size: 28px; font-weight: 900; color: var(--red2); }
    .stat-lbl { font-size: 11px; letter-spacing: 2px; color: var(--text2); margin-top: 4px; text-transform: uppercase; }

    /* ── LAYOUT ── */
    .wrapper { display: flex; min-height: calc(100vh - 250px); }

    /* ── SIDEBAR ── */
    .sidebar {
      width: 220px;
      flex-shrink: 0;
      background: var(--bg2);
      border-right: 1px solid var(--border);
      padding: 28px 0;
    }
    .sidebar-label {
      font-size: 10px;
      letter-spacing: 2px;
      color: var(--text2);
      text-transform: uppercase;
      padding: 0 22px;
      margin-bottom: 10px;
      margin-top: 20px;
    }
    .sidebar-label:first-child { margin-top: 0; }
    .sidebar a {
      display: block;
      padding: 10px 22px;
      color: var(--text);
      text-decoration: none;
      font-size: 13px;
      transition: background 0.15s, color 0.15s;
      border-left: 3px solid transparent;
    }
    .sidebar a:hover, .sidebar a.active {
      background: var(--bg3);
      color: var(--green);
      border-left-color: var(--red2);
    }
    .sidebar a .ico { margin-right: 8px; }

    /* ── MAIN ── */
    .main { flex: 1; padding: 36px 40px; }
    .page-title {
      font-size: 11px;
      letter-spacing: 3px;
      color: var(--text2);
      text-transform: uppercase;
      margin-bottom: 6px;
    }
    .main h2 {
      font-size: 24px;
      color: var(--green);
      margin-bottom: 28px;
      border-bottom: 1px solid var(--border);
      padding-bottom: 14px;
    }

    /* ── CARDS DASHBOARD ── */
    .dash-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 36px; }
    .dash-card {
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 22px;
    }
    .dash-card .d-val { font-size: 32px; font-weight: 900; color: var(--red2); }
    .dash-card .d-lbl { font-size: 11px; color: var(--text2); letter-spacing: 1px; margin-top: 6px; }
    .dash-card .d-trend { font-size: 12px; color: var(--green); margin-top: 4px; }

    /* ── FEED ── */
    .feed { background: var(--bg2); border: 1px solid var(--border); border-radius: 8px; padding: 24px; }
    .feed h3 { font-size: 13px; letter-spacing: 2px; color: var(--text2); text-transform: uppercase; margin-bottom: 16px; }
    .feed-item {
      display: flex;
      align-items: flex-start;
      gap: 12px;
      padding: 10px 0;
      border-bottom: 1px solid var(--border);
      font-size: 13px;
    }
    .feed-item:last-child { border-bottom: none; }
    .feed-dot { width: 8px; height: 8px; border-radius: 50%; background: var(--red2); flex-shrink: 0; margin-top: 4px; }
    .feed-dot.green { background: var(--green); }
    .feed-time { color: var(--text2); font-size: 11px; min-width: 90px; }
    .feed-msg { color: var(--text); }

    /* ── BUSCA ── */
    .search-box {
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 8px;
      padding: 28px;
      margin-bottom: 30px;
    }
    .search-box h3 { font-size: 13px; letter-spacing: 2px; color: var(--text2); text-transform: uppercase; margin-bottom: 20px; }
    .form-row { display: flex; gap: 14px; flex-wrap: wrap; align-items: flex-end; }
    .form-group { display: flex; flex-direction: column; gap: 6px; flex: 1; min-width: 180px; }
    .form-group label { font-size: 11px; color: var(--text2); letter-spacing: 1px; text-transform: uppercase; }
    .form-group input, .form-group select {
      background: var(--bg3);
      border: 1px solid #333;
      color: var(--text);
      padding: 10px 14px;
      border-radius: 5px;
      font-family: inherit;
      font-size: 13px;
      transition: border-color 0.2s;
    }
    .form-group input:focus, .form-group select:focus { outline: none; border-color: var(--red2); }
    .form-group select option { background: var(--bg3); }
    .btn-search {
      padding: 10px 24px;
      background: var(--red);
      color: #fff;
      border: none;
      border-radius: 5px;
      font-family: inherit;
      font-size: 13px;
      font-weight: 700;
      letter-spacing: 1px;
      cursor: pointer;
      text-transform: uppercase;
      transition: background 0.2s;
    }
    .btn-search:hover { background: var(--red2); }

    /* ── TABELA ── */
    .table-wrap { overflow-x: auto; }
    table { width: 100%; border-collapse: collapse; font-size: 13px; }
    thead tr { background: var(--bg3); }
    th {
      padding: 12px 16px;
      text-align: left;
      font-size: 10px;
      letter-spacing: 2px;
      color: var(--text2);
      text-transform: uppercase;
      border-bottom: 1px solid var(--border);
    }
    td { padding: 11px 16px; border-bottom: 1px solid var(--border); color: var(--text); vertical-align: middle; }
    tr:hover td { background: var(--bg2); }
    .badge {
      display: inline-block;
      padding: 3px 8px;
      border-radius: 3px;
      font-size: 10px;
      font-weight: 700;
      letter-spacing: 1px;
      text-transform: uppercase;
    }
    .badge-crypt { background: #4a0000; color: #ff6666; }
    .badge-exfil { background: #004400; color: var(--green); }
    .badge-recon { background: #1a1a00; color: #ffcc00; }
    .badge-negoc { background: #001a33; color: #66aaff; }

    /* ── GALERIA ── */
    .gallery { display: grid; grid-template-columns: repeat(auto-fit, minmax(260px, 1fr)); gap: 24px; }
    .gallery-item {
      background: var(--bg2);
      border: 1px solid var(--border);
      border-radius: 8px;
      overflow: hidden;
    }
    .gallery-item img { width: 100%; height: auto; max-height: 420px; object-fit: contain; background: #0d0d0d; filter: saturate(0.3) brightness(0.7); display: block; }
    .gallery-cap { padding: 14px 16px; }
    .gallery-cap h4 { font-size: 14px; color: var(--red2); margin-bottom: 6px; }
    .gallery-cap p { font-size: 12px; color: var(--text2); }

    /* ── FERRAMENTAS ── */
    .tools-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 20px; }
    .tool-card {
      background: var(--bg2);
      border: 1px dashed #333;
      border-radius: 8px;
      padding: 22px;
      transition: border-color 0.2s;
    }
    .tool-card:hover { border-color: var(--red); }
    .tool-card h4 { color: var(--green); font-size: 14px; margin-bottom: 8px; }
    .tool-card p { font-size: 12px; color: var(--text2); }
    .tool-tag { display: inline-block; background: #1a0000; color: var(--red2); font-size: 10px; padding: 2px 8px; border-radius: 3px; margin-top: 10px; }

    /* ── ERRO ── */
    .error-box {
      background: #200000;
      border: 1px solid var(--red);
      border-radius: 6px;
      padding: 14px 18px;
      color: var(--red2);
      font-size: 13px;
      margin-bottom: 20px;
      font-family: monospace;
    }

    /* ── FOOTER ── */
    footer {
      background: var(--bg2);
      border-top: 1px solid var(--border);
      text-align: center;
      padding: 18px;
      font-size: 12px;
      color: #333;
      letter-spacing: 1px;
    }
  </style>
</head>
<body>

<!-- ══ HERO ══ -->
<div class="hero">
  <div class="hero-inner">
    <div class="hero-logo">
      <img src="../img/satonas1.png" alt="SatΩnas">
    </div>
    <div class="hero-content">
      <h1>SAT<span>Ω</span>NAS</h1>
      <div class="hero-sub">Ransomware-as-a-Service :: Painel de Controle C2</div>
      <div class="hero-stats">
        <div class="stat-box"><div class="stat-num">47</div><div class="stat-lbl">Vítimas Ativas</div></div>
        <div class="stat-box"><div class="stat-num">R$ 2.4M</div><div class="stat-lbl">Resgates Cobrados</div></div>
        <div class="stat-box"><div class="stat-num">12</div><div class="stat-lbl">Campanhas</div></div>
        <div class="stat-box"><div class="stat-num">97%</div><div class="stat-lbl">Taxa de Criptografia</div></div>
      </div>
    </div>
  </div>
</div>

<!-- ══ LAYOUT ══ -->
<div class="wrapper">

  <!-- ══ SIDEBAR ══ -->
  <nav class="sidebar">
    <div class="sidebar-label">Operações</div>
    <a href="?page=dashboard" class="<?= $page === 'dashboard' ? 'active' : '' ?>"><span class="ico">◈</span> Dashboard</a>
    <a href="?page=vitimas" class="<?= $page === 'vitimas' ? 'active' : '' ?>"><span class="ico">◈</span> Vítimas</a>
    <a href="?page=busca" class="<?= $page === 'busca' ? 'active' : '' ?>"><span class="ico">◈</span> Busca</a>
    <a href="?page=campanhas" class="<?= $page === 'campanhas' ? 'active' : '' ?>"><span class="ico">◈</span> Campanhas</a>
    <div class="sidebar-label">Arsenal</div>
    <a href="?page=ferramentas" class="<?= $page === 'ferramentas' ? 'active' : '' ?>"><span class="ico">◈</span> Ferramentas</a>
    <a href="?page=galeria" class="<?= $page === 'galeria' ? 'active' : '' ?>"><span class="ico">◈</span> Galeria</a>
    <div class="sidebar-label">Sistema</div>
    <a href="?page=sobre" class="<?= $page === 'sobre' ? 'active' : '' ?>"><span class="ico">◈</span> Sobre</a>
  </nav>

  <!-- ══ MAIN ══ -->
  <main class="main">

    <?php if ($page === 'dashboard'): ?>
    <div class="page-title">Painel Principal</div>
    <h2>// Dashboard</h2>
    <div class="dash-grid">
      <div class="dash-card">
        <div class="d-val">47</div>
        <div class="d-lbl">Vítimas Totais</div>
        <div class="d-trend">↑ +3 esta semana</div>
      </div>
      <div class="dash-card">
        <div class="d-val">18</div>
        <div class="d-lbl">Aguardando Pagamento</div>
        <div class="d-trend">↑ Prazo expira em 48h</div>
      </div>
      <div class="dash-card">
        <div class="d-val">29</div>
        <div class="d-lbl">Sistemas Criptografados</div>
        <div class="d-trend">✓ Operação concluída</div>
      </div>
      <div class="dash-card">
        <div class="d-val">R$480k</div>
        <div class="d-lbl">Pendente de Receber</div>
        <div class="d-trend">↑ Maior lote do mês</div>
      </div>
    </div>
    <div class="feed">
      <h3>// Feed de Atividades</h3>
      <div class="feed-item"><div class="feed-dot"></div><span class="feed-time">2026-02-24 03:12</span><span class="feed-msg">Nova vítima registrada: <strong>TechCorp Nordeste</strong> — Campanha: SERPENTE-07</span></div>
      <div class="feed-item"><div class="feed-dot"></div><span class="feed-time">2026-02-23 22:45</span><span class="feed-msg">Beacon recebido de <strong>172.16.8.44</strong> — sistema: Windows Server 2019</span></div>
      <div class="feed-item"><div class="feed-dot green"></div><span class="feed-time">2026-02-23 18:30</span><span class="feed-msg">Pagamento confirmado: <strong>Grupo Meireles S/A</strong> — R$ 95.000 em BTC</span></div>
      <div class="feed-item"><div class="feed-dot"></div><span class="feed-time">2026-02-23 11:00</span><span class="feed-msg">Criptografia concluída em <strong>Distribuidora Solaris</strong> — 14.200 arquivos</span></div>
      <div class="feed-item"><div class="feed-dot green"></div><span class="feed-time">2026-02-22 09:15</span><span class="feed-msg">Exfiltração concluída: <strong>Clínica Boa Saúde</strong> — 2.3 GB de dados sensíveis</span></div>
    </div>

    <?php elseif ($page === 'vitimas'): ?>
    <div class="page-title">Gerenciamento</div>
    <h2>// Vítimas</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr>
            <th>#</th><th>Hostname</th><th>Sistema</th><th>Campanha</th><th>Status</th><th>Resgate</th>
          </tr>
        </thead>
        <tbody>
          <?php
          try {
              $pdo  = getPDO();
              $rows = $pdo->query("SELECT id, hostname, sistema_operacional, campanha, status, valor_resgate FROM vitimas ORDER BY id")->fetchAll(PDO::FETCH_ASSOC);
              foreach ($rows as $r):
                $statusClass = match($r['status']) {
                    'CRIPTOGRAFADO'  => 'badge-crypt',
                    'EXFILTRADO'     => 'badge-exfil',
                    'RECONHECIMENTO' => 'badge-recon',
                    default          => 'badge-negoc',
                };
          ?>
          <tr>
            <td><?= htmlspecialchars($r['id']) ?></td>
            <td><?= htmlspecialchars($r['hostname']) ?></td>
            <td><?= htmlspecialchars($r['sistema_operacional']) ?></td>
            <td><?= htmlspecialchars($r['campanha']) ?></td>
            <td><span class="badge <?= $statusClass ?>"><?= htmlspecialchars($r['status']) ?></span></td>
            <td><?= htmlspecialchars($r['valor_resgate']) ?></td>
          </tr>
          <?php endforeach; } catch (Exception $e) { echo '<tr><td colspan="6" style="color:#cc0000">' . htmlspecialchars($e->getMessage()) . '</td></tr>'; } ?>
        </tbody>
      </table>
    </div>

    <?php elseif ($page === 'busca'): ?>
    <div class="page-title">Inteligência</div>
    <h2>// Busca de Vítimas</h2>

    <div class="search-box">
      <h3>// Filtros de Pesquisa</h3>
      <form method="POST" action="?page=busca">
        <div class="form-row">
          <div class="form-group">
            <label>Buscar por</label>
            <select name="coluna">
              <option value="hostname" <?= $coluna === 'hostname' ? 'selected' : '' ?>>Hostname</option>
              <option value="sistema_operacional" <?= $coluna === 'sistema_operacional' ? 'selected' : '' ?>>Sistema Operacional</option>
              <option value="campanha" <?= $coluna === 'campanha' ? 'selected' : '' ?>>Campanha</option>
              <option value="status" <?= $coluna === 'status' ? 'selected' : '' ?>>Status</option>
            </select>
          </div>
          <div class="form-group">
            <label>Valor</label>
            <input type="text" name="busca" value="<?= htmlspecialchars($busca) ?>" placeholder="Ex: Windows, SERPENTE, CRIPTOGRAFADO..." />
          </div>
          <button type="submit" class="btn-search">&#x25BA; Buscar</button>
        </div>
      </form>
    </div>



    <?php if (!empty($resultados)): ?>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>#</th><th>Hostname</th><th>Sistema</th><th>Campanha</th><th>Status</th></tr>
        </thead>
        <tbody>
          <?php foreach ($resultados as $r): ?>
          <tr>
            <td><?= htmlspecialchars($r['id']) ?></td>
            <td><?= htmlspecialchars($r['hostname']) ?></td>
            <td><?= htmlspecialchars($r['sistema_operacional']) ?></td>
            <td><?= htmlspecialchars($r['campanha']) ?></td>
            <td><?= htmlspecialchars($r['status']) ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
    <p style="color:var(--text2);font-size:13px;">Nenhuma vítima encontrada para os critérios informados.</p>
    <?php endif; ?>

    <?php elseif ($page === 'campanhas'): ?>
    <div class="page-title">Operações</div>
    <h2>// Campanhas</h2>
    <div class="table-wrap">
      <table>
        <thead>
          <tr><th>Código</th><th>Alvo</th><th>Vetor</th><th>Início</th><th>Status</th></tr>
        </thead>
        <tbody>
          <tr><td>SERPENTE-01</td><td>Saúde / Sul</td><td>Phishing Doc</td><td>2025-10-03</td><td><span class="badge badge-crypt">ENCERRADA</span></td></tr>
          <tr><td>SERPENTE-03</td><td>Logística / SP</td><td>VPN 0-day</td><td>2025-11-14</td><td><span class="badge badge-exfil">EXFILTRADA</span></td></tr>
          <tr><td>SERPENTE-05</td><td>Financeiro / RJ</td><td>Macro Office</td><td>2026-01-07</td><td><span class="badge badge-negoc">NEGOCIAÇÃO</span></td></tr>
          <tr><td>SERPENTE-07</td><td>Varejo / NE</td><td>Supply chain</td><td>2026-02-10</td><td><span class="badge badge-recon">ATIVO</span></td></tr>
        </tbody>
      </table>
    </div>

    <?php elseif ($page === 'ferramentas'): ?>
    <div class="page-title">Arsenal</div>
    <h2>// Ferramentas</h2>
    <div class="tools-grid">
      <div class="tool-card"><h4>SatoLocker v3.1</h4><p>Módulo de criptografia AES-256 + RSA-2048. Suporte a SMB lateral movement.</p><span class="tool-tag">RANSOMWARE</span></div>
      <div class="tool-card"><h4>DataSip</h4><p>Exfiltrador silencioso via DNS tunneling. Bypass de DLP e proxies corporativos.</p><span class="tool-tag">EXFIL</span></div>
      <div class="tool-card"><h4>ShadowBeacon</h4><p>C2 beacon persistence via scheduled tasks e COM hijacking. Latência 30–120s.</p><span class="tool-tag">C2</span></div>
      <div class="tool-card"><h4>NightCrawler</h4><p>Lateral movement automatizado: DCOM, WMI, PsExec. Credential harvesting via LSASS.</p><span class="tool-tag">LATERAL</span></div>
      <div class="tool-card"><h4>PhantomDrop</h4><p>Packer polimórfico com assinatura rotativa. Evasão de AV/EDR com score &lt;5%.</p><span class="tool-tag">EVASÃO</span></div>
      <div class="tool-card"><h4>WipeTrace</h4><p>Limpeza de logs: EventLog, Prefetch, Shellbags, MFT journal. Anti-forensics.</p><span class="tool-tag">ANTIFORENSE</span></div>
    </div>

    <?php elseif ($page === 'galeria'): ?>
    <div class="page-title">Documentação</div>
    <h2>// Galeria de Operações</h2>
    <div class="gallery">
      <div class="gallery-item">
        <img src="../img/satonas1.png" alt="Op Serpente">
        <div class="gallery-cap"><h4>Op. Serpente-01</h4><p>Primeiro ataque bem-sucedido. Rede hospitalar — 3.200 endpoints criptografados.</p></div>
      </div>
      <div class="gallery-item">
        <img src="../img/satonas2.png" alt="Op Sombra">
        <div class="gallery-cap"><h4>Op. Sombra</h4><p>Exfiltração de dados de contabilidade. 14GB extraídos antes da criptografia.</p></div>
      </div>
      <div class="gallery-item">
        <img src="../img/satonas3.png" alt="Op Abismo">
        <div class="gallery-cap"><h4>Op. Abismo</h4><p>Campanha de supply chain. Comprometimento de 8 empresas via fornecedor de TI.</p></div>
      </div>
    </div>

    <?php else: ?>
    <div class="page-title">Informações</div>
    <h2>// Sobre o SatΩnas</h2>
    <p style="color:var(--text2);font-size:14px;line-height:1.9;max-width:600px;">
      <strong style="color:var(--red2)">SatΩnas</strong> é o líder e fundador de um grupo de Ransomware-as-a-Service (RaaS) especializado em alvos corporativos de médio e grande porte na América Latina.<br><br>
      Utilizamos táticas avançadas de persistência, exfiltração de dados antes da criptografia (double extortion) e negociação direta via Tor.<br><br>
      Versão do painel: <span style="color:var(--green)">3.4.1</span><br>
      Build: <span style="color:var(--red2)">2026.02-internal</span>
    </p>
    <?php endif; ?>

  </main>
</div>

<footer>
  SatΩnas C2 Panel :: Build 2026.02 :: Uso interno autorizado apenas
</footer>

</body>
</html>
