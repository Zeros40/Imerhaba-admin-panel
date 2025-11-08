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
        INSERT INTO designs (
            brand, title, description, headline, subtext, cta_text,
            ratio, language, theme, design_data, status
        ) VALUES (
            :brand, :title, :description, :headline, :subtext, :cta_text,
            :ratio, :language, :theme, :design_data, 'generated'
        )
    ");

    $stmt->execute([
        'brand' => $input['brand'] ?? '',
        'title' => $input['title'] ?? '',
        'description' => $input['description'] ?? '',
        'headline' => $input['headline'] ?? '',
        'subtext' => $input['subtext'] ?? '',
        'cta_text' => $input['cta_text'] ?? '',
        'ratio' => $input['ratio'] ?? '1:1',
        'language' => $input['language'] ?? 'EN',
        'theme' => $input['theme'] ?? 'default',
        'design_data' => json_encode($input['design_data'] ?? [])
    ]);

    $designId = $db->lastInsertId();

    echo json_encode([
        'success' => true,
        'design_id' => $designId,
        'message' => 'Design saved successfully'
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
