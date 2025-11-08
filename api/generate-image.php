<?php
/**
 * AI Image Generation API
 * Supports: Replicate (Stable Diffusion XL), Stability AI
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../db/config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['prompt'])) {
        throw new Exception('Prompt is required');
    }

    $db = db();

    // Get API key from settings
    $apiKeyStmt = $db->prepare("SELECT setting_value FROM settings WHERE setting_key = :key");
    $apiKeyStmt->execute(['key' => 'replicate_api_key']);
    $replicateKey = $apiKeyStmt->fetchColumn();

    $provider = $input['provider'] ?? 'replicate';
    $prompt = $input['prompt'];
    $brand = $input['brand'] ?? 'imerhaba';

    // Enhance prompt with brand context
    $brands = require __DIR__ . '/../config/brands.php';
    $brandData = $brands[$brand] ?? $brands['imerhaba'];

    $enhancedPrompt = enhancePrompt($prompt, $brandData);

    $imageUrl = null;

    if ($provider === 'replicate' && $replicateKey) {
        $imageUrl = generateWithReplicate($enhancedPrompt, $replicateKey);
    } elseif ($provider === 'stability') {
        // Stability AI implementation
        $imageUrl = generateWithStability($enhancedPrompt);
    } else {
        // Fallback: use placeholder service
        $imageUrl = "https://placehold.co/1080x1080/0B132B/D9A441?text=" . urlencode(substr($prompt, 0, 50));
    }

    echo json_encode([
        'success' => true,
        'image_url' => $imageUrl,
        'prompt' => $enhancedPrompt
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function enhancePrompt($prompt, $brandData) {
    $keywords = implode(', ', $brandData['keywords']);
    $mood = $brandData['mood'];

    return "Professional marketing image: $prompt. Style: luxury minimalist, modern corporate design, $mood. Keywords: $keywords. High quality, photorealistic, 8k, professional photography.";
}

function generateWithReplicate($prompt, $apiKey) {
    $ch = curl_init('https://api.replicate.com/v1/predictions');

    $payload = [
        'version' => 'stability-ai/sdxl:39ed52f2a78e934b3ba6e2a89f5b1c712de7dfea535525255b1aa35c5565e08b',
        'input' => [
            'prompt' => $prompt,
            'width' => 1024,
            'height' => 1024,
            'num_outputs' => 1,
            'guidance_scale' => 7.5,
            'num_inference_steps' => 50
        ]
    ];

    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => json_encode($payload),
        CURLOPT_HTTPHEADER => [
            'Authorization: Token ' . $apiKey,
            'Content-Type: application/json'
        ]
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpCode !== 201) {
        throw new Exception('Replicate API error: ' . $response);
    }

    $result = json_decode($response, true);
    $predictionId = $result['id'];

    // Poll for completion
    $imageUrl = pollReplicate($predictionId, $apiKey);

    return $imageUrl;
}

function pollReplicate($predictionId, $apiKey, $maxAttempts = 30) {
    for ($i = 0; $i < $maxAttempts; $i++) {
        sleep(2);

        $ch = curl_init("https://api.replicate.com/v1/predictions/$predictionId");
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Authorization: Token ' . $apiKey,
                'Content-Type: application/json'
            ]
        ]);

        $response = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($response, true);

        if ($result['status'] === 'succeeded') {
            return $result['output'][0] ?? null;
        } elseif ($result['status'] === 'failed') {
            throw new Exception('Image generation failed');
        }
    }

    throw new Exception('Image generation timeout');
}

function generateWithStability($prompt) {
    // Stability AI implementation (requires API key)
    // For now, return placeholder
    return "https://placehold.co/1024x1024/0B132B/D9A441?text=Stability+AI";
}
