<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>计算器工具 - 演示应用</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            color: #333;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            color: #2c3e50;
            text-align: center;
            border-bottom: 2px solid #eee;
            padding-bottom: 10px;
        }
        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        h2 {
            color: #3498db;
            margin-top: 0;
        }
        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #2980b9;
        }
        .result {
            margin-top: 15px;
            padding: 10px;
            background: #e8f4fc;
            border-left: 4px solid #3498db;
            word-wrap: break-word;
        }
        .warning {
            color: #e74c3c;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>计算器工具</h1>

    <div class="section">
        <h2>数学表达式计算</h2>
        <p>输入一个数学表达式，我们将为您计算结果。</p>
        <form method="POST">
            <label for="expression">数学表达式:</label>
            <input type="text" id="expression" name="expression" placeholder="例如: 2+3*5" required>
            <button type="submit">计算</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['expression'])) {
            echo '<div class="result">';
            echo '<strong>结果:</strong><br>';

            // 不安全的eval使用 - 代码执行漏洞
            $expression = $_POST['expression'];
            try {
                eval("\$result = $expression;");
                echo htmlspecialchars($expression) . " = " . htmlspecialchars($result);
            } catch (Exception $e) {
                echo "计算错误: " . htmlspecialchars($e->getMessage());
            }

            echo '</div>';
        }
        ?>

        <p class="warning">安全提示: 此功能使用eval()函数直接执行用户输入，存在严重安全风险！</p>
    </div>

    <div class="section">
        <h2>系统信息查询</h2>
        <p>输入一个系统命令，我们将为您执行并返回结果。</p>
        <form method="GET">
            <label for="command">系统命令:</label>
            <input type="text" id="command" name="command" placeholder="例如: ls -l" required>
            <button type="submit">执行</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['command'])) {
            echo '<div class="result">';
            echo '<strong>命令结果:</strong><br>';

            // 不安全的命令执行 - RCE漏洞
            $command = $_GET['command'];
            echo "<pre>" . htmlspecialchars(shell_exec($command)) . "</pre>";

            echo '</div>';
        }
        ?>

        <p class="warning">安全提示: 此功能直接执行用户输入的系统命令，存在严重安全风险！</p>
    </div>

    <div class="footer">
        <p>此应用仅用于演示不安全的代码执行和命令执行功能可能导致的RCE漏洞</p>
        <p>切勿在生产环境中使用此类代码</p>
    </div>
</div>
</body>
</html>