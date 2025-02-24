<?php
try {
    $dsn = 'mysql:dbname=order;host=127.0.0.1';
    $PDO = new PDO($dsn, 'order', 'adminhlkj99'); // 请根据实际情况修改用户名和密码
    $PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "数据库连接失败: ". $e->getMessage();
    die();
}
?>