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

if (!$slug || !$title) {
    echo "Slug y title son requeridos";
    exit;
}

try {
    $pdo = try_get_pdo();
    if ($pdo) {
        if ($id) {
            $stmt = $pdo->prepare('UPDATE pages SET slug = ?, title = ?, content = ? WHERE id = ?');
            $stmt->execute([$slug, $title, $content, $id]);
        } else {
            $stmt = $pdo->prepare('INSERT INTO pages (slug,title,content) VALUES (?,?,?)');
            $stmt->execute([$slug, $title, $content]);
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
        header('Location: /public/admin/index.php?page=list_pages');
        exit;
    }
} catch (Exception $e) {
    echo 'Error: ' . $e->getMessage();
}
