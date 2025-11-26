<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class OpenAIService extends AIService
{
    private Client $client;
    private ?string $organizationId;

    public function __construct(string $apiKey, string $model = 'gpt-4', ?string $organizationId = null, int $maxTokens = 4000, float $temperature = 0.7)
    {
        parent::__construct($apiKey, $model, $maxTokens, $temperature);
        $this->organizationId = $organizationId;
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $headers = [
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json',
        ];

        if ($this->organizationId) {
            $headers['OpenAI-Organization'] = $this->organizationId;
        }

        $this->client = new Client([
            'base_uri' => 'https://api.openai.com/v1/',
            'headers' => $headers,
            'timeout' => 120,
        ]);
    }

    public function generateCode(string $prompt, ?array $systemPrompt = null): array
    {
        $system = $systemPrompt ?? $this->getWebAppSystemPrompt();

        try {
            $startTime = microtime(true);
            $response = $this->client->post('chat/completions', [
                'json' => [
                    'model' => $this->model,
                    'messages' => [
                        [
                            'role' => 'system',
                            'content' => $system,
                        ],
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => $this->temperature,
                    'max_tokens' => $this->maxTokens,
                ],
            ]);

            $endTime = microtime(true);
            $processingTime = (int)(($endTime - $startTime) * 1000);

            $body = json_decode($response->getBody()->getContents(), true);

            return [
                'success' => true,
                'code' => $body['choices'][0]['message']['content'] ?? '',
                'model' => $this->model,
                'input_tokens' => $body['usage']['prompt_tokens'] ?? 0,
                'output_tokens' => $body['usage']['completion_tokens'] ?? 0,
                'total_tokens' => $body['usage']['total_tokens'] ?? 0,
                'processing_time' => $processingTime,
            ];
        } catch (GuzzleException $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => '',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
                'code' => '',
            ];
        }
    }

    public function generateCodeForApp(string $appDescription, string $appType = 'web'): array
    {
        $systemPrompt = $this->createSystemPrompt($appType);
        $prompt = "Create a complete $appType application based on this description:\n\n$appDescription";
        return $this->generateCode($prompt, [$systemPrompt]);
    }

    public function validateApiKey(): bool
    {
        try {
            $response = $this->client->get('models');
            return $response->getStatusCode() === 200;
        } catch (\Exception) {
            return false;
        }
    }

    public function listAvailableModels(): array
    {
        try {
            $response = $this->client->get('models');
            $body = json_decode($response->getBody()->getContents(), true);
            return $body['data'] ?? [];
        } catch (\Exception) {
            return [];
        }
    }
}
