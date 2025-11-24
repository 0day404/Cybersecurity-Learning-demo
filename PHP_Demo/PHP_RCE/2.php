<?php
// config.php - 模拟配置文件
define('APP_NAME', 'Windows Server Admin Panel');
define('APP_VERSION', '1.2.3');
$allowed_users = ['admin', 'operator'];
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> v<?= APP_VERSION ?></title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 20px;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: white;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #0078D7;
            color: white;
            padding: 20px;
            text-align: center;
        }
        .nav {
            background: #e6e6e6;
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .nav a {
            margin-right: 15px;
            text-decoration: none;
            color: #005a9e;
            font-weight: 500;
        }
        .content {
            padding: 20px;
        }
        .panel {
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .panel-header {
            background: #f3f3f3;
            padding: 10px 15px;
            border-bottom: 1px solid #ddd;
            font-weight: bold;
        }
        .panel-body {
            padding: 15px;
        }
        .form-group {
            margin-bottom: 15px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }
        input[type="text"], input[type="password"], select, textarea {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #0078D7;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 4px;
            cursor: pointer;
        }
        button:hover {
            background: #106ebe;
        }
        .result {
            margin-top: 15px;
            padding: 10px;
            background: #f9f9f9;
            border-left: 4px solid #0078D7;
        }
        pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-all;
        }
        .alert {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 4px;
        }
        .alert-warning {
            background: #fff4ce;
            border-left: 4px solid #ffc107;
        }
        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><?= APP_NAME ?></h1>
        <p>版本 <?= APP_VERSION ?> - Windows服务器管理</p>
    </div>

    <div class="nav">
        <a href="#dashboard">仪表盘</a>
        <a href="#system-info">系统信息</a>
        <a href="#services">服务管理</a>
        <a href="#tasks">计划任务</a>
        <a href="#custom-cmd">自定义命令</a>
    </div>

    <div class="content">
        <!-- 仪表盘 -->
        <div class="panel" id="dashboard">
            <div class="panel-header">
                服务器状态概览
            </div>
            <div class="panel-body">
                <?php
                // 漏洞示例1：直接执行系统命令获取信息
                $uptime = shell_exec('net statistics server 2>&1');
                $disk = shell_exec('wmic logicaldisk get caption,freespace,size 2>&1');
                $memory = shell_exec('wmic OS get FreePhysicalMemory,TotalVisibleMemorySize /Value 2>&1');
                ?>

                <h3>系统运行时间:</h3>
                <pre><?= htmlspecialchars($uptime ?: '无法获取运行时间信息') ?></pre>

                <h3>磁盘空间:</h3>
                <pre><?= htmlspecialchars($disk ?: '无法获取磁盘信息') ?></pre>

                <h3>内存使用:</h3>
                <pre><?= htmlspecialchars($memory ?: '无法获取内存信息') ?></pre>
            </div>
        </div>

        <!-- 系统信息模块 -->
        <div class="panel" id="system-info">
            <div class="panel-header">
                详细系统信息
            </div>
            <div class="panel-body">
                <form method="POST">
                    <div class="form-group">
                        <label>信息类型:</label>
                        <select name="info_type">
                            <option value="system">系统详情</option>
                            <option value="network">网络配置</option>
                            <option value="users">用户账户</option>
                            <option value="custom">自定义WMI查询</option>
                        </select>
                    </div>
                    <div class="form-group" id="custom-wmi" style="display:none;">
                        <label>WMI查询语句:</label>
                        <input type="text" name="wmi_query" placeholder="例如: SELECT * FROM Win32_Process">
                    </div>
                    <button type="submit">查询</button>
                </form>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['info_type'])) {
                    echo '<div class="result">';

                    $type = $_POST['info_type'];
                    $query = $_POST['wmi_query'] ?? '';

                    // 漏洞示例2：不安全的WMI查询拼接
                    if ($type === 'custom' && $query) {
                        $output = shell_exec("wmic {$query} 2>&1");
                        echo "<h4>WMI查询结果:</h4>";
                        echo "<pre>" . $output . "</pre>";
                    } else {
                        $commands = [
                            'system' => 'systeminfo',
                            'network' => 'ipconfig /all & netstat -ano',
                            'users' => 'net user'
                        ];

                        if (isset($commands[$type])) {
                            $output = shell_exec($commands[$type] . " 2>&1");
                            echo "<h4>系统信息:</h4>";
                            echo "<pre>" . $output . "</pre>";
                        }
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- 服务管理模块 -->
        <div class="panel" id="services">
            <div class="panel-header">
                Windows服务管理
            </div>
            <div class="panel-body">
                <form method="POST">
                    <div class="form-group">
                        <label>操作:</label>
                        <select name="service_action" id="service-action">
                            <option value="list">列出服务</option>
                            <option value="start">启动服务</option>
                            <option value="stop">停止服务</option>
                            <option value="restart">重启服务</option>
                        </select>
                    </div>
                    <div class="form-group" id="service-name-group" style="display:none;">
                        <label>服务名称:</label>
                        <input type="text" name="service_name" placeholder="例如: wuauserv">
                    </div>
                    <button type="submit">执行</button>
                </form>

                <?php
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_action'])) {
                    echo '<div class="result">';

                    $action = $_POST['service_action'];
                    $service = $_POST['service_name'] ?? '';

                    // 漏洞示例3：不安全的服务管理命令拼接
                    switch ($action) {
                        case 'list':
                            $output = shell_exec('sc queryex state=all 2>&1');
                            echo "<pre>" . $output . "</pre>";
                            break;
                        case 'start':
                            $output = shell_exec("net start \"{$service}\" 2>&1");
                            echo "<pre>启动服务结果:\n" . $output . "</pre>";
                            break;
                        case 'stop':
                            $output = shell_exec("net stop \"{$service}\" 2>&1");
                            echo "<pre>停止服务结果:\n" . $output . "</pre>";
                            break;
                        case 'restart':
                            $output = shell_exec("net stop \"{$service}\" & net start \"{$service}\" 2>&1");
                            echo "<pre>重启服务结果:\n" . $output . "</pre>";
                            break;
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- 自定义命令模块 -->
        <div class="panel" id="custom-cmd">
            <div class="panel-header">
                自定义命令执行
            </div>
            <div class="panel-body">
                <div class="alert alert-danger">
                    <strong>高危操作!</strong> 此功能允许执行任意系统命令，请谨慎使用!
                </div>
                <form method="POST">
                    <div class="form-group">
                        <label>命令:</label>
                        <input type="text" name="command" placeholder="例如: ping 127.0.0.1" required>
                    </div>
                    <button type="submit">执行</button>
                </form>

                <?php
                // 漏洞示例4：完全无限制的命令执行
                if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['command'])) {
                    echo '<div class="result">';
                    echo '<h4>命令执行结果:</h4>';

                    $cmd = $_POST['command'];
                    $output = shell_exec($cmd);
//                    echo "<pre>" . $output . "</pre>";
//
//                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    // 显示/隐藏服务名称输入框
    document.getElementById('service-action').addEventListener('change', function() {
        document.getElementById('service-name-group').style.display =
            this.value === 'list' ? 'none' : 'block';
    });

    // 显示/隐藏WMI查询输入框
    document.querySelector('select[name="info_type"]').addEventListener('change', function() {
        document.getElementById('custom-wmi').style.display =
            this.value === 'custom' ? 'block' : 'none';
    });
</script>
</body>
</html>