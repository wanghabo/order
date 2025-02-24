<!--预约保存-->
<?php
session_start(); // 开启会话
include('db.php');

// 处理预约信息保存逻辑
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 获取表单数据
    $doctor_id = $_POST['doctor_id']?? '';
    $patient_name = $_POST['patient_name']?? '';
    $patient_phone = $_POST['patient_phone']?? '';
    $message = $_POST['message']?? '';
    $appointment_time = $_POST['appointment_time']?? '';

    // 只验证 $message
    if (empty($message)) {
        // 数据不完整，重定向到预约页面并传递错误信息
        header("Location: index.php?success=0&error=". urlencode("请提交正确的信息，留言内容不能为空"));
        exit;
    }

    // 如果 doctor_id 为空字符串，转换为 NULL
    $doctor_id = empty($doctor_id) ? null : $doctor_id;

    // 如果数据完整，插入数据到数据库
    $sql = "INSERT INTO appointments (doctor_id, patient_name, patient_phone, message, appointment_time) VALUES (:doctor_id, :patient_name, :patient_phone, :message, :appointment_time)";
    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':doctor_id', $doctor_id, PDO::PARAM_INT);
    $stmt->bindParam(':patient_name', $patient_name);
    $stmt->bindParam(':patient_phone', $patient_phone);
    $stmt->bindParam(':message', $message);
    $stmt->bindParam(':appointment_time', $appointment_time);

    if ($stmt->execute()) {
        // 保存成功，设置会话变量
        $_SESSION['appointment_success'] = true;
        // 重定向到预约页面
        header("Location: index.php");
        exit;
    } else {
        // 保存失败，重定向到预约页面并传递失败信息
        header("Location: index.php?success=0&error=". urlencode("数据库插入失败"));
        exit;
    }
}
?>