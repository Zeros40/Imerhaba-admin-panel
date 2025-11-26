<?php

declare(strict_types=1);

return [
    'up' => function ($pdo) {
        // Users table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS users (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(255) NOT NULL UNIQUE,
                email VARCHAR(255) NOT NULL UNIQUE,
                password VARCHAR(255) NOT NULL,
                first_name VARCHAR(100),
                last_name VARCHAR(100),
                avatar_url VARCHAR(500),
                bio TEXT,
                plan VARCHAR(50) DEFAULT 'free',
                is_active BOOLEAN DEFAULT TRUE,
                email_verified BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                INDEX idx_email (email),
                INDEX idx_username (username),
                INDEX idx_plan (plan)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Projects table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS projects (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                description TEXT,
                prompt TEXT NOT NULL,
                ai_model VARCHAR(100) NOT NULL,
                technology_stack VARCHAR(255),
                is_public BOOLEAN DEFAULT FALSE,
                is_template BOOLEAN DEFAULT FALSE,
                status VARCHAR(50) DEFAULT 'draft',
                generated_code LONGTEXT,
                features JSON,
                tags JSON,
                views_count INT DEFAULT 0,
                likes_count INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_status (status),
                INDEX idx_created_at (created_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Prompts table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS prompts (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                user_id INT NOT NULL,
                content TEXT NOT NULL,
                ai_model VARCHAR(100) NOT NULL,
                temperature DECIMAL(3,2) DEFAULT 0.7,
                max_tokens INT DEFAULT 4000,
                response TEXT,
                response_tokens INT,
                input_tokens INT,
                processing_time INT,
                status VARCHAR(50) DEFAULT 'pending',
                error_message TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_project_id (project_id),
                INDEX idx_user_id (user_id),
                INDEX idx_status (status)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Generated Apps table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS generated_apps (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                user_id INT NOT NULL,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                code LONGTEXT NOT NULL,
                language VARCHAR(50),
                framework VARCHAR(100),
                dependencies JSON,
                version VARCHAR(50) DEFAULT '0.1.0',
                file_path VARCHAR(500),
                is_active BOOLEAN DEFAULT TRUE,
                execution_count INT DEFAULT 0,
                last_executed_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_project_id (project_id),
                INDEX idx_user_id (user_id)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // API Keys table (for managing user API keys)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS api_keys (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                api_provider VARCHAR(100) NOT NULL,
                encrypted_key TEXT NOT NULL,
                key_name VARCHAR(255),
                is_active BOOLEAN DEFAULT TRUE,
                last_used_at TIMESTAMP NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_provider (api_provider)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Project Versions table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS project_versions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                project_id INT NOT NULL,
                user_id INT NOT NULL,
                version VARCHAR(50) NOT NULL,
                code LONGTEXT NOT NULL,
                change_description TEXT,
                is_published BOOLEAN DEFAULT FALSE,
                download_count INT DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (project_id) REFERENCES projects(id) ON DELETE CASCADE,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_project_id (project_id),
                UNIQUE KEY unique_version (project_id, version)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // User Sessions table (for JWT token tracking)
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS user_sessions (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                token_hash VARCHAR(255) NOT NULL UNIQUE,
                device_info VARCHAR(255),
                ip_address VARCHAR(45),
                user_agent VARCHAR(500),
                expires_at TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_expires_at (expires_at)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Usage Stats table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS usage_stats (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                ai_model VARCHAR(100),
                input_tokens INT DEFAULT 0,
                output_tokens INT DEFAULT 0,
                total_requests INT DEFAULT 0,
                cost DECIMAL(10,4) DEFAULT 0,
                month VARCHAR(7),
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
                INDEX idx_user_id (user_id),
                INDEX idx_month (month),
                UNIQUE KEY unique_user_month (user_id, month)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Templates table
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS templates (
                id INT AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(255) NOT NULL,
                description TEXT,
                category VARCHAR(100),
                language VARCHAR(50),
                framework VARCHAR(100),
                code LONGTEXT NOT NULL,
                preview_image_url VARCHAR(500),
                author_id INT,
                is_featured BOOLEAN DEFAULT FALSE,
                usage_count INT DEFAULT 0,
                rating DECIMAL(3,2) DEFAULT 0,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                FOREIGN KEY (author_id) REFERENCES users(id) ON DELETE SET NULL,
                INDEX idx_category (category),
                INDEX idx_language (language)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        echo "✓ All tables created successfully\n";
    },

    'down' => function ($pdo) {
        $tables = [
            'usage_stats',
            'user_sessions',
            'project_versions',
            'api_keys',
            'generated_apps',
            'prompts',
            'projects',
            'templates',
            'users'
        ];

        foreach ($tables as $table) {
            $pdo->exec("DROP TABLE IF EXISTS $table");
        }

        echo "✓ All tables dropped successfully\n";
    }
];
