-- ============================================
-- Fix permissions for info@i-want-it.es
-- Run this on the production MySQL database,
-- then run: php artisan permission:cache-reset
-- ============================================

-- 1. Ensure Admin role exists
INSERT IGNORE INTO roles (name, guard_name, description, can_manage_all_users, can_manage_own_users, created_at, updated_at)
VALUES ('Admin', 'web', 'Acceso total al sistema', 1, 1, NOW(), NOW());

-- 2. Insert all 74 permissions
INSERT IGNORE INTO permissions (name, guard_name, created_at, updated_at) VALUES
-- users
('users-menu',             'web', NOW(), NOW()),
('users-screen',           'web', NOW(), NOW()),
('users-list',             'web', NOW(), NOW()),
('users-create',           'web', NOW(), NOW()),
('users-edit',             'web', NOW(), NOW()),
('users-delete',           'web', NOW(), NOW()),
('users-view',             'web', NOW(), NOW()),
-- projects
('projects-menu',          'web', NOW(), NOW()),
('projects-screen',        'web', NOW(), NOW()),
('projects-list',          'web', NOW(), NOW()),
('projects-create',        'web', NOW(), NOW()),
('projects-edit',          'web', NOW(), NOW()),
('projects-delete',        'web', NOW(), NOW()),
('projects-view',          'web', NOW(), NOW()),
-- hotpoints
('hotpoints-menu',         'web', NOW(), NOW()),
('hotpoints-screen',       'web', NOW(), NOW()),
('hotpoints-list',         'web', NOW(), NOW()),
('hotpoints-create',       'web', NOW(), NOW()),
('hotpoints-edit',         'web', NOW(), NOW()),
('hotpoints-delete',       'web', NOW(), NOW()),
('hotpoints-view',         'web', NOW(), NOW()),
-- tags
('tags-menu',              'web', NOW(), NOW()),
('tags-screen',            'web', NOW(), NOW()),
('tags-list',              'web', NOW(), NOW()),
('tags-create',            'web', NOW(), NOW()),
('tags-edit',              'web', NOW(), NOW()),
('tags-delete',            'web', NOW(), NOW()),
('tags-view',              'web', NOW(), NOW()),
-- territories
('territories-menu',       'web', NOW(), NOW()),
('territories-screen',     'web', NOW(), NOW()),
('territories-list',       'web', NOW(), NOW()),
('territories-create',     'web', NOW(), NOW()),
('territories-edit',       'web', NOW(), NOW()),
('territories-delete',     'web', NOW(), NOW()),
('territories-view',       'web', NOW(), NOW()),
-- brands
('brands-menu',            'web', NOW(), NOW()),
('brands-screen',          'web', NOW(), NOW()),
('brands-list',            'web', NOW(), NOW()),
('brands-create',          'web', NOW(), NOW()),
('brands-edit',            'web', NOW(), NOW()),
('brands-delete',          'web', NOW(), NOW()),
('brands-view',            'web', NOW(), NOW()),
-- products
('products-menu',          'web', NOW(), NOW()),
('products-screen',        'web', NOW(), NOW()),
('products-list',          'web', NOW(), NOW()),
('products-create',        'web', NOW(), NOW()),
('products-edit',          'web', NOW(), NOW()),
('products-delete',        'web', NOW(), NOW()),
('products-view',          'web', NOW(), NOW()),
-- options (Security items)
('options-menu',           'web', NOW(), NOW()),
('options-screen',         'web', NOW(), NOW()),
('options-list',           'web', NOW(), NOW()),
('options-create',         'web', NOW(), NOW()),
('options-edit',           'web', NOW(), NOW()),
('options-delete',         'web', NOW(), NOW()),
('options-view',           'web', NOW(), NOW()),
-- datision-parameters (AI Machine CFG)
('datision-parameters-menu',    'web', NOW(), NOW()),
('datision-parameters-screen',  'web', NOW(), NOW()),
('datision-parameters-list',    'web', NOW(), NOW()),
('datision-parameters-create',  'web', NOW(), NOW()),
('datision-parameters-edit',    'web', NOW(), NOW()),
('datision-parameters-delete',  'web', NOW(), NOW()),
('datision-parameters-view',    'web', NOW(), NOW()),
-- roles
('roles-menu',             'web', NOW(), NOW()),
('roles-screen',           'web', NOW(), NOW()),
('roles-list',             'web', NOW(), NOW()),
('roles-create',           'web', NOW(), NOW()),
('roles-edit',             'web', NOW(), NOW()),
('roles-delete',           'web', NOW(), NOW()),
('roles-view',             'web', NOW(), NOW()),
-- permissions
('permissions-menu',       'web', NOW(), NOW()),
('permissions-screen',     'web', NOW(), NOW()),
('permissions-list',       'web', NOW(), NOW()),
('permissions-view',       'web', NOW(), NOW());

-- 3. Fetch the Admin role ID
SET @admin_role_id = (SELECT id FROM roles WHERE name = 'Admin' AND guard_name = 'web');

-- 4. Clear existing role-permission assignments and re-sync all to Admin
DELETE FROM role_has_permissions WHERE role_id = @admin_role_id;

INSERT INTO role_has_permissions (permission_id, role_id)
SELECT p.id, @admin_role_id
FROM permissions p
WHERE p.guard_name = 'web';

-- 5. Fetch the user ID
SET @user_id = (SELECT id FROM users WHERE email = 'info@i-want-it.es');

-- 6. Remove existing role assignments for this user
DELETE FROM model_has_roles WHERE model_id = @user_id AND model_type = 'App\\Models\\User';

-- 7. Assign Admin role to the user
INSERT INTO model_has_roles (role_id, model_type, model_id)
VALUES (@admin_role_id, 'App\\Models\\User', @user_id);

-- 8. Verification query (run separately to check)
-- SELECT u.email, r.name AS role, COUNT(rhp.permission_id) AS permissions_count
-- FROM users u
-- JOIN model_has_roles mhr ON mhr.model_id = u.id AND mhr.model_type = 'App\\Models\\User'
-- JOIN roles r ON r.id = mhr.role_id
-- LEFT JOIN role_has_permissions rhp ON rhp.role_id = r.id
-- WHERE u.email = 'info@i-want-it.es'
-- GROUP BY u.email, r.name;
