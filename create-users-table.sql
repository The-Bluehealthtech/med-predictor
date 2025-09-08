-- Création de la table users pour Laravel
USE med_predictor;

CREATE TABLE IF NOT EXISTS users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    remember_token VARCHAR(100) NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    role VARCHAR(50) DEFAULT 'user',
    association_id BIGINT UNSIGNED NULL,
    club_id BIGINT UNSIGNED NULL,
    player_id BIGINT UNSIGNED NULL,
    team_id BIGINT UNSIGNED NULL,
    last_login_at TIMESTAMP NULL,
    phone VARCHAR(20) NULL,
    preferences JSON NULL,
    INDEX idx_users_email (email),
    INDEX idx_users_association_id (association_id),
    INDEX idx_users_club_id (club_id),
    INDEX idx_users_player_id (player_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création d'un utilisateur admin par défaut
INSERT INTO users (name, email, password, role, created_at, updated_at) VALUES 
('Admin', 'admin@medpredictor.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'system_admin', NOW(), NOW())
ON DUPLICATE KEY UPDATE name = VALUES(name);

-- Création de la table sessions si elle n'existe pas
CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(255) COLLATE utf8mb4_unicode_ci NOT NULL,
    user_id BIGINT UNSIGNED DEFAULT NULL,
    ip_address VARCHAR(45) DEFAULT NULL,
    user_agent TEXT,
    payload LONGTEXT COLLATE utf8mb4_unicode_ci NOT NULL,
    last_activity INT NOT NULL,
    PRIMARY KEY (id),
    KEY sessions_user_id_index (user_id),
    KEY sessions_last_activity_index (last_activity)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table password_reset_tokens si elle n'existe pas
CREATE TABLE IF NOT EXISTS password_reset_tokens (
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP NULL,
    PRIMARY KEY (email)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table personal_access_tokens si elle n'existe pas
CREATE TABLE IF NOT EXISTS personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL,
    abilities TEXT,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,
    UNIQUE KEY personal_access_tokens_token_unique (token),
    KEY personal_access_tokens_tokenable_type_tokenable_id_index (tokenable_type, tokenable_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table failed_jobs si elle n'existe pas
CREATE TABLE IF NOT EXISTS failed_jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid VARCHAR(255) NOT NULL UNIQUE,
    connection TEXT NOT NULL,
    queue TEXT NOT NULL,
    payload LONGTEXT NOT NULL,
    exception LONGTEXT NOT NULL,
    failed_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table cache si elle n'existe pas
CREATE TABLE IF NOT EXISTS cache (
    `key` VARCHAR(255) NOT NULL,
    value MEDIUMTEXT NOT NULL,
    expiration INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table cache_locks si elle n'existe pas
CREATE TABLE IF NOT EXISTS cache_locks (
    `key` VARCHAR(255) NOT NULL,
    owner VARCHAR(255) NOT NULL,
    expiration INT NOT NULL,
    PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table jobs si elle n'existe pas
CREATE TABLE IF NOT EXISTS jobs (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    queue VARCHAR(255) NOT NULL,
    payload LONGTEXT NOT NULL,
    attempts TINYINT UNSIGNED NOT NULL,
    reserved_at INT UNSIGNED NULL,
    available_at INT UNSIGNED NOT NULL,
    created_at INT UNSIGNED NOT NULL,
    KEY jobs_queue_index (queue)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table job_batches si elle n'existe pas
CREATE TABLE IF NOT EXISTS job_batches (
    id VARCHAR(255) NOT NULL,
    name VARCHAR(255) NOT NULL,
    total_jobs INT NOT NULL,
    pending_jobs INT NOT NULL,
    failed_jobs INT NOT NULL,
    failed_job_ids LONGTEXT NOT NULL,
    options MEDIUMTEXT,
    cancelled_at INT NULL,
    created_at INT NOT NULL,
    finished_at INT NULL,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Création de la table migrations si elle n'existe pas
CREATE TABLE IF NOT EXISTS migrations (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    migration VARCHAR(255) NOT NULL,
    batch INT NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertion des migrations de base
INSERT INTO migrations (migration, batch) VALUES 
('0001_01_01_000001_create_cache_table', 1),
('0001_01_01_000002_create_jobs_table', 1),
('2025_07_14_200310_create_users_table', 1),
('2025_07_14_214102_create_personal_access_tokens_table', 1),
('2025_07_14_215145_create_sessions_table', 1)
ON DUPLICATE KEY UPDATE migration = VALUES(migration);

SELECT 'Tables créées avec succès!' as status;
