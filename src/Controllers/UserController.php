<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Request;
use App\Response;
use App\Database;

class UserController
{
    private Database $db;

    public function __construct(Database $db)
    {
        $this->db = $db;
    }

    public function getProfile(Request $request): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $profile = $this->db->findOne(
            'SELECT id, username, email, first_name, last_name, avatar_url, bio, plan, created_at, updated_at
             FROM users WHERE id = ?',
            [$user['id']]
        );

        if (!$profile) {
            return Response::notFound('User not found');
        }

        // Get usage statistics
        $stats = $this->db->findOne(
            'SELECT SUM(total_requests) as total_requests, SUM(input_tokens) as input_tokens,
                    SUM(output_tokens) as output_tokens, SUM(cost) as total_cost
             FROM usage_stats WHERE user_id = ?',
            [$user['id']]
        );

        $profile['stats'] = $stats ?? [
            'total_requests' => 0,
            'input_tokens' => 0,
            'output_tokens' => 0,
            'total_cost' => 0,
        ];

        return Response::success($profile);
    }

    public function updateProfile(Request $request): Response
    {
        if (!$request->isPut()) {
            return Response::error('Method not allowed', 405);
        }

        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $data = [];

        if ($request->input('first_name') !== null) {
            $data['first_name'] = $request->input('first_name');
        }
        if ($request->input('last_name') !== null) {
            $data['last_name'] = $request->input('last_name');
        }
        if ($request->input('bio') !== null) {
            $data['bio'] = $request->input('bio');
        }

        if (empty($data)) {
            return Response::badRequest('No data to update');
        }

        $this->db->update('users', $data, 'id = ?', [$user['id']]);

        return Response::success(null, 200, 'Profile updated successfully');
    }

    public function getUsageStats(Request $request): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $month = $request->input('month', date('Y-m'));

        $stats = $this->db->findOne(
            'SELECT * FROM usage_stats WHERE user_id = ? AND month = ?',
            [$user['id'], $month]
        );

        if (!$stats) {
            $stats = [
                'user_id' => $user['id'],
                'month' => $month,
                'total_requests' => 0,
                'input_tokens' => 0,
                'output_tokens' => 0,
                'cost' => 0,
            ];
        }

        return Response::success($stats);
    }

    public function getRecentProjects(Request $request): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $limit = (int)($request->input('limit', 5) ?? 5);

        $projects = $this->db->findAll(
            'SELECT id, title, ai_model, status, created_at
             FROM projects WHERE user_id = ? ORDER BY created_at DESC LIMIT ?',
            [$user['id'], $limit]
        );

        return Response::success($projects);
    }

    public function getStats(Request $request): Response
    {
        $user = $request->getAttribute('user');
        if (!$user) {
            return Response::unauthorized();
        }

        $stats = [
            'total_projects' => 0,
            'total_generations' => 0,
            'total_tokens_used' => 0,
            'favorite_model' => null,
        ];

        // Total projects
        $projects = $this->db->findOne(
            'SELECT COUNT(*) as count FROM projects WHERE user_id = ?',
            [$user['id']]
        );
        $stats['total_projects'] = $projects['count'] ?? 0;

        // Total generations
        $generations = $this->db->findOne(
            'SELECT COUNT(*) as count FROM generated_apps WHERE user_id = ?',
            [$user['id']]
        );
        $stats['total_generations'] = $generations['count'] ?? 0;

        // Total tokens
        $tokens = $this->db->findOne(
            'SELECT SUM(input_tokens) + SUM(output_tokens) as total FROM prompts WHERE user_id = ?',
            [$user['id']]
        );
        $stats['total_tokens_used'] = (int)($tokens['total'] ?? 0);

        // Favorite model
        $model = $this->db->findOne(
            'SELECT ai_model, COUNT(*) as count FROM projects WHERE user_id = ? GROUP BY ai_model ORDER BY count DESC LIMIT 1',
            [$user['id']]
        );
        $stats['favorite_model'] = $model['ai_model'] ?? null;

        return Response::success($stats);
    }
}
