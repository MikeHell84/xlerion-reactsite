<?php
$env = [];
foreach(file('.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line){
    $t = trim($line);
    if($t === '' || strpos($t, '#') === 0) continue;
    if(strpos($t, '=') === false) continue;
    list($k,$v) = explode('=', $t, 2);
    $env[trim($k)] = trim($v, "'\"\n\r ");
}
$hash = $env['ADMIN_PASS_HASH'] ?? '';
echo "ADMIN_PASS_HASH=" . ($hash ? 'SET' : 'EMPTY') . PHP_EOL;
echo password_verify('admin1984!', $hash) ? "password_verify=OK" : "password_verify=FAIL";

?>
