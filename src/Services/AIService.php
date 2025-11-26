<?php

declare(strict_types=1);

namespace App\Services;

abstract class AIService
{
    protected string $apiKey;
    protected string $model;
    protected int $maxTokens;
    protected float $temperature;

    public function __construct(string $apiKey, string $model, int $maxTokens = 4000, float $temperature = 0.7)
    {
        $this->apiKey = $apiKey;
        $this->model = $model;
        $this->maxTokens = $maxTokens;
        $this->temperature = $temperature;
    }

    abstract public function generateCode(string $prompt, ?array $systemPrompt = null): array;

    abstract public function generateCodeForApp(string $appDescription, string $appType = 'web'): array;

    public function setModel(string $model): self
    {
        $this->model = $model;
        return $this;
    }

    public function setMaxTokens(int $maxTokens): self
    {
        $this->maxTokens = $maxTokens;
        return $this;
    }

    public function setTemperature(float $temperature): self
    {
        $this->temperature = max(0, min(1, $temperature));
        return $this;
    }

    protected function createSystemPrompt(string $appType = 'web'): string
    {
        return match($appType) {
            'react' => $this->getReactSystemPrompt(),
            'vue' => $this->getVueSystemPrompt(),
            'angular' => $this->getAngularSystemPrompt(),
            'api' => $this->getAPISystemPrompt(),
            'cli' => $this->getCLISystemPrompt(),
            default => $this->getWebAppSystemPrompt(),
        };
    }

    protected function getWebAppSystemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert full-stack web developer. Generate complete, production-ready HTML, CSS, and JavaScript code for web applications.
Requirements:
1. Use modern HTML5 semantic markup
2. Write clean, maintainable CSS with proper styling
3. Include responsive design principles
4. Use vanilla JavaScript (no external dependencies unless specified)
5. Include comments explaining key functionality
6. Ensure accessibility best practices
7. Return only valid, executable code
PROMPT;
    }

    protected function getReactSystemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert React developer. Generate complete, production-ready React components and applications.
Requirements:
1. Use functional components with hooks
2. Follow React best practices and conventions
3. Include proper state management
4. Use modern JavaScript (ES6+)
5. Include comments explaining component logic
6. Return only valid JSX code
7. Include necessary imports
PROMPT;
    }

    protected function getVueSystemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert Vue.js developer. Generate complete, production-ready Vue components and applications.
Requirements:
1. Use Vue 3 Composition API
2. Follow Vue best practices and conventions
3. Include proper state management
4. Use modern JavaScript (ES6+)
5. Include comments explaining component logic
6. Return only valid Vue code
7. Include necessary imports
PROMPT;
    }

    protected function getAngularSystemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert Angular developer. Generate complete, production-ready Angular components and services.
Requirements:
1. Use Angular best practices
2. Include TypeScript typing
3. Follow Angular style guide
4. Include dependency injection
5. Add proper error handling
6. Return only valid Angular code
7. Include necessary imports
PROMPT;
    }

    protected function getAPISystemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert backend API developer. Generate complete, production-ready API code.
Requirements:
1. Use RESTful API design principles
2. Include proper error handling
3. Add request validation
4. Include database models and migrations
5. Add authentication/authorization if needed
6. Use modern best practices
7. Return only valid, executable code
PROMPT;
    }

    protected function getCLISystemPrompt(): string
    {
        return <<<'PROMPT'
You are an expert CLI application developer. Generate complete, production-ready CLI applications.
Requirements:
1. Use argparse or similar for argument parsing
2. Include proper error handling
3. Add user-friendly output formatting
4. Include help documentation
5. Use modern best practices
6. Return only valid, executable code
7. Include necessary imports
PROMPT;
    }
}
