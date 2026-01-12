<?php
// Generates a schema-only SQL file from data/remote_schema.json
$jsonPath = __DIR__ . '/../data/remote_schema.json';
$outPath = __DIR__ . '/../remote_schema_only_from_json.sql';
if (!file_exists($jsonPath)) {
    fwrite(STDERR, "remote_schema.json not found\n");
    exit(1);
}
$schema = json_decode(file_get_contents($jsonPath), true);
if (!is_array($schema)) {
    fwrite(STDERR, "Failed to parse JSON\n");
    exit(1);
}

$sql = "-- Generated CREATE TABLE statements from data/remote_schema.json\n\nSET SQL_MODE = 'NO_ENGINE_SUBSTITUTION';\nSET FOREIGN_KEY_CHECKS=0;\n\n";

foreach ($schema as $table => $meta) {
    $columns = $meta['columns'] ?? [];
    $colDefs = [];
    $primary = [];
    $uniques = [];
    $keys = [];
    foreach ($columns as $col) {
        $name = $col['Field'];
        $type = $col['Type'];
        $null = ($col['Null'] === 'YES') ? 'NULL' : 'NOT NULL';
        $default = '';
        if (isset($col['Default']) && $col['Default'] !== null) {
            $d = $col['Default'];
            // If column is TEXT/BLOB/JSON-like, skip defaults (unsupported on many MySQL versions)
            if (preg_match('/text|blob|json|longtext|mediumtext/i', $type)) {
                $d = null;
            }
            // Handle function defaults safely: allow CURRENT_TIMESTAMP on datetime/timestamp only
            $lower = strtolower($d);
            if (stripos($lower, 'curdate') !== false) {
                // curdate() not reliably supported as column default on many MySQL versions — skip
                $default = '';
            } elseif (stripos($lower, 'current_timestamp') !== false) {
                if (stripos(strtolower($type), 'timestamp') !== false || stripos(strtolower($type), 'datetime') !== false) {
                    $default = " DEFAULT {$d}";
                } else {
                    $default = '';
                }
            } elseif (preg_match('/\(\)$/', $d)) {
                // other functions — skip to be safe
                $default = '';
            } else {
                // Only allow simple printable defaults (avoid function calls and broken quoting)
                if (preg_match('/^[\w \-@.]+$/', $d)) {
                    $default = " DEFAULT '" . str_replace("'", "''", $d) . "'";
                } else {
                    // skip unsafe default
                    $default = '';
                }
            }
        }
        $extra = $col['Extra'] ? ' ' . $col['Extra'] : '';
        $colDefs[] = "  `{$name}` {$type} {$null}{$default}{$extra}";
        if (!empty($col['Key'])) {
            if ($col['Key'] === 'PRI') $primary[] = $name;
            if ($col['Key'] === 'UNI') $uniques[] = $name;
            if ($col['Key'] === 'MUL') $keys[] = $name;
        }
    }
    if (!empty($primary)) {
        $colDefs[] = '  PRIMARY KEY (`' . implode('`,`', $primary) . '`)';
    }
    foreach ($uniques as $u) {
        $colDefs[] = '  UNIQUE KEY `uniq_' . $u . '` (`' . $u . '`)';
    }
    foreach ($keys as $k) {
        $colDefs[] = '  KEY `idx_' . $k . '` (`' . $k . '`)';
    }

    $sql .= "DROP TABLE IF EXISTS `{$table}`;\n";
    $sql .= "CREATE TABLE `{$table}` (\n" . implode(",\n", $colDefs) . "\n) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;\n\n";
}

$sql .= "SET FOREIGN_KEY_CHECKS=1;\n";
file_put_contents($outPath, $sql);
fwrite(STDOUT, "Wrote $outPath\n");
return 0;
