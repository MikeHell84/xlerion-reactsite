<?php
// public/api/contact.php
// Receives POST from contact form; saves to DB (PDO) or falls back to backup JSON.
require_once __DIR__ . '/../../includes/config.php';

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
    $sql = "INSERT INTO contacts (`name`,`email`,`message`,`created_at`) VALUES (:name,:email,:message,:created_at)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute($entry);
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
