<?php
/**
 * Compara el esquema remoto (data/remote_schema.json) con la DB local
 * y genera sentencias ALTER TABLE ADD COLUMN para columnas faltantes
 * No elimina ni modifica columnas existentes.
 */
require_once __DIR__ . '/../includes/config.php';

$remotePath = __DIR__ . '/../data/remote_schema.json';
if (!file_exists($remotePath)) {
    echo "remote schema not found: $remotePath\n";
    exit(1);
}
$remote = json_decode(file_get_contents($remotePath), true);
if (!$remote) { echo "invalid remote schema JSON\n"; exit(1); }

$tablesOfInterest = ['pages','modules','users'];
$pdo = try_get_pdo();
$migrationsSql = [];
$diff = [];

foreach ($tablesOfInterest as $tbl) {
    if (!isset($remote[$tbl])) {
        $diff[$tbl] = ['error' => 'table missing in remote schema'];
        continue;
    }
    // check local table existence
    $tableExists = false;
    try {
        $r = $pdo->query("SHOW TABLES LIKE '" . addslashes($tbl) . "'")->fetchAll();
        $tableExists = count($r) > 0;
    } catch (Exception $e) {
        echo "DB error checking table $tbl: " . $e->getMessage() . "\n";
        $diff[$tbl] = ['error' => $e->getMessage()];
        continue;
    }
    if (!$tableExists) {
        $diff[$tbl] = ['status' => 'missing_table_local', 'note' => 'Not creating full table per policy'];
        continue;
    }

    // get local columns
    $localCols = [];
    try {
        $cols = $pdo->query("DESCRIBE `$tbl`")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($cols as $c) { $localCols[$c['Field']] = $c; }
    } catch (Exception $e) {
        $diff[$tbl] = ['error' => $e->getMessage()];
        continue;
    }

    $remoteCols = [];
    foreach ($remote[$tbl]['columns'] as $c) { $remoteCols[$c['Field']] = $c; }

    $missing = [];
    foreach ($remoteCols as $colName => $colDef) {
        if (!array_key_exists($colName, $localCols)) {
            $missing[$colName] = $colDef;
            // Build ADD COLUMN SQL
            $type = $colDef['Type'];
            $null = ($colDef['Null'] === 'YES') ? 'NULL' : 'NOT NULL';
            $default = '';
            if ($colDef['Default'] !== null && $colDef['Default'] !== '') {
                // handle functions like current_timestamp()
                if (preg_match('/\(\)$/', $colDef['Default']) || stripos($colDef['Default'],'current_timestamp')!==false) {
                    $default = ' DEFAULT ' . $colDef['Default'];
                } else {
                    $default = " DEFAULT '" . addslashes($colDef['Default']) . "'";
                }
            } elseif ($colDef['Null'] === 'YES') {
                $default = ' DEFAULT NULL';
            }
            $extra = isset($colDef['Extra']) && $colDef['Extra'] ? ' ' . $colDef['Extra'] : '';
            $migrationsSql[] = "ALTER TABLE `$tbl` ADD COLUMN `$colName` $type $null$default$extra;";
        }
    }
    $diff[$tbl] = ['missing_columns' => array_keys($missing)];
}

$outSql = __DIR__ . '/../database/migrations/remote_sync_add_columns.sql';
$header = "-- Remote schema sync (generated)\n-- Date: " . date('c') . "\n-- Policy: only ADD columns, no drops or modifications\n\n";
file_put_contents($outSql, $header . implode("\n", $migrationsSql) . "\n");

$logPath = __DIR__ . '/../xlerion-backups/remote_sync_diff.log';
file_put_contents($logPath, date('c') . "\n" . json_encode($diff, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n", FILE_APPEND | LOCK_EX);

echo "Wrote migrations to: $outSql\n";
echo "Wrote diff log to: $logPath\n";
echo "Statements generated: " . count($migrationsSql) . "\n";

// Also write a quick models map placeholder
$modelsDir = __DIR__ . '/../includes/models';
if (!is_dir($modelsDir)) mkdir($modelsDir, 0755, true);
// Pages model
file_put_contents($modelsDir . '/PagesModel.php', <<<'PHP'
<?php
class PagesModel {
    public static function all() {
        require_once __DIR__ . '/../config.php';
        $pdo = try_get_pdo(); if (!$pdo) return [];
        $stmt = $pdo->query('SELECT * FROM pages ORDER BY id ASC'); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
PHP
);
// Modules model
file_put_contents($modelsDir . '/ModulesModel.php', <<<'PHP'
<?php
class ModulesModel {
    public static function forPage($pageId) { require_once __DIR__ . '/../config.php'; 
        $pdo = try_get_pdo(); if (!$pdo) return []; 
        $stmt = $pdo->prepare('SELECT * FROM modules WHERE page_id = ? ORDER BY `order` ASC'); 
        $stmt->execute([$pageId]); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
PHP
);
// Users model
file_put_contents($modelsDir . '/UsersModel.php', <<<'PHP'
<?php
class UsersModel {
    public static function all() { require_once __DIR__ . '/../config.php'; 
        $pdo = try_get_pdo(); if (!$pdo) return []; 
        $stmt = $pdo->query('SELECT * FROM users ORDER BY id ASC'); return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
PHP
);

// Audit log
audit_log('remote_schema_sync:generated', null, ['sql_file' => $outSql, 'diff_log' => $logPath, 'statements' => count($migrationsSql)]);

return 0;

