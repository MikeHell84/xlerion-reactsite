<?php
require_once __DIR__ . '/../../../includes/config.php';
header('Content-Type: application/json');

$dataFile = __DIR__ . '/../../../data/clients.json';
if (!is_dir(dirname($dataFile))) {
  @mkdir(dirname($dataFile), 0755, true);
}

function load_clients_file($path) {
  if (!file_exists($path)) return [];
  $j = file_get_contents($path);
  $a = json_decode($j, true);
  return is_array($a) ? $a : [];
}

function save_clients_file($path, $arr) {
  file_put_contents($path, json_encode(array_values($arr), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$method = $_SERVER['REQUEST_METHOD'];
$env = $GLOBALS['env'] ?? [];
// Dev-only header to simulate an authenticated admin user when APP_ENV=development
if (!empty($env['APP_ENV']) && strtolower($env['APP_ENV']) === 'development') {
  $devAuth = $_SERVER['HTTP_X_DEV_AUTH'] ?? null;
  if ($devAuth === 'dev-admin-token') {
    $_SESSION['user'] = ['id' => 1, 'username' => 'dev', 'role' => 'admin'];
    // Provide a predictable CSRF token for dev testing
    if (empty($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = 'devtoken';
  }
}
$pdo = try_get_pdo();

// Helper: read JSON body for PUT/DELETE
function read_json_body() {
  $txt = file_get_contents('php://input');
  $data = json_decode($txt, true);
  return is_array($data) ? $data : [];
}

// Simple auth for mutating requests
if (in_array($method, ['POST','PUT','DELETE'])) {
  // Accept session-based auth or dev fallback
  if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'Unauthorized']);
    exit;
  }
  // CSRF check: header X-CSRF-Token or csrf_token in body
  $csrf = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
  if (!$csrf) {
    $body = read_json_body();
    $csrf = $body['csrf_token'] ?? null;
  }
  if (!validate_csrf_token($csrf)) {
    http_response_code(403);
    echo json_encode(['ok'=>false,'error'=>'Invalid CSRF token']);
    exit;
  }
}

try {
  if ($pdo) {
    // If DB available, use simple table `crm_customers` (best-effort)
    if ($method === 'GET') {
      $id = isset($_GET['id']) ? intval($_GET['id']) : null;
      if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM crm_customers WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        echo json_encode(['ok'=>true,'data'=>$row]);
        exit;
      }
      $stmt = $pdo->query('SELECT * FROM crm_customers ORDER BY id DESC');
      $rows = $stmt->fetchAll();
      echo json_encode(['ok'=>true,'data'=>$rows]);
      exit;
    }
    if ($method === 'POST') {
      $body = $_POST ?: read_json_body();
      $name = $body['name'] ?? '';
      $email = $body['email'] ?? null;
      $status = $body['status'] ?? 'active';
      $tags = isset($body['tags']) ? json_encode($body['tags']) : null;
      $stmt = $pdo->prepare('INSERT INTO crm_customers (name,email,status,tags,created_at) VALUES (?,?,?,?,NOW())');
      $stmt->execute([$name,$email,$status,$tags]);
      $id = $pdo->lastInsertId();
      audit_log('crm.customers.create', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
      echo json_encode(['ok'=>true,'id'=>$id]);
      exit;
    }
    if ($method === 'PUT') {
      $body = read_json_body();
      $id = intval($body['id'] ?? 0);
      if (!$id) { echo json_encode(['ok'=>false,'error'=>'Missing id']); exit; }
      $fields = [];
      $params = [];
      foreach (['name','email','status'] as $f) {
        if (isset($body[$f])) { $fields[] = "$f = ?"; $params[] = $body[$f]; }
      }
      if ($fields) {
        $params[] = $id;
        $sql = 'UPDATE crm_customers SET ' . implode(',', $fields) . ' WHERE id = ?';
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        audit_log('crm.customers.update', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
      }
      echo json_encode(['ok'=>true]);
      exit;
    }
    if ($method === 'DELETE') {
      $body = read_json_body();
      $id = intval($body['id'] ?? 0);
      if (!$id) { echo json_encode(['ok'=>false,'error'=>'Missing id']); exit; }
      $stmt = $pdo->prepare('DELETE FROM crm_customers WHERE id = ?');
      $stmt->execute([$id]);
      audit_log('crm.customers.delete', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
      echo json_encode(['ok'=>true]);
      exit;
    }
  }
} catch (Exception $e) {
  // Fall back to file storage if DB operations fail
}

// File-backed fallback
$clients = load_clients_file($dataFile);
if ($method === 'GET') {
  $id = isset($_GET['id']) ? intval($_GET['id']) : null;
  if ($id) {
    foreach ($clients as $c) if (intval($c['id']) === $id) { echo json_encode(['ok'=>true,'data'=>$c]); exit; }
    echo json_encode(['ok'=>false,'error'=>'Not found']); exit;
  }
  echo json_encode(['ok'=>true,'data'=>$clients]);
  exit;
}

if ($method === 'POST') {
  $body = $_POST ?: read_json_body();
  $next = 1; foreach ($clients as $c) $next = max($next, intval($c['id'])+1);
  $item = [
    'id' => $next,
    'name' => $body['name'] ?? 'Sin nombre',
    'email' => $body['email'] ?? null,
    'status' => $body['status'] ?? 'active',
    'tags' => $body['tags'] ?? [],
    'created_at' => date('c')
  ];
  $clients[] = $item;
  save_clients_file($dataFile, $clients);
  audit_log('crm.customers.create', $_SESSION['user']['id'] ?? null, ['id'=>$item['id']]);
  echo json_encode(['ok'=>true,'id'=>$item['id'],'data'=>$item]);
  exit;
}

if ($method === 'PUT') {
  $body = read_json_body();
  $id = intval($body['id'] ?? 0);
  foreach ($clients as &$c) {
    if (intval($c['id']) === $id) {
      foreach (['name','email','status','tags'] as $f) if (isset($body[$f])) $c[$f] = $body[$f];
      $c['updated_at'] = date('c');
      save_clients_file($dataFile, $clients);
      audit_log('crm.customers.update', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
      echo json_encode(['ok'=>true,'data'=>$c]); exit;
    }
  }
  echo json_encode(['ok'=>false,'error'=>'Not found']); exit;
}

if ($method === 'DELETE') {
  $body = read_json_body();
  $id = intval($body['id'] ?? 0);
  $found = false;
  foreach ($clients as $i => $c) {
    if (intval($c['id']) === $id) { $found = true; array_splice($clients, $i, 1); break; }
  }
  if ($found) {
    save_clients_file($dataFile, $clients);
    audit_log('crm.customers.delete', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
    echo json_encode(['ok'=>true]); exit;
  }
  echo json_encode(['ok'=>false,'error'=>'Not found']); exit;
}

// Method not allowed
http_response_code(405);
echo json_encode(['ok'=>false,'error'=>'Method not allowed']);
