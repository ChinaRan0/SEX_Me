# 游戏应用 Web 程序

一个基于 PHP 的 Web 游戏应用，包含三种趣味游戏类型：骰子游戏、姿势选择和随机任务。支持后台管理游戏内容，并提供预设系统通过分享码分享游戏配置。

## 功能特性

### 游戏模式

1. **骰子游戏** - 结合动作和部位，通过骰子随机组合产生趣味结果
2. **姿势游戏** - 随机选择姿势、地点和时间，创造有趣的场景
3. **随机任务** - 提供随机任务描述，增加游戏趣味性

### 预设系统
- 支持创建和分享游戏预设配置
- 通过 8 位分享码快速分享预设
- 可保存多轮游戏配置

### 后台管理
- 管理游戏内容（动作、部位、姿势、地点、时间、任务）
- 启用/禁用游戏内容
- 创建和管理预设配置
- 生成分享码

## 技术栈

- **后端框架**: 自定义 MVC 架构
- **PHP 版本**: PHP 7.4+
- **数据库**: SQLite
- **Web 服务器**: Apache (需启用 mod_rewrite) / PHP 内置服务器 (开发环境)
- **认证方式**: Token (Bearer Token)

## 目录结构

```
├── app/
│   ├── Controllers/    # 控制器 (处理 HTTP 请求)
│   ├── Models/         # 模型 (数据库操作)
│   ├── Services/       # 服务层 (业务逻辑)
│   └── Helpers/        # 辅助类
├── api/
│   └── index.php       # API 入口和路由定义
├── config/
│   └── config.php      # 应用配置
├── database/
│   ├── schema.sql      # 数据库架构
│   └── game.db         # SQLite 数据库 (自动创建)
├── admin/              # 后台管理页面
├── *.html              # 公共游戏页面
├── .htaccess           # Apache 重写规则
└── README.md           # 项目说明文档
```

## 安装部署

### 环境要求

- PHP 7.4 或更高版本
- SQLite 扩展
- Apache 服务器 (生产环境) 或 PHP 内置服务器 (开发环境)

### 开发环境

1. 克隆或下载项目到 Web 服务器目录

2. 启动 PHP 内置服务器:
```bash
php -S localhost:8000
```

3. 访问 `http://localhost:8000` 查看游戏页面

### 生产环境

1. 将项目文件上传到 Apache 服务器

2. 确保启用了 `mod_rewrite` 模块:
```bash
sudo a2enmod rewrite
```

3. 设置正确的文件权限:
```bash
chmod -R 755 /path/to/project
chmod 666 /path/to/project/database/game.db
```

4. 数据库会在首次访问时自动创建和初始化

## API 文档

### 公共接口 (无需认证)

| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/dice` | 获取骰子动作和部位 |
| GET | `/api/poses` | 获取姿势数据 |
| GET | `/api/tasks` | 获取任务列表 |
| GET | `/api/tasks/random` | 获取单个随机任务 |
| GET | `/api/preset/{code}` | 通过分享码获取预设 |

### 管理接口 (需认证)

需要在请求头中添加 Bearer Token:
```
Authorization: Bearer {your_token}
```

#### 认证相关

| 方法 | 路由 | 说明 |
|------|------|------|
| POST | `/api/admin/login` | 管理员登录 |
| POST | `/api/admin/logout` | 管理员登出 |
| GET | `/api/admin/verify` | 验证 Token 有效性 |

#### 骰子管理

| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/dice/actions` | 获取动作列表 |
| POST | `/api/admin/dice/actions` | 创建动作 |
| PUT | `/api/admin/dice/actions/{id}` | 更新动作 |
| DELETE | `/api/admin/dice/actions/{id}` | 删除动作 |
| GET | `/api/admin/dice/parts` | 获取部位列表 |
| POST | `/api/admin/dice/parts` | 创建部位 |
| PUT | `/api/admin/dice/parts/{id}` | 更新部位 |
| DELETE | `/api/admin/dice/parts/{id}` | 删除部位 |

#### 姿势管理

| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/poses` | 获取姿势列表 |
| POST | `/api/admin/poses` | 创建姿势 |
| PUT | `/api/admin/poses/{id}` | 更新姿势 |
| DELETE | `/api/admin/poses/{id}` | 删除姿势 |
| GET | `/api/admin/places` | 获取地点列表 |
| POST | `/api/admin/places` | 创建地点 |
| PUT | `/api/admin/places/{id}` | 更新地点 |
| DELETE | `/api/admin/places/{id}` | 删除地点 |
| GET | `/api/admin/times` | 获取时间列表 |
| POST | `/api/admin/times` | 创建时间 |
| PUT | `/api/admin/times/{id}` | 更新时间 |
| DELETE | `/api/admin/times/{id}` | 删除时间 |

#### 任务管理

| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/tasks` | 获取任务列表 |
| POST | `/api/admin/tasks` | 创建任务 |
| PUT | `/api/admin/tasks/{id}` | 更新任务 |
| DELETE | `/api/admin/tasks/{id}` | 删除任务 |

#### 预设管理

| 方法 | 路由 | 说明 |
|------|------|------|
| GET | `/api/admin/presets` | 获取预设列表 |
| POST | `/api/admin/presets` | 创建预设 |
| DELETE | `/api/admin/presets/{code}` | 删除预设 |

## 后台管理

访问 `admin/` 目录下的管理页面即可进行后台管理。

功能包括：
- 管理游戏内容（增删改查）
- 启用/禁用游戏内容
- 创建和删除预设配置
- 生成分享码

## 数据库结构

### 主要数据表

| 表名 | 说明 |
|------|------|
| `admins` | 管理员账号 |
| `admin_sessions` | 管理员会话 (Token) |
| `dice_actions` | 骰子动作 |
| `dice_parts` | 骰子部位 |
| `zishi_poses` | 姿势 |
| `zishi_places` | 地点 |
| `zishi_times` | 时间 |
| `tasks` | 任务 |
| `preset_rounds` | 预设轮次 |

详细表结构请参考 `database/schema.sql`。

## 安全特性

- **Token 认证**: 使用 Bearer Token 保护管理接口
- **密码加密**: 使用 `password_hash()` 加密存储密码
- **SQL 注入防护**: 使用 PDO 预处理语句
- **访问控制**: 管理接口需要认证
- **Token 过期**: 会话 Token 自动过期机制
- **登录限制**: 登录失败频率限制 (15 分钟内最多 5 次尝试)

## 默认账号

```
用户名: admin
密码: admin123
```

> 首次运行时自动创建，请在生产环境中及时修改默认密码！

## 许可证

本项目仅供学习和娱乐使用。

## 支持

如有问题或建议，请通过项目仓库提交 Issue。
