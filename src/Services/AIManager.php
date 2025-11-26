<?php

declare(strict_types=1);

namespace App\Services;

class AIManager
{
    private array $config;
    private array $services = [];

    public function __construct(array $config)
    {
        $this->config = $config;
    }

    public function getService(string $model): AIService
    {
        if (isset($this->services[$model])) {
            return $this->services[$model];
        }

        $service = match (true) {
            str_contains($model, 'gpt-4') || str_contains($model, 'o1') => $this->createOpenAIService($model),
            str_contains($model, 'claude') => $this->createAnthropicService($model),
            default => throw new \InvalidArgumentException("Unsupported model: $model"),
        };

        $this->services[$model] = $service;
        return $service;
    }

    private function createOpenAIService(string $model): OpenAIService
    {
        return new OpenAIService(
            apiKey: $this->config['openai']['api_key'],
            model: $model,
            organizationId: $this->config['openai']['org_id'] ?? null,
            maxTokens: $this->config['generation']['max_tokens'] ?? 4000,
            temperature: $this->config['generation']['temperature'] ?? 0.7,
        );
    }

    private function createAnthropicService(string $model): AnthropicService
    {
        return new AnthropicService(
            apiKey: $this->config['anthropic']['api_key'],
            model: $model,
            maxTokens: $this->config['generation']['max_tokens'] ?? 4000,
            temperature: $this->config['generation']['temperature'] ?? 0.7,
        );
    }

    public function generateCode(string $model, string $prompt, ?string $appType = null): array
    {
        $service = $this->getService($model);

        if ($appType) {
            return $service->generateCodeForApp($prompt, $appType);
        }

        return $service->generateCode($prompt);
    }

    public function getAvailableModels(): array
    {
        return [
            'gpt-4' => 'GPT-4 (OpenAI)',
            'gpt-4-turbo' => 'GPT-4 Turbo (OpenAI)',
            'claude-3-5-sonnet-20241022' => 'Claude 3.5 Sonnet (Anthropic)',
            'o1' => 'OpenAI o1',
            'o1-mini' => 'OpenAI o1-mini',
        ];
    }

    public function validateApiKeys(): array
    {
        $results = [];

        // Validate OpenAI
        if (!empty($this->config['openai']['api_key'])) {
            try {
                $openaiService = new OpenAIService($this->config['openai']['api_key']);
                $results['openai'] = $openaiService->validateApiKey();
            } catch (\Exception) {
                $results['openai'] = false;
            }
        }

        // Validate Anthropic
        if (!empty($this->config['anthropic']['api_key'])) {
            try {
                $anthropicService = new AnthropicService($this->config['anthropic']['api_key']);
                $results['anthropic'] = $anthropicService->validateApiKey();
            } catch (\Exception) {
                $results['anthropic'] = false;
            }
        }

        return $results;
    }
}
