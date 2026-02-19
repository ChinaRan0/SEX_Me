-- Admin users table
CREATE TABLE IF NOT EXISTS admins (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Admin sessions table
CREATE TABLE IF NOT EXISTS admin_sessions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    admin_id INTEGER NOT NULL,
    token VARCHAR(64) UNIQUE NOT NULL,
    expires_at DATETIME NOT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (admin_id) REFERENCES admins(id) ON DELETE CASCADE
);

-- Dice actions table
CREATE TABLE IF NOT EXISTS dice_actions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Dice parts table
CREATE TABLE IF NOT EXISTS dice_parts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Pose places table
CREATE TABLE IF NOT EXISTS zishi_places (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Poses table
CREATE TABLE IF NOT EXISTS zishi_poses (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    image_path VARCHAR(255),
    description TEXT,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Time options table
CREATE TABLE IF NOT EXISTS zishi_times (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Tasks table (随机任务库)
CREATE TABLE IF NOT EXISTS tasks (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    description TEXT NOT NULL,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Presets table (预设配置 - 统一预设，一个链接包含所有游戏类型)
CREATE TABLE IF NOT EXISTS presets (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    share_code VARCHAR(16) UNIQUE NOT NULL,
    rounds INTEGER DEFAULT 5,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Preset rounds table (统一轮次表，包含骰子+姿势+任务+剧情四种游戏数据)
CREATE TABLE IF NOT EXISTS preset_rounds (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    preset_id INTEGER NOT NULL,
    round_number INTEGER NOT NULL,
    -- 骰子数据
    dice_action_id INTEGER,
    dice_part_id INTEGER,
    -- 姿势数据
    pose_id INTEGER,
    place_id INTEGER,
    time_id INTEGER,
    -- 任务数据
    task_id INTEGER,
    -- 剧情数据
    story_male_role_id INTEGER,
    story_female_role_id INTEGER,
    story_relationship_id INTEGER,
    story_initiative_id INTEGER,
    story_behavior_id INTEGER,
    story_action_id INTEGER,
    FOREIGN KEY (preset_id) REFERENCES presets(id) ON DELETE CASCADE,
    FOREIGN KEY (dice_action_id) REFERENCES dice_actions(id),
    FOREIGN KEY (dice_part_id) REFERENCES dice_parts(id),
    FOREIGN KEY (pose_id) REFERENCES zishi_poses(id),
    FOREIGN KEY (place_id) REFERENCES zishi_places(id),
    FOREIGN KEY (time_id) REFERENCES zishi_times(id),
    FOREIGN KEY (task_id) REFERENCES tasks(id),
    FOREIGN KEY (story_male_role_id) REFERENCES story_male_roles(id),
    FOREIGN KEY (story_female_role_id) REFERENCES story_female_roles(id),
    FOREIGN KEY (story_relationship_id) REFERENCES story_relationships(id),
    FOREIGN KEY (story_initiative_id) REFERENCES story_initiatives(id),
    FOREIGN KEY (story_behavior_id) REFERENCES story_behaviors(id),
    FOREIGN KEY (story_action_id) REFERENCES story_actions(id)
);

-- Login attempts table (for rate limiting)
CREATE TABLE IF NOT EXISTS login_attempts (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    ip_address VARCHAR(45) NOT NULL,
    username VARCHAR(50),
    success BOOLEAN DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Story Male Roles table (男方身份)
CREATE TABLE IF NOT EXISTS story_male_roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Story Female Roles table (女方身份)
CREATE TABLE IF NOT EXISTS story_female_roles (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Story Relationships table (两人关系)
CREATE TABLE IF NOT EXISTS story_relationships (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Story Initiatives table (主动权)
CREATE TABLE IF NOT EXISTS story_initiatives (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Story Behaviors table (行为)
CREATE TABLE IF NOT EXISTS story_behaviors (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Story Actions table (动作)
CREATE TABLE IF NOT EXISTS story_actions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    sort_order INTEGER DEFAULT 0,
    is_active BOOLEAN DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- Create indexes
CREATE INDEX IF NOT EXISTS idx_admin_sessions_token ON admin_sessions(token);
CREATE INDEX IF NOT EXISTS idx_admin_sessions_expires ON admin_sessions(expires_at);
CREATE INDEX IF NOT EXISTS idx_login_attempts_ip ON login_attempts(ip_address);
CREATE INDEX IF NOT EXISTS idx_presets_code ON presets(share_code);
CREATE INDEX IF NOT EXISTS idx_preset_rounds_preset ON preset_rounds(preset_id);

-- Story indexes
CREATE INDEX IF NOT EXISTS idx_story_male_roles_active ON story_male_roles(is_active);
CREATE INDEX IF NOT EXISTS idx_story_female_roles_active ON story_female_roles(is_active);
CREATE INDEX IF NOT EXISTS idx_story_relationships_active ON story_relationships(is_active);
CREATE INDEX IF NOT EXISTS idx_story_initiatives_active ON story_initiatives(is_active);
CREATE INDEX IF NOT EXISTS idx_story_behaviors_active ON story_behaviors(is_active);
CREATE INDEX IF NOT EXISTS idx_story_actions_active ON story_actions(is_active);
