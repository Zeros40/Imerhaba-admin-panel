<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Services\CodeGenerationService;

class GenerationController
{
    private CodeGenerationService $generationService;

    public function __construct(CodeGenerationService $generationService)
    {
        $this->generationService = $generationService;
    }

    public function generateApp(Request $request, ?string $projectId = null): Response
    {
        if (!$request->isPost()) {
            return Response::error('Method not allowed', 405);
        }

        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $errors = $request->validate([
            'project_name' => 'required|max:255',
            'prompt' => 'required',
            'ai_model' => 'required',
        ]);

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $result = $this->generationService->generateAppFromPrompt(
            userId: $user['id'],
            projectName: $request->input('project_name'),
            prompt: $request->input('prompt'),
            aiModel: $request->input('ai_model'),
            appType: $request->input('app_type')
        );

        if (!$result['success']) {
            return Response::error($result['error'], 400);
        }

        return Response::created($result, 'App generated successfully');
    }

    public function regenerate(Request $request, string $projectId): Response
    {
        if (!$request->isPost()) {
            return Response::error('Method not allowed', 405);
        }

        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $errors = $request->validate([
            'prompt' => 'required',
            'ai_model' => 'required',
        ]);

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $result = $this->generationService->regenerateCode(
            (int)$projectId,
            $request->input('prompt'),
            $request->input('ai_model')
        );

        if (!$result['success']) {
            return Response::error($result['error'], 400);
        }

        return Response::success($result, 200, 'Code regenerated successfully');
    }

    public function refine(Request $request, string $projectId): Response
    {
        if (!$request->isPost()) {
            return Response::error('Method not allowed', 405);
        }

        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $errors = $request->validate([
            'refinement' => 'required',
        ]);

        if (!empty($errors)) {
            return Response::error('Validation failed', 422, $errors);
        }

        $result = $this->generationService->refineCode(
            (int)$projectId,
            $request->input('refinement')
        );

        if (!$result['success']) {
            return Response::error($result['error'], 400);
        }

        return Response::success($result, 200, 'Code refined successfully');
    }
}
