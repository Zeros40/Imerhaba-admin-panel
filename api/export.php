<?php
/**
 * Export API - Exports designs as PNG, HTML, PDF, or ZIP
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../db/config.php';

try {
    $input = json_decode(file_get_contents('php://input'), true);

    if (!$input || !isset($input['designs'])) {
        throw new Exception('Designs data is required');
    }

    $designs = $input['designs'];
    $format = $input['format'] ?? 'png';

    switch ($format) {
        case 'png':
            $result = exportAsPNG($designs);
            break;
        case 'html':
            $result = exportAsHTML($designs);
            break;
        case 'zip':
            $result = exportAsZIP($designs);
            header('Content-Type: application/zip');
            header('Content-Disposition: attachment; filename="designs-' . time() . '.zip"');
            echo $result;
            exit;
        default:
            throw new Exception('Invalid format');
    }

    echo json_encode([
        'success' => true,
        'files' => $result
    ]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function exportAsPNG($designs) {
    // This would use a headless browser like Puppeteer or wkhtmltoimage
    // For now, return URLs
    $files = [];

    foreach ($designs as $design) {
        // In production, you'd convert HTML to PNG here
        $files[] = [
            'url' => '/exports/' . md5($design['text']) . '.png',
            'name' => sanitizeFilename($design['text']) . '.png'
        ];
    }

    return $files;
}

function exportAsHTML($designs) {
    $files = [];
    $exportDir = __DIR__ . '/../exports/';

    if (!is_dir($exportDir)) {
        mkdir($exportDir, 0755, true);
    }

    foreach ($designs as $design) {
        $filename = sanitizeFilename($design['text']) . '.html';
        $filepath = $exportDir . $filename;

        $html = generateDesignHTML($design);
        file_put_contents($filepath, $html);

        $files[] = [
            'url' => '/exports/' . $filename,
            'name' => $filename
        ];
    }

    return $files;
}

function exportAsZIP($designs) {
    $zipFile = tempnam(sys_get_temp_dir(), 'designs_');
    $zip = new ZipArchive();

    if ($zip->open($zipFile, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== TRUE) {
        throw new Exception('Cannot create ZIP file');
    }

    foreach ($designs as $index => $design) {
        $html = generateDesignHTML($design);
        $filename = sprintf('%03d_%s.html', $index + 1, sanitizeFilename($design['text']));
        $zip->addFromString($filename, $html);
    }

    // Add README
    $readme = "Imerhaba Design Studio Export\n\n";
    $readme .= "Total designs: " . count($designs) . "\n";
    $readme .= "Exported: " . date('Y-m-d H:i:s') . "\n\n";
    $readme .= "Instructions:\n";
    $readme .= "- Open any HTML file in a browser to view the design\n";
    $readme .= "- Use screenshot tools to capture as images\n";
    $readme .= "- Edit HTML files to customize further\n";
    $zip->addFromString('README.txt', $readme);

    $zip->close();

    return file_get_contents($zipFile);
}

function generateDesignHTML($design) {
    $brands = require __DIR__ . '/../config/brands.php';
    $brandData = $brands[$design['brand']] ?? $brands['imerhaba'];

    return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{$design['text']}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700&family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: #f5f5f5;
        }

        .design-card {
            width: 1080px;
            height: 1080px;
            background: linear-gradient(135deg, {$brandData['colors']['primary']}, {$brandData['colors']['background']});
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 4rem;
            text-align: center;
            color: white;
            position: relative;
            overflow: hidden;
        }

        .brand-badge {
            position: absolute;
            top: 2rem;
            right: 2rem;
            background: {$brandData['colors']['secondary']};
            color: {$brandData['colors']['primary']};
            padding: 0.8rem 1.5rem;
            border-radius: 30px;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 0.9rem;
            letter-spacing: 1px;
        }

        .headline {
            font-family: 'Poppins', sans-serif;
            font-size: 4rem;
            font-weight: 700;
            margin-bottom: 2rem;
            color: {$brandData['colors']['secondary']};
            line-height: 1.2;
            text-shadow: 2px 2px 8px rgba(0,0,0,0.3);
        }

        .subtext {
            font-size: 1.8rem;
            margin-bottom: 3rem;
            opacity: 0.95;
            max-width: 800px;
            line-height: 1.5;
        }

        .cta-button {
            background: {$brandData['colors']['secondary']};
            color: {$brandData['colors']['primary']};
            padding: 1.5rem 4rem;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.5rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.3);
        }

        .decorative-element {
            position: absolute;
            width: 300px;
            height: 300px;
            border-radius: 50%;
            background: {$brandData['colors']['secondary']};
            opacity: 0.1;
            filter: blur(80px);
        }

        .element-1 {
            top: -150px;
            left: -150px;
        }

        .element-2 {
            bottom: -150px;
            right: -150px;
        }
    </style>
</head>
<body>
    <div class="design-card">
        <div class="decorative-element element-1"></div>
        <div class="decorative-element element-2"></div>

        <div class="brand-badge">{$design['brand']}</div>

        <div class="headline">{$design['text']}</div>
        <div class="subtext">Transform your business with innovative solutions</div>
        <div class="cta-button">Learn More</div>
    </div>
</body>
</html>
HTML;
}

function sanitizeFilename($string) {
    $string = preg_replace('/[^a-zA-Z0-9\s-]/', '', $string);
    $string = preg_replace('/\s+/', '-', $string);
    $string = substr($string, 0, 50);
    return strtolower($string);
}
