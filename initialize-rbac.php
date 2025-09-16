<?php

// Use SQLite directly
$pdo = new PDO('sqlite:database/database.sqlite');
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

echo "🚀 Initializing RBAC System...\n";

try {
    // Create basic roles
    $roles = [
        [
            'name' => 'system_admin',
            'display_name' => 'System Administrator',
            'description' => 'Full system access with all permissions',
            'permissions' => json_encode([
                'players.view', 'players.create', 'players.edit', 'players.delete',
                'clubs.view', 'clubs.create', 'clubs.edit', 'clubs.delete',
                'competitions.view', 'competitions.create', 'competitions.edit', 'competitions.manage',
                'referees.view', 'referees.assign', 'referees.manage',
                'system.admin', 'system.manage', 'system.stats',
                'fifa.connect', 'fifa.sync',
                'medical.access', 'medical.manage'
            ]),
            'is_system_role' => 1,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'club_admin',
            'display_name' => 'Club Administrator',
            'description' => 'Club management permissions',
            'permissions' => json_encode([
                'players.view', 'players.create', 'players.edit',
                'clubs.view', 'clubs.edit',
                'competitions.view',
                'referees.view',
                'medical.access'
            ]),
            'is_system_role' => 0,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'referee',
            'display_name' => 'Referee',
            'description' => 'Referee permissions',
            'permissions' => json_encode([
                'competitions.view',
                'referees.view'
            ]),
            'is_system_role' => 0,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ],
        [
            'name' => 'player',
            'display_name' => 'Player',
            'description' => 'Player permissions',
            'permissions' => json_encode([
                'players.view'
            ]),
            'is_system_role' => 0,
            'is_active' => 1,
            'created_at' => now(),
            'updated_at' => now()
        ]
    ];

    // Insert roles
    foreach ($roles as $role) {
        $stmt = $pdo->prepare("INSERT INTO roles (name, display_name, description, permissions, is_system_role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $role['name'],
            $role['display_name'],
            $role['description'],
            $role['permissions'],
            $role['is_system_role'],
            $role['is_active'],
            $role['created_at'],
            $role['updated_at']
        ]);
        echo "✅ Created role: {$role['display_name']}\n";
    }

    // Create basic permissions table if it doesn't exist
    $pdo->exec("CREATE TABLE IF NOT EXISTS permissions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name VARCHAR NOT NULL,
        slug VARCHAR NOT NULL UNIQUE,
        module VARCHAR NOT NULL,
        action VARCHAR NOT NULL,
        description TEXT,
        created_at DATETIME,
        updated_at DATETIME
    )");

    // Create basic permissions
    $permissions = [
        // Players module
        ['name' => 'View Players', 'slug' => 'players.view', 'module' => 'players', 'action' => 'view', 'description' => 'View player information'],
        ['name' => 'Create Players', 'slug' => 'players.create', 'module' => 'players', 'action' => 'create', 'description' => 'Create new players'],
        ['name' => 'Edit Players', 'slug' => 'players.edit', 'module' => 'players', 'action' => 'edit', 'description' => 'Edit player information'],
        ['name' => 'Delete Players', 'slug' => 'players.delete', 'module' => 'players', 'action' => 'delete', 'description' => 'Delete players'],
        
        // Clubs module
        ['name' => 'View Clubs', 'slug' => 'clubs.view', 'module' => 'clubs', 'action' => 'view', 'description' => 'View club information'],
        ['name' => 'Create Clubs', 'slug' => 'clubs.create', 'module' => 'clubs', 'action' => 'create', 'description' => 'Create new clubs'],
        ['name' => 'Edit Clubs', 'slug' => 'clubs.edit', 'module' => 'clubs', 'action' => 'edit', 'description' => 'Edit club information'],
        ['name' => 'Delete Clubs', 'slug' => 'clubs.delete', 'module' => 'clubs', 'action' => 'delete', 'description' => 'Delete clubs'],
        
        // Competitions module
        ['name' => 'View Competitions', 'slug' => 'competitions.view', 'module' => 'competitions', 'action' => 'view', 'description' => 'View competition information'],
        ['name' => 'Create Competitions', 'slug' => 'competitions.create', 'module' => 'competitions', 'action' => 'create', 'description' => 'Create new competitions'],
        ['name' => 'Edit Competitions', 'slug' => 'competitions.edit', 'module' => 'competitions', 'action' => 'edit', 'description' => 'Edit competition information'],
        ['name' => 'Manage Competitions', 'slug' => 'competitions.manage', 'module' => 'competitions', 'action' => 'manage', 'description' => 'Manage competitions'],
        
        // Referees module
        ['name' => 'View Referees', 'slug' => 'referees.view', 'module' => 'referees', 'action' => 'view', 'description' => 'View referee information'],
        ['name' => 'Assign Referees', 'slug' => 'referees.assign', 'module' => 'referees', 'action' => 'assign', 'description' => 'Assign referees to matches'],
        ['name' => 'Manage Referees', 'slug' => 'referees.manage', 'module' => 'referees', 'action' => 'manage', 'description' => 'Manage referees'],
        
        // System module
        ['name' => 'System Admin', 'slug' => 'system.admin', 'module' => 'system', 'action' => 'admin', 'description' => 'System administration'],
        ['name' => 'System Manage', 'slug' => 'system.manage', 'module' => 'system', 'action' => 'manage', 'description' => 'System management'],
        ['name' => 'System Stats', 'slug' => 'system.stats', 'module' => 'system', 'action' => 'stats', 'description' => 'System statistics'],
        
        // FIFA module
        ['name' => 'FIFA Connect', 'slug' => 'fifa.connect', 'module' => 'fifa', 'action' => 'connect', 'description' => 'FIFA Connect integration'],
        ['name' => 'FIFA Sync', 'slug' => 'fifa.sync', 'module' => 'fifa', 'action' => 'sync', 'description' => 'FIFA data synchronization'],
        
        // Medical module
        ['name' => 'Medical Access', 'slug' => 'medical.access', 'module' => 'medical', 'action' => 'access', 'description' => 'Access medical information'],
        ['name' => 'Medical Manage', 'slug' => 'medical.manage', 'module' => 'medical', 'action' => 'manage', 'description' => 'Manage medical information']
    ];

    // Insert permissions
    foreach ($permissions as $permission) {
        $stmt = $pdo->prepare("INSERT INTO permissions (name, slug, module, action, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->execute([
            $permission['name'],
            $permission['slug'],
            $permission['module'],
            $permission['action'],
            $permission['description'],
            date('Y-m-d H:i:s'),
            date('Y-m-d H:i:s')
        ]);
        echo "✅ Created permission: {$permission['name']}\n";
    }

    echo "\n🎉 RBAC System initialized successfully!\n";
    echo "📊 Created " . count($roles) . " roles and " . count($permissions) . " permissions\n";

} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
    exit(1);
}
