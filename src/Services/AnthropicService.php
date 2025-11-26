<?php

declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class AnthropicService extends AIService
{
    private Client $client;
    private const ANTHROPIC_API_VERSION = '2023-06-01';

    public function __construct(string $apiKey, string $model = 'claude-3-5-sonnet-20241022', int $maxTokens = 4000, float $temperature = 0.7)
    {
        parent::__construct($apiKey, $model, $maxTokens, $temperature);
        $this->initializeClient();
    }

    private function initializeClient(): void
    {
        $this->client = new Client([
            'base_uri' => 'https://api.anthropic.com/v1/',
            'headers' => [
                'x-api-key' => $this->apiKey,
                'anthropic-version' => self::ANTHROPIC_API_VERSION,
                'Content-Type' => 'application/json',
            ],
            'timeout' => 120,
        ]);
    }

    public function generateCode(string $prompt, ?array $systemPrompt = null): array
    {
        $system = $systemPrompt ?? [$this->getWebAppSystemPrompt()];
        if (!is_array($system)) {
            $system = [$system];
        }

        try {
            $startTime = microtime(true);
            $response = $this->client->post('messages', [
                'json' => [
                    'model' => $this->model,
                    'max_tokens' => $this->maxTokens,
                    'system' => implode("\n\n", $system),
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => $prompt,
                        ],
                    ],
                    'temperature' => $this->temperature,
                ],
            ]);

            $endTime = microtime(true);
            $processingTime = (int)(($endTime - $startTime) * 1000);

            $body = json_decode($response->getBody()->getContents(), true);

            $code = '';
            if (isset($body['content']) && is_array($body['content'])) {
                foreach ($body['content'] as $block) {
                    if ($block['type'] === 'text') {
                        $code .= $block['text'];
                    }
                }
            }

            return [
                'success' => true,
                'code' => $code,
                'model' => $this->model,
                'input_tokens' => $body['usage']['input_tokens'] ?? 0,
                'output_tokens' => $body['usage']['output_tokens'] ?? 0,
                'total_tokens' => ($body['usage']['input_tokens'] ?? 0) + ($body['usage']['output_tokens'] ?? 0),
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
            // Try a simple message to validate the API key
            $response = $this->client->post('messages', [
                'json' => [
                    'model' => $this->model,
                    'max_tokens' => 10,
                    'messages' => [
                        [
                            'role' => 'user',
                            'content' => 'test',
                        ],
                    ],
                ],
            ]);
            return $response->getStatusCode() === 200;
        } catch (\Exception) {
            return false;
        }
    }
}
