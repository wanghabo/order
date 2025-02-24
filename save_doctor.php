<!--保存添加的医生-->
<?php
include('db.php');

// 处理医生信息保存逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $name = $_POST['name']?? '';
    $department = $_POST['department']?? '';
    $remarks = $_POST['remarks']?? '';

    // 处理文件上传
    $avatar = $_FILES['avatar']?? null;
    $avatarPath = '';
    if ($avatar && $avatar['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/'; // 上传目录
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        $avatarPath = $uploadDir. basename($avatar['name']);
        move_uploaded_file($avatar['tmp_name'], $avatarPath);
    }

    // 数据验证
    if (empty($name) || empty($department)) {
        // 数据不完整，重定向到添加医生页面并传递错误信息
        header("Location: add_doctor.php?success=0&error=". urlencode("请提交正确的信息，您提交的信息不完整或者有错误"));
        exit;
    }

    // 如果数据完整，插入数据到数据库
    $sql = "INSERT INTO doctors (name, department, remarks, avatar) VALUES (:name, :department, :remarks, :avatar)";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':remarks', $remarks);
    $stmt->bindParam(':avatar', $avatarPath);

    if ($stmt->execute()) {
        // 保存成功，重定向到添加医生页面并传递成功信息
        header("Location: add_doctor.php?success=1");
        exit;
    } else {
        // 保存失败，重定向到添加医生页面并传递失败信息
        header("Location: add_doctor.php?success=0&error=". urlencode("数据库插入失败"));
        exit;
    }
}
?>