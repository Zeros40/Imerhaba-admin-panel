<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../db/config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input) {
        throw new Exception('Invalid JSON input');
    }

    $db = db();
    $stmt = $db->prepare("
        INSERT INTO landing_pages (
            name, brand, slug, page_data, seo_title, seo_description
        ) VALUES (
            :name, :brand, :slug, :page_data, :seo_title, :seo_description
        )
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            brand = VALUES(brand),
            page_data = VALUES(page_data),
            seo_title = VALUES(seo_title),
            seo_description = VALUES(seo_description),
            updated_at = CURRENT_TIMESTAMP
    ");

    $stmt->execute([
        'name' => $input['name'] ?? 'Untitled Page',
        'brand' => $input['brand'] ?? 'imerhaba',
        'slug' => $input['slug'] ?? 'page-' . time(),
        'page_data' => json_encode([
            'html' => $input['html'] ?? '',
            'sections' => $input['sections'] ?? []
        ]),
        'seo_title' => $input['seo_title'] ?? '',
        'seo_description' => $input['seo_description'] ?? ''
    ]);

    $pageId = $db->lastInsertId();

    echo json_encode([
        'success' => true,
        'page_id' => $pageId,
        'url' => '/page/' . ($input['slug'] ?? 'page-' . time()),
        'message' => 'Page saved successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
