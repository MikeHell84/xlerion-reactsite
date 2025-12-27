<?php
// public/api/contact.php
// Receives POST from contact form; saves to DB (PDO) or falls back to backup JSON.
require_once __DIR__ . '/../../includes/config.php';

// DEBUG: dump some server vars to a temp file for diagnosis (removed after)
@file_put_contents(__DIR__ . '/contact_debug.json', json_encode([
    'HTTP_ACCEPT'=>($_SERVER['HTTP_ACCEPT'] ?? null),
    'CONTENT_TYPE'=>($_SERVER['CONTENT_TYPE'] ?? null),
    'REQUEST_METHOD'=>($_SERVER['REQUEST_METHOD'] ?? null),
    'POST'=>$_POST
], JSON_PRETTY_PRINT));

// Allow both form POST and JSON
$isJson = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) || (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false);

function respond($data, $code = 200){
    $acceptsJson = (isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json')) || (strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false);
    if($acceptsJson){
        header('Content-Type: application/json'); http_response_code($code); echo json_encode($data, JSON_UNESCAPED_UNICODE); exit;
    }
    // default: redirect back to contact page with status
    $loc = '/contacto.php';
    if(isset($data['status'])) $loc .= '?status=' . urlencode($data['status']);
    header('Location: ' . $loc);
    exit;
}

$name = trim($_POST['name'] ?? '');
$email = trim($_POST['email'] ?? '');
$message = trim($_POST['message'] ?? '');

if($name === '' || $email === '' || $message === ''){
    respond(['status'=>'error','message'=>'Campos requeridos faltantes'], 400);
}

$entry = [
    'name'=>$name,
    'email'=>$email,
    'message'=>$message,
    'created_at'=>date('Y-m-d H:i:s')
];

$saved = false; $errorMsg = null;
try{
    $pdo = get_pdo();
    // Adapt to existing contacts schema if necessary
    $colsStmt = $pdo->query("SHOW COLUMNS FROM contacts");
    $cols = array_map(function($c){ return $c['Field']; }, $colsStmt->fetchAll(PDO::FETCH_ASSOC));
    if(in_array('name', $cols)){
        $sql = "INSERT INTO contacts (`name`,`email`,`message`,`created_at`) VALUES (:name,:email,:message,:created_at)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($entry);
    } elseif(in_array('first_name', $cols) && in_array('last_name', $cols)){
        // split full name into first/last
        $parts = preg_split('/\s+/', $entry['name'], 2);
        $first = $parts[0] ?? '';
        $last = $parts[1] ?? '';
        $sql = "INSERT INTO contacts (first_name,last_name,email,notes,created_at) VALUES (:first,:last,:email,:notes,:created_at)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['first'=>$first,'last'=>$last,'email'=>$entry['email'],'notes'=>$entry['message'],'created_at'=>$entry['created_at']]);
    } else {
        // fallback: try basic columns
        $sql = "INSERT INTO contacts (email,notes,created_at) VALUES (:email,:notes,:created_at)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(['email'=>$entry['email'],'notes'=>$entry['message'],'created_at'=>$entry['created_at']]);
    }
    $saved = true;
} catch (Exception $e){
    $errorMsg = $e->getMessage();
}

if(!$saved){
    // fallback: write to backup/contacts
    $backupDir = __DIR__ . '/../../backup/contacts';
    if(!is_dir($backupDir)) @mkdir($backupDir, 0755, true);
    $filename = $backupDir . '/' . date('Ymd_His') . '_' . preg_replace('/[^a-z0-9]/i','_',substr($name,0,20)) . '.json';
    $payload = ['entry'=>$entry,'error'=>$errorMsg];
    file_put_contents($filename, json_encode($payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE));
}

if($saved){
    respond(['status'=>'ok','message'=>'Mensaje recibido'], 200);
} else {
    respond(['status'=>'queued','message'=>'No se pudo guardar en DB, mensaje en backup'], 202);
}
