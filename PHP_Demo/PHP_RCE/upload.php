<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["fileToUpload"]["name"]);

    // 这里使用系统命令 move 来移动文件，存在安全风险
    $cmd = "move " . $_FILES["fileToUpload"]["tmp_name"] . " " . $target_file;
    echo $cmd;


    if (exec($cmd)) {
        echo "<div class='container mx-auto py-10 text-center text-green-500 font-bold animate-fade-in'>文件 ". htmlspecialchars( basename( $_FILES["fileToUpload"]["name"])). " 已成功上传。</div>";
    } else {
        echo "<div class='container mx-auto py-10 text-center text-red-500 font-bold animate-fade-in'>抱歉，文件上传失败。</div>";
    }
}
?>