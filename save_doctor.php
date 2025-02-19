<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $department = $_POST['department'];
    $remarks = $_POST['remarks'];

    $avatar = "";
    if (isset($_FILES['avatar']) && $_FILES['avatar']['error'] === 0) {
        $target_dir = "uploads/";
        // 检查目录是否存在，如果不存在则创建
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0755, true);
        }
        $target_file = $target_dir . basename($_FILES["avatar"]["name"]);
        $imageFileType = strtolower(pathinfo($target_file,PATHINFO_EXTENSION));

        // 允许的文件类型
        $allowed_types = array("jpg", "jpeg", "png", "gif");
        if (in_array($imageFileType, $allowed_types)) {
            if (move_uploaded_file($_FILES["avatar"]["tmp_name"], $target_file)) {
                $avatar = $target_file;
            } else {
                echo "头像上传失败。";
            }
        } else {
            echo "不允许的文件类型。";
        }
    }

    try {
        $sql = "INSERT INTO doctors (name, department, remarks, avatar) 
                VALUES (:name, :department, :remarks, :avatar)";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':department', $department);
        $stmt->bindParam(':remarks', $remarks);
        $stmt->bindParam(':avatar', $avatar);
        $stmt->execute();
        echo "医生添加成功。";
    } catch (PDOException $e) {
        echo "保存医生信息时出错: ". $e->getMessage();
    }
}
?>