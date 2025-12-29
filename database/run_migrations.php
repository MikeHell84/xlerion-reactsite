<?php
// Migration runner - executes SQL files in database/migrations
require_once __DIR__ . '/../includes/config.php';

function runMigrations(){
    // Try configured PDO (MySQL). If it fails, fall back to a local SQLite file for development/testing.
    try {
        $pdo = get_pdo();
    } catch (Exception $e) {
        echo "MySQL connection failed: " . $e->getMessage() . "\n";
        echo "Falling back to local SQLite database at database/xlerion_dev.sqlite\n";
        $sqlitePath = __DIR__ . '/xlerion_dev.sqlite';
        $pdo = new PDO('sqlite:' . $sqlitePath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    }
    // create a migrations table if not present with our expected schema
    $pdo->exec("CREATE TABLE IF NOT EXISTS migrations (id INT AUTO_INCREMENT PRIMARY KEY, filename VARCHAR(255) NOT NULL UNIQUE, applied_at DATETIME NOT NULL)");

    // detect existing migrations table schema
    $colsStmt = $pdo->query("SHOW COLUMNS FROM migrations");
    $cols = array_map(function($c){ return $c['Field']; }, $colsStmt->fetchAll(PDO::FETCH_ASSOC));
    $usesFilename = in_array('filename', $cols);
    $usesMigration = in_array('migration', $cols);

    $files = glob(__DIR__ . '/migrations/*.sql');
    sort($files);
    foreach($files as $file){
        $name = basename($file);
        if($usesFilename){
            $stmt = $pdo->prepare('SELECT COUNT(*) as c FROM migrations WHERE filename = ?');
            $stmt->execute([$name]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row && $row['c'] > 0) continue; // already applied
        }elseif($usesMigration){
            $stmt = $pdo->prepare('SELECT COUNT(*) as c FROM migrations WHERE migration = ?');
            $stmt->execute([$name]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            if($row && $row['c'] > 0) continue;
        }

        echo "Applying: $name\n";
        $sql = file_get_contents($file);
        try{
            $pdo->exec($sql);
            if($usesFilename){
                $ins = $pdo->prepare('INSERT INTO migrations (filename, applied_at) VALUES (?, ?)');
                $ins->execute([$name, date('Y-m-d H:i:s')]);
            }elseif($usesMigration){
                $batchRow = $pdo->query('SELECT MAX(batch) as b FROM migrations')->fetch(PDO::FETCH_ASSOC);
                $nextBatch = (($batchRow && $batchRow['b']) ? (int)$batchRow['b'] + 1 : 1);
                $ins = $pdo->prepare('INSERT INTO migrations (migration, batch) VALUES (?, ?)');
                $ins->execute([$name, $nextBatch]);
            }
            echo "Applied: $name\n";
        }catch(Exception $e){
            echo "Failed: $name -> " . $e->getMessage() . "\n";
            return false;
        }
    }
    return true;
}

if(PHP_SAPI === 'cli'){
    $ok = runMigrations();
    exit($ok?0:1);
}

?>
