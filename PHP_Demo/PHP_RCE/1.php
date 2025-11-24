<?php
// config.php - 配置文件
define('APP_NAME', '动态代码执行平台');
define('ADMIN_EMAIL', 'admin@example.com');
$allowed_ips = ['192.168.1.100', '10.0.0.15']; // 所谓的IP白名单
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - 开发工具</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: #6777ef;
            color: white;
            padding: 25px;
            text-align: center;
        }
        .nav {
            background: #e6e9f0;
            padding: 15px;
            border-bottom: 1px solid #d8dbe0;
        }
        .nav a {
            margin-right: 20px;
            text-decoration: none;
            color: #191d21;
            font-weight: 500;
        }
        .content {
            padding: 25px;
        }
        .panel {
            margin-bottom: 30px;
            border: 1px solid #e4e6fc;
            border-radius: 6px;
            overflow: hidden;
        }
        .panel-header {
            background: #f6f7fb;
            padding: 15px 20px;
            border-bottom: 1px solid #e4e6fc;
            font-weight: bold;
            color: #6777ef;
        }
        .panel-body {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #191d21;
        }
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            min-height: 150px;
            font-family: 'Consolas', monospace;
            font-size: 14px;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #6777ef;
            color: white;
            border: none;
            padding: 12px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            transition: background 0.3s;
        }
        button:hover {
            background: #5166ea;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background: #f9fafc;
            border-left: 4px solid #6777ef;
            border-radius: 4px;
        }
        pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }
        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }
        .footer {
            text-align: center;
            padding: 20px;
            color: #6c757d;
            font-size: 14px;
            border-top: 1px solid #e4e6fc;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><?= APP_NAME ?></h1>
        <p>高级开发工具 - 仅限内部使用</p>
    </div>

    <div class="nav">
        <a href="#code-tester">代码测试器</a>
        <a href="#template-engine">模板引擎</a>
        <a href="#data-processor">数据处理</a>
    </div>

    <div class="content">
        <!-- 代码测试器模块 -->
        <div class="panel" id="code-tester">
            <div class="panel-header">
                PHP代码测试器
            </div>
            <div class="panel-body">
                <div class="alert alert-warning">
                    <strong>注意：</strong> 此功能允许执行PHP代码，请确保输入安全的代码片段。
                </div>
                <form method="POST" action="?action=execute">
                    <div class="form-group">
                        <label for="php_code">输入PHP代码：</label>
                        <textarea id="php_code" name="php_code" placeholder="例如: return 2 + 2 * 2;" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>执行模式：</label>
                        <select name="exec_mode">
                            <option value="eval">直接执行(eval)</option>
                            <option value="return">返回结果</option>
                            <option value="print">打印输出</option>
                        </select>
                    </div>
                    <button type="submit">执行代码</button>
                </form>

                <?php
                // 漏洞1：直接eval用户输入的PHP代码
                if (isset($_GET['action']) && $_GET['action'] === 'execute' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo '<div class="result">';
                    echo '<h4>执行结果：</h4>';

                    $code = $_POST['php_code'];
                    $mode = $_POST['exec_mode'];

                    try {
                        // 危险！直接eval用户输入
                        switch ($mode) {
                            case 'eval':
                                eval($code);
                                break;
                            case 'return':
                                $result = eval("return {$code};");
                                echo "<pre>" . print_r($result, true) . "</pre>";
                                break;
                            case 'print':
                                eval("print({$code});");
                                break;
                        }
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">错误: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- 模板引擎模块 -->
        <div class="panel" id="template-engine">
            <div class="panel-header">
                动态模板引擎
            </div>
            <div class="panel-body">
                <form method="POST" action="?action=render">
                    <div class="form-group">
                        <label for="template">模板内容：</label>
                        <textarea id="template" name="template" placeholder="例如: Hello, {= $name }! Today is {= date('Y-m-d') }" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="template_vars">模板变量 (JSON格式)：</label>
                        <input type="text" id="template_vars" name="template_vars" placeholder='例如: {"name": "John"}' value='{"name": "Guest"}'>
                    </div>
                    <button type="submit">渲染模板</button>
                </form>

                <?php
                // 漏洞2：自定义模板引擎中的eval执行
                if (isset($_GET['action']) && $_GET['action'] === 'render' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo '<div class="result">';
                    echo '<h4>渲染结果：</h4>';

                    $template = $_POST['template'];
                    $vars = json_decode($_POST['template_vars'], true) ?: [];

                    try {
                        // 解析模板标签
                        $output = preg_replace_callback(
                            '/\{=\s*(.+?)\s*\}/',
                            function($matches) use ($vars) {
                                extract($vars);
                                // 危险！eval模板表达式
                                return eval("return {$matches[1]};");
                            },
                            $template
                        );

                        echo "<pre>" . htmlspecialchars($output) . "</pre>";
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">渲染错误: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- 数据处理模块 -->
        <div class="panel" id="data-processor">
            <div class="panel-header">
                自定义数据处理
            </div>
            <div class="panel-body">
                <form method="POST" action="?action=process">
                    <div class="form-group">
                        <label for="input_data">输入数据 (JSON格式)：</label>
                        <textarea id="input_data" name="input_data" placeholder='例如: {"numbers": [1,2,3,4,5]}' required>[1, 2, 3, 4, 5]</textarea>
                    </div>
                    <div class="form-group">
                        <label for="processing_code">处理逻辑 (PHP代码)：</label>
                        <textarea id="processing_code" name="processing_code" placeholder='例如: return array_sum($input);' required>return array_sum($input);</textarea>
                    </div>
                    <button type="submit">处理数据</button>
                </form>

                <?php
                // 漏洞3：数据处理中的动态代码执行
                if (isset($_GET['action']) && $_GET['action'] === 'process' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo '<div class="result">';
                    echo '<h4>处理结果：</h4>';

                    $input = json_decode($_POST['input_data'], true);
                    $code = $_POST['processing_code'];

                    try {
                        // 危险！eval数据处理逻辑
                        $result = eval("
                                \$input = " . var_export($input, true) . ";
                                {$code}
                            ");

                        echo "<pre>输入数据:\n" . print_r($input, true) . "\n\n处理结果:\n" . print_r($result, true) . "</pre>";
                    } catch (Exception $e) {
                        echo '<div class="alert alert-danger">处理错误: ' . htmlspecialchars($e->getMessage()) . '</div>';
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <div class="footer">
        <p><?= APP_NAME ?> &copy; <?= date('Y') ?> | 管理员联系: <?= ADMIN_EMAIL ?></p>
        <p>最后访问IP: <?= $_SERVER['REMOTE_ADDR'] ?? '未知' ?></p>
    </div>
</div>
</body>
</html>