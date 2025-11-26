<?php

declare(strict_types=1);

namespace App\Services;

use App\Database;

class CodeGenerationService
{
    private AIManager $aiManager;
    private Database $db;

    public function __construct(AIManager $aiManager, Database $db)
    {
        $this->aiManager = $aiManager;
        $this->db = $db;
    }

    public function generateAppFromPrompt(int $userId, string $projectName, string $prompt, string $aiModel, ?string $appType = null): array
    {
        try {
            // Create project record
            $projectId = $this->db->insert('projects', [
                'user_id' => $userId,
                'title' => $projectName,
                'prompt' => $prompt,
                'ai_model' => $aiModel,
                'status' => 'processing',
            ]);

            // Create prompt record
            $promptId = $this->db->insert('prompts', [
                'project_id' => $projectId,
                'user_id' => $userId,
                'content' => $prompt,
                'ai_model' => $aiModel,
                'status' => 'processing',
            ]);

            // Generate code using AI
            $result = $this->aiManager->generateCode($aiModel, $prompt, $appType);

            if (!$result['success']) {
                $this->db->update('prompts', [
                    'status' => 'failed',
                    'error_message' => $result['error'] ?? 'Unknown error',
                ], 'id = ?', [$promptId]);

                $this->db->update('projects', [
                    'status' => 'failed',
                ], 'id = ?', [$projectId]);

                return [
                    'success' => false,
                    'error' => $result['error'] ?? 'Code generation failed',
                ];
            }

            // Save generated code
            $generatedAppId = $this->db->insert('generated_apps', [
                'project_id' => $projectId,
                'user_id' => $userId,
                'name' => $projectName,
                'code' => $result['code'],
                'language' => $this->detectLanguage($result['code']),
                'framework' => $this->detectFramework($result['code']),
                'version' => '0.1.0',
            ]);

            // Update project with generated code
            $this->db->update('projects', [
                'generated_code' => $result['code'],
                'status' => 'completed',
            ], 'id = ?', [$projectId]);

            // Update prompt record with results
            $this->db->update('prompts', [
                'response' => $result['code'],
                'status' => 'completed',
                'input_tokens' => $result['input_tokens'] ?? 0,
                'response_tokens' => $result['output_tokens'] ?? 0,
                'processing_time' => $result['processing_time'] ?? 0,
            ], 'id = ?', [$promptId]);

            // Save generated app file
            $this->saveGeneratedApp($projectId, $result['code']);

            return [
                'success' => true,
                'project_id' => $projectId,
                'generated_app_id' => $generatedAppId,
                'prompt_id' => $promptId,
                'code' => $result['code'],
                'stats' => [
                    'input_tokens' => $result['input_tokens'] ?? 0,
                    'output_tokens' => $result['output_tokens'] ?? 0,
                    'processing_time' => $result['processing_time'] ?? 0,
                ],
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function regenerateCode(int $projectId, string $newPrompt, string $aiModel): array
    {
        try {
            // Get project
            $project = $this->db->findOne('SELECT * FROM projects WHERE id = ?', [$projectId]);
            if (!$project) {
                return ['success' => false, 'error' => 'Project not found'];
            }

            // Update prompt
            $this->db->update('projects', [
                'prompt' => $newPrompt,
                'status' => 'processing',
            ], 'id = ?', [$projectId]);

            // Create new prompt record
            $promptId = $this->db->insert('prompts', [
                'project_id' => $projectId,
                'user_id' => $project['user_id'],
                'content' => $newPrompt,
                'ai_model' => $aiModel,
                'status' => 'processing',
            ]);

            // Generate code
            $result = $this->aiManager->generateCode($aiModel, $newPrompt);

            if (!$result['success']) {
                return ['success' => false, 'error' => $result['error'] ?? 'Code generation failed'];
            }

            // Create new version
            $versionNumber = $this->getNextVersion($projectId);
            $this->db->insert('project_versions', [
                'project_id' => $projectId,
                'user_id' => $project['user_id'],
                'version' => $versionNumber,
                'code' => $result['code'],
                'change_description' => 'Regenerated from prompt',
            ]);

            // Update project
            $this->db->update('projects', [
                'generated_code' => $result['code'],
                'status' => 'completed',
            ], 'id = ?', [$projectId]);

            // Create new generated app record
            $generatedAppId = $this->db->insert('generated_apps', [
                'project_id' => $projectId,
                'user_id' => $project['user_id'],
                'name' => $project['title'] . ' v' . $versionNumber,
                'code' => $result['code'],
                'version' => $versionNumber,
            ]);

            $this->saveGeneratedApp($projectId, $result['code']);

            return [
                'success' => true,
                'generated_app_id' => $generatedAppId,
                'code' => $result['code'],
                'version' => $versionNumber,
            ];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    public function refineCode(int $projectId, string $refinement): array
    {
        try {
            $project = $this->db->findOne('SELECT * FROM projects WHERE id = ?', [$projectId]);
            if (!$project) {
                return ['success' => false, 'error' => 'Project not found'];
            }

            $refinedPrompt = $project['prompt'] . "\n\nRefinement: " . $refinement;
            return $this->regenerateCode($projectId, $refinedPrompt, $project['ai_model']);
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    private function saveGeneratedApp(int $projectId, string $code): void
    {
        $dir = GENERATED_APPS_PATH . '/' . $projectId;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $file = $dir . '/app_' . date('Y-m-d_His') . '.code';
        file_put_contents($file, $code);
    }

    private function getNextVersion(int $projectId): string
    {
        $latest = $this->db->findOne(
            'SELECT version FROM project_versions WHERE project_id = ? ORDER BY version DESC LIMIT 1',
            [$projectId]
        );

        if (!$latest) {
            return '0.2.0';
        }

        $parts = explode('.', $latest['version']);
        $parts[1]++;
        return implode('.', $parts);
    }

    private function detectLanguage(string $code): string
    {
        if (preg_match('/<[^>]*(html|body|div|script|style)[^>]*>/i', $code)) {
            return 'HTML/CSS/JavaScript';
        }
        if (preg_match('/(import|from)\s+[\'"]react[\'"]|function.*\(\)\s*\{|const.*=>|JSX/i', $code)) {
            return 'JavaScript (React)';
        }
        if (preg_match('/<template>|<script setup>|import.*from.*\.vue/i', $code)) {
            return 'Vue';
        }
        if (preg_match('/import.*from\s+[\'"]angular/i', $code)) {
            return 'TypeScript (Angular)';
        }
        if (preg_match('/^#!\/usr\/bin\/(env\s+)?python/m', $code)) {
            return 'Python';
        }
        if (preg_match('/(const|let|var)\s+\w+\s*=\s*require\(|module\.exports/i', $code)) {
            return 'JavaScript (Node.js)';
        }
        if (preg_match('/<?php/i', $code)) {
            return 'PHP';
        }
        return 'Code';
    }

    private function detectFramework(string $code): string
    {
        if (strpos($code, 'React') !== false || strpos($code, 'react') !== false) {
            return 'React';
        }
        if (strpos($code, 'Vue') !== false || strpos($code, 'vue') !== false) {
            return 'Vue';
        }
        if (strpos($code, 'Angular') !== false || strpos($code, 'angular') !== false) {
            return 'Angular';
        }
        if (strpos($code, 'Express') !== false || strpos($code, 'express') !== false) {
            return 'Express.js';
        }
        if (strpos($code, 'Flask') !== false || strpos($code, 'flask') !== false) {
            return 'Flask';
        }
        return 'None';
    }
}
