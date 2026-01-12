<?php
require_once __DIR__ . '/../../../includes/config.php';
header('Content-Type: application/json');

$dataFile = __DIR__ . '/../../../data/leads.json';
if (!is_dir(dirname($dataFile))) {
  @mkdir(dirname($dataFile), 0755, true);
}

function load_leads_file($path) {
  if (!file_exists($path)) return [];
  $j = file_get_contents($path);
  $a = json_decode($j, true);
  return is_array($a) ? $a : [];
}

function save_leads_file($path, $arr) {
  file_put_contents($path, json_encode(array_values($arr), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

$method = $_SERVER['REQUEST_METHOD'];
$pdo = try_get_pdo();

function read_json_body() {
  $txt = file_get_contents('php://input');
  $data = json_decode($txt, true);
  return is_array($data) ? $data : [];
}

if (in_array($method, ['POST','PUT','DELETE'])) {
  if (empty($_SESSION['user']) || empty($_SESSION['user']['id'])) {
    http_response_code(401);
    echo json_encode(['ok'=>false,'error'=>'Unauthorized']);
    exit;
  }
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
    if ($method === 'GET') {
      $id = isset($_GET['id']) ? intval($_GET['id']) : null;
      if ($id) {
        $stmt = $pdo->prepare('SELECT * FROM crm_leads WHERE id = ? LIMIT 1');
        $stmt->execute([$id]);
        echo json_encode(['ok'=>true,'data'=>$stmt->fetch()]); exit;
      }
      $rows = $pdo->query('SELECT * FROM crm_leads ORDER BY id DESC')->fetchAll();
      echo json_encode(['ok'=>true,'data'=>$rows]); exit;
    }
    if ($method === 'POST') {
      $body = $_POST ?: read_json_body();
      $stmt = $pdo->prepare('INSERT INTO crm_leads (source,name,email,score,status,created_at) VALUES (?,?,?,?,?,NOW())');
      $stmt->execute([$body['source'] ?? 'web',$body['name'] ?? '','' . ($body['email'] ?? null), intval($body['score'] ?? 0), $body['status'] ?? 'new']);
      $id = $pdo->lastInsertId();
      audit_log('crm.leads.create', $_SESSION['user']['id'] ?? null, ['id'=>$id]);
      echo json_encode(['ok'=>true,'id'=>$id]); exit;
    }
    if ($method === 'PUT') {
      $body = read_json_body();
      $id = intval($body['id'] ?? 0);
      if (!$id) { echo json_encode(['ok'=>false,'error'=>'Missing id']); exit; }
      $fields = [];$params=[];
      foreach (['source','name','email','score','status'] as $f) if (isset($body[$f])) { $fields[]="$f = ?"; $params[]=$body[$f]; }
      if ($fields) { $params[]=$id; $sql='UPDATE crm_leads SET '.implode(',',$fields).' WHERE id = ?'; $stmt=$pdo->prepare($sql); $stmt->execute($params); audit_log('crm.leads.update', $_SESSION['user']['id'] ?? null, ['id'=>$id]); }
      echo json_encode(['ok'=>true]); exit;
    }
    if ($method === 'DELETE') {
      $body = read_json_body(); $id = intval($body['id'] ?? 0); if (!$id) { echo json_encode(['ok'=>false,'error'=>'Missing id']); exit; }
      $stmt=$pdo->prepare('DELETE FROM crm_leads WHERE id = ?'); $stmt->execute([$id]); audit_log('crm.leads.delete', $_SESSION['user']['id'] ?? null, ['id'=>$id]); echo json_encode(['ok'=>true]); exit;
    }
  }
} catch (Exception $e) {
  // continue to file fallback
}

$leads = load_leads_file($dataFile);
if ($method === 'GET') {
  $id = isset($_GET['id']) ? intval($_GET['id']) : null;
  if ($id) { foreach ($leads as $l) if (intval($l['id']) === $id) { echo json_encode(['ok'=>true,'data'=>$l]); exit; } echo json_encode(['ok'=>false,'error'=>'Not found']); exit; }
  echo json_encode(['ok'=>true,'data'=>$leads]); exit;
}

if ($method === 'POST') {
  $body = $_POST ?: read_json_body(); $next=1; foreach($leads as $l) $next=max($next,intval($l['id'])+1);
  $item=['id'=>$next,'source'=>$body['source']??'web','name'=>$body['name']??'','email'=>$body['email']??null,'score'=>intval($body['score']??0),'status'=>$body['status']??'new','created_at'=>date('c')];
  $leads[]=$item; save_leads_file($dataFile,$leads); audit_log('crm.leads.create', $_SESSION['user']['id'] ?? null, ['id'=>$item['id']]); echo json_encode(['ok'=>true,'id'=>$item['id'],'data'=>$item]); exit;
}

if ($method === 'PUT') {
  $body = read_json_body(); $id=intval($body['id']??0);
  foreach($leads as &$l) { if (intval($l['id'])=== $id) { foreach(['source','name','email','score','status'] as $f) if (isset($body[$f])) $l[$f]=$body[$f]; $l['updated_at']=date('c'); save_leads_file($dataFile,$leads); audit_log('crm.leads.update', $_SESSION['user']['id'] ?? null, ['id'=>$id]); echo json_encode(['ok'=>true,'data'=>$l]); exit; } }
  echo json_encode(['ok'=>false,'error'=>'Not found']); exit;
}

if ($method === 'DELETE') {
  $body = read_json_body(); $id=intval($body['id']??0); $found=false; foreach($leads as $i=>$l) { if (intval($l['id'])=== $id) { $found=true; array_splice($leads,$i,1); break; } }
  if ($found) { save_leads_file($dataFile,$leads); audit_log('crm.leads.delete', $_SESSION['user']['id'] ?? null, ['id'=>$id]); echo json_encode(['ok'=>true]); exit; }
  echo json_encode(['ok'=>false,'error'=>'Not found']); exit;
}

http_response_code(405); echo json_encode(['ok'=>false,'error'=>'Method not allowed']);
