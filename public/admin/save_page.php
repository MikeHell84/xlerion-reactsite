<?php
require_once __DIR__ . '/../../includes/config.php';
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: /public/admin/index.php');
    exit;
}

 $slug = preg_replace('/[^a-z0-9\-\_]/i','', ($_POST['slug'] ?? ''));
 $title = $_POST['title'] ?? '';
 $content = $_POST['content'] ?? '';
 $id = isset($_POST['id']) ? (int)$_POST['id'] : null;

// CSRF validation (allow header X-CSRF-Token for AJAX if needed)
$csrf = $_POST['csrf_token'] ?? ($_SERVER['HTTP_X_CSRF_TOKEN'] ?? null);
if (!validate_csrf_token($csrf)) {
    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => false, 'error' => 'Invalid CSRF token']);
        exit;
    }
    echo 'Invalid CSRF token';
    exit;
}

if (!$slug || !$title) {
    echo "Slug y title son requeridos";
    exit;
}

try {
    $pdo = try_get_pdo();
    if ($pdo) {
        // Backup current pages table to file before making changes
        try {
            $stmtDump = $pdo->query('SELECT id,slug,title,content,created_at,updated_at FROM pages');
            $all = $stmtDump->fetchAll();
            $dumpFile = __DIR__ . '/../../xlerion-backups/pages_dump_' . date('Ymd_His') . '.json';
            @file_put_contents($dumpFile, json_encode($all, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } catch (Exception $e) {
            // ignore dump errors
        }
        if ($id) {
            $stmt = $pdo->prepare('UPDATE pages SET slug = ?, title = ?, content = ? WHERE id = ?');
            $stmt->execute([$slug, $title, $content, $id]);
            audit_log('page.update', $_SESSION['user']['id'] ?? null, ['id'=>$id,'slug'=>$slug,'title'=>$title]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO pages (slug,title,content) VALUES (?,?,?)');
            $stmt->execute([$slug, $title, $content]);
            $newId = $pdo->lastInsertId();
            audit_log('page.create', $_SESSION['user']['id'] ?? null, ['id'=>$newId,'slug'=>$slug,'title'=>$title]);
        }
        // If AJAX request, return JSON instead of redirect
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            header('Content-Type: application/json; charset=utf-8');
            $retId = $id ?? ($newId ?? null);
            echo json_encode(['ok' => true, 'message' => 'Página guardada', 'id' => $retId, 'slug' => $slug, 'title' => $title]);
            exit;
        }
        header('Location: /public/admin/index.php?page=list_pages');
        exit;
    } else {
        // Fallback to JSON file editing
        $pagesFile = __DIR__ . '/../../data/pages.json';
        $data = [];
        if (file_exists($pagesFile)) {
            $data = json_decode(file_get_contents($pagesFile), true) ?: [];
        }
        // Backup JSON file before modifying
        if (file_exists($pagesFile)) {
            backup_file($pagesFile);
        }
        if ($id) {
            $found = false;
            foreach ($data as &$p) {
                if (isset($p['id']) && $p['id'] == $id) {
                    $p['slug'] = $slug;
                    $p['title'] = $title;
                    $p['content'] = $content;
                    $found = true; break;
                }
            }
            if (!$found) {
                // append
                $data[] = ['id' => $id, 'slug'=>$slug, 'title'=>$title, 'content'=>$content];
            }
        } else {
            $max = 0; foreach ($data as $p) { if (isset($p['id']) && $p['id']>$max) $max=$p['id']; }
            $data[] = ['id' => $max+1, 'slug'=>$slug, 'title'=>$title, 'content'=>$content];
        }
        file_put_contents($pagesFile, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        audit_log('page.create_or_update_fallback', $_SESSION['user']['id'] ?? null, ['id'=>$id ?? null,'slug'=>$slug,'title'=>$title]);
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
            // determine new id for fallback
            $assignedId = $id;
            if (empty($assignedId)) {
                $assignedId = 0; foreach ($data as $p) { if (isset($p['id']) && $p['id']>$assignedId) $assignedId = $p['id']; }
            }
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode(['ok' => true, 'message' => 'Página guardada (fallback JSON)', 'id' => $assignedId, 'slug' => $slug, 'title' => $title]);
            exit;
        }
        header('Location: /public/admin/index.php?page=list_pages');
        exit;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
