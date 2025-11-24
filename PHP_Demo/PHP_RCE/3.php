<?php
// config.php - 系统配置
define('APP_NAME', '企业数据分析平台');
define('VERSION', '2.3.1');
$departments = ['销售', '财务', '人力资源', 'IT'];

// 模拟数据库连接
class Database {
    public static function query($sql) {
        // 实际应用中这里会连接真实数据库
        $mockData = [
            ['id' => 1, 'name' => '张三', 'sales' => 15000, 'department' => '销售'],
            ['id' => 2, 'name' => '李四', 'sales' => 23000, 'department' => '销售'],
            ['id' => 3, 'name' => '王五', 'salary' => 8000, 'department' => '财务']
        ];

        // 简单模拟SQL查询
        if (stripos($sql, 'sales') !== false) {
            return array_filter($mockData, function($item) {
                return isset($item['sales']);
            });
        }
        return $mockData;
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> v<?= VERSION ?></title>
    <style>
        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            color: #333;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            background: #fff;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        .header {
            background: #2c3e50;
            color: white;
            padding: 20px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .nav {
            background: #34495e;
            padding: 15px 30px;
        }
        .nav a {
            color: #ecf0f1;
            text-decoration: none;
            margin-right: 20px;
            font-weight: 500;
        }
        .content {
            padding: 30px;
        }
        .card {
            background: white;
            border-radius: 5px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 30px;
            overflow: hidden;
        }
        .card-header {
            background: #f8f9fa;
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
            font-size: 18px;
            font-weight: 500;
        }
        .card-body {
            padding: 20px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
        }
        select, input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 15px;
        }
        textarea {
            min-height: 150px;
            font-family: monospace;
        }
        button {
            background: #3498db;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #2980b9;
        }
        .result {
            margin-top: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 4px;
            border-left: 4px solid #3498db;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="header">
        <h1><?= APP_NAME ?></h1>
        <div>版本 <?= VERSION ?></div>
    </div>

    <div class="nav">
        <a href="#report">报表生成</a>
        <a href="#custom">自定义分析</a>
        <a href="#formulas">公式管理</a>
    </div>

    <div class="content">
        <!-- 报表生成模块 -->
        <div class="card" id="report">
            <div class="card-header">
                动态报表生成
            </div>
            <div class="card-body">
                <form method="POST" action="?action=generate_report">
                    <div class="form-group">
                        <label for="report_type">报表类型</label>
                        <select id="report_type" name="report_type">
                            <option value="sales">销售业绩</option>
                            <option value="employee">员工信息</option>
                            <option value="custom">自定义报表</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="department">部门筛选</label>
                        <select id="department" name="department">
                            <option value="">全部部门</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?= htmlspecialchars($dept) ?>"><?= htmlspecialchars($dept) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group" id="custom-formula-group" style="display:none;">
                        <label for="custom_formula">自定义计算逻辑 (PHP代码)</label>
                        <textarea id="custom_formula" name="custom_formula" placeholder="例如: return array_sum(array_column($data, 'sales'));"></textarea>
                        <p class="alert alert-warning">
                            提示: 使用$data变量访问查询结果，必须返回计算结果
                        </p>
                    </div>

                    <button type="submit">生成报表</button>
                </form>

                <?php
                // 漏洞1：报表生成中的eval执行
                if (isset($_GET['action']) && $_GET['action'] === 'generate_report' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    echo '<div class="result">';

                    $reportType = $_POST['report_type'];
                    $department = $_POST['department'];
                    $customFormula = $_POST['custom_formula'] ?? '';

                    // 模拟数据库查询
                    $sql = "SELECT * FROM employees" . ($department ? " WHERE department = '$department'" : "");
                    $data = Database::query($sql);

                    if ($reportType === 'custom' && !empty($customFormula)) {
                        echo '<h3>自定义分析结果</h3>';

                        try {
                            // 高危！直接eval用户提供的计算逻辑
                            $result = eval("
                                    \$data = " . var_export($data, true) . ";
                                    $customFormula
                                ");

                            echo "<p>计算结果: <strong>" . htmlspecialchars($result) . "</strong></p>";
                        } catch (Exception $e) {
                            echo '<p style="color:red;">计算错误: ' . htmlspecialchars($e->getMessage()) . '</p>';
                        }
                    }

                    // 显示数据预览
                    echo '<h3>数据预览 (前10条)</h3>';
                    echo '<table><tr>';
                    foreach (array_keys($data[0] ?? []) as $column) {
                        echo '<th>' . htmlspecialchars($column) . '</th>';
                    }
                    echo '</tr>';

                    foreach (array_slice($data, 0, 10) as $row) {
                        echo '<tr>';
                        foreach ($row as $value) {
                            echo '<td>' . htmlspecialchars($value) . '</td>';
                        }
                        echo '</tr>';
                    }
                    echo '</table>';

                    echo '</div>';
                }
                ?>
            </div>
        </div>

        <!-- 公式管理模块 -->
        <div class="card" id="formulas">
            <div class="card-header">
                常用公式管理
            </div>
            <div class="card-body">
                <form method="POST" action="?action=save_formula">
                    <div class="form-group">
                        <label for="formula_name">公式名称</label>
                        <input type="text" id="formula_name" name="formula_name" required>
                    </div>

                    <div class="form-group">
                        <label for="formula_code">公式逻辑 (PHP代码)</label>
                        <textarea id="formula_code" name="formula_code" required placeholder="例如:
// 计算平均销售额
return array_sum(array_column(\$data, 'sales')) / count(\$data);"></textarea>
                    </div>

                    <button type="submit">保存公式</button>
                </form>

                <?php
                // 漏洞2：公式保存与动态执行
                if (isset($_GET['action']) && $_GET['action'] === 'save_formula' && $_SERVER['REQUEST_METHOD'] === 'POST') {
                    $name = $_POST['formula_name'];
                    $code = $_POST['formula_code'];

                    // 模拟保存公式到数据库
                    $formulaFile = 'formulas/' . preg_replace('/[^a-z0-9_]/i', '_', $name) . '.php';
                    file_put_contents($formulaFile, "<?php\n// $name\nreturn function(\$data) {\n$code\n};\n");

                    echo '<div class="result">';
                    echo '<p>公式已保存: <strong>' . htmlspecialchars($name) . '</strong></p>';

                    // 测试执行保存的公式
                    $testData = Database::query("SELECT * FROM employees LIMIT 5");
                    try {
                        // 高危！动态include并执行用户提供的代码
                        $formulaFunc = include $formulaFile;
                        $testResult = $formulaFunc($testData);

                        echo '<p>测试结果: <strong>' . htmlspecialchars($testResult) . '</strong></p>';
                    } catch (Exception $e) {
                        echo '<p style="color:red;">测试执行失败: ' . htmlspecialchars($e->getMessage()) . '</p>';
                    }

                    echo '</div>';
                }
                ?>
            </div>
        </div>
    </div>
</div>

<script>
    // 显示/隐藏自定义公式输入框
    document.getElementById('report_type').addEventListener('change', function() {
        document.getElementById('custom-formula-group').style.display =
            this.value === 'custom' ? 'block' : 'none';
    });
</script>
</body>
</html>