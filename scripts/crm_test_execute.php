<?php
require __DIR__ . '/../includes/config.php';

$cookieFile = __DIR__ . '/cookies.txt';

function curl_post($url, $postFields, $cookieFile) {
    $ch = curl_init($url);
    $post = is_string($postFields) ? $postFields : http_build_query($postFields);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/x-www-form-urlencoded']);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
        throw new Exception('cURL error: ' . $err);
    }
    return $res;
}

function curl_get($url, $cookieFile) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
    curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    $res = curl_exec($ch);
    $err = curl_error($ch);
    curl_close($ch);
    if ($err) {
        throw new Exception('cURL error: ' . $err);
    }
    return $res;
}

echo "Logging in...\n";
try {
    $login = curl_post('http://localhost:8000/public/admin/login.php', ['username' => 'admin', 'password' => 'admin1984!'], $cookieFile);
} catch (Exception $e) {
    echo "Login request failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Fetching customers page...\n";
$page = curl_get('http://localhost:8000/public/admin/crm/customers.php', $cookieFile);
if (preg_match("/CSRF_TOKEN\s*=\s*'([a-f0-9]+)'/", $page, $m)) {
    $token = $m[1];
    echo "TOKEN:$token\n";
} else {
    echo "CSRF token not found — saving page to scripts/customers_page.html for inspection\n";
    file_put_contents(__DIR__ . '/customers_page.html', $page);
    exit(2);
}

echo "Creating customer via API...\n";
$payload = json_encode(['name' => 'Test Client 20251228', 'email' => 'test+20251228@example.com', 'phone' => '3200000000', 'csrf_token' => $token]);
$ch = curl_init('http://localhost:8000/public/api/crm/customers.php');
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
$res = curl_exec($ch);
$http = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$err = curl_error($ch);
curl_close($ch);
if ($err) {
    echo "API cURL error: $err\n";
    exit(1);
}
echo "API_RESPONSE_HTTP:$http\n";
echo $res . "\n";

echo "Verifying DB entry via PDO...\n";
$pdo = try_get_pdo();
if (!$pdo) {
    echo "No DB connection via PDO\n";
    exit(0);
}
$stmt = $pdo->prepare('SELECT id,name,email,created_at FROM crm_customers WHERE email = ? LIMIT 1');
$stmt->execute(['test+20251228@example.com']);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
echo json_encode($row, JSON_PRETTY_PRINT) . "\n";

?>
