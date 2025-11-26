<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Database;

class ProjectController
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function listProjects(Request $request): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $page = (int)($request->input('page', 1) ?? 1);
        $limit = (int)($request->input('limit', 10) ?? 10);
        $offset = ($page - 1) * $limit;

        $projects = $this->db->findAll(
            'SELECT id, title, description, ai_model, status, created_at, updated_at
             FROM projects WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?',
            [$user['id'], $limit, $offset]
        );

        $total = $this->db->findOne(
            'SELECT COUNT(*) as count FROM projects WHERE user_id = ?',
            [$user['id']]
        );

        return Response::success([
            'projects' => $projects,
            'pagination' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total['count'] ?? 0,
            ],
        ]);
    }

    public function getProject(Request $request, string $projectId): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $project = $this->db->findOne(
            'SELECT * FROM projects WHERE id = ? AND user_id = ?',
            [$projectId, $user['id']]
        );

        if (!$project) {
            return Response::notFound('Project not found');
        }

        // Get generated apps
        $apps = $this->db->findAll(
            'SELECT id, name, version, language, framework, created_at FROM generated_apps WHERE project_id = ? ORDER BY created_at DESC',
            [$projectId]
        );

        // Get prompts
        $prompts = $this->db->findAll(
            'SELECT id, content, ai_model, status, created_at FROM prompts WHERE project_id = ? ORDER BY created_at DESC',
            [$projectId]
        );

        $project['generated_apps'] = $apps;
        $project['prompts'] = $prompts;

        return Response::success($project);
    }

    public function updateProject(Request $request, string $projectId): Response
    {
        if (!$request->isPut()) {
            return Response::error('Method not allowed', 405);
        }

        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $project = $this->db->findOne(
            'SELECT id FROM projects WHERE id = ? AND user_id = ?',
            [$projectId, $user['id']]
        );

        if (!$project) {
            return Response::notFound('Project not found');
        }

        $data = [];
        if ($request->input('title')) {
            $data['title'] = $request->input('title');
        }
        if ($request->input('description') !== null) {
            $data['description'] = $request->input('description');
        }
        if ($request->input('is_public') !== null) {
            $data['is_public'] = $request->input('is_public') ? 1 : 0;
        }

        if (empty($data)) {
            return Response::badRequest('No data to update');
        }

        $this->db->update('projects', $data, 'id = ?', [$projectId]);

        return Response::success(null, 200, 'Project updated successfully');
    }

    public function deleteProject(Request $request, string $projectId): Response
    {
        if (!$request->isDelete()) {
            return Response::error('Method not allowed', 405);
        }

        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $project = $this->db->findOne(
            'SELECT id FROM projects WHERE id = ? AND user_id = ?',
            [$projectId, $user['id']]
        );

        if (!$project) {
            return Response::notFound('Project not found');
        }

        $this->db->delete('projects', 'id = ?', [$projectId]);

        return Response::success(null, 200, 'Project deleted successfully');
    }

    public function exportProject(Request $request, string $projectId): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $project = $this->db->findOne(
            'SELECT * FROM projects WHERE id = ? AND user_id = ?',
            [$projectId, $user['id']]
        );

        if (!$project) {
            return Response::notFound('Project not found');
        }

        $generatedApp = $this->db->findOne(
            'SELECT code FROM generated_apps WHERE project_id = ? ORDER BY created_at DESC LIMIT 1',
            [$projectId]
        );

        return Response::success([
            'project' => $project,
            'code' => $generatedApp['code'] ?? null,
        ]);
    }
}
