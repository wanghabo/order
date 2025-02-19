<?php
include('db.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $doctor_id = $_POST['doctor_id'];
    $patient_name = $_POST['patient_name'];
    $patient_phone = $_POST['patient_phone'];
    $message = $_POST['message'];
    $appointment_time = $_POST['appointment_time'];

    // 检查名字是否包含敏感词汇
    $restricted_names = array("管理员", "超级管理员", "admin", "root", "administrator");
    foreach ($restricted_names as $restricted_name) {
        if (strpos($patient_name, $restricted_name) !== false) {
            echo "包含禁止使用的名称，无法提交预约。";
            return;
        }
    }

    // 检查 10 分钟内是否有相同姓名或者相同手机的提交记录
    $ten_minutes_ago = date('Y-m-d H:i:s', strtotime('-10 minutes'));
    $check_sql = "SELECT * FROM appointments 
                  WHERE (patient_name = :patient_name OR patient_phone = :patient_phone) 
                  AND created_at >= :ten_minutes_ago";
    $check_stmt = $PDO->prepare($check_sql);
    $check_stmt->bindParam(':patient_name', $patient_name);
    $check_stmt->bindParam(':patient_phone', $patient_phone);
    $check_stmt->bindParam(':ten_minutes_ago', $ten_minutes_ago);
    $check_stmt->execute();

    if ($check_stmt->rowCount() > 0) {
        echo "你在 10 分钟内已经提交过预约，请稍后再试。";
    } else {
        try {
            // 假设你的 appointments 表有 created_at 字段来记录创建时间
            $insert_sql = "INSERT INTO appointments (doctor_id, patient_name, patient_phone, message, appointment_time, created_at) 
                           VALUES (:doctor_id, :patient_name, :patient_phone, :message, :appointment_time, NOW())";
            $insert_stmt = $PDO->prepare($insert_sql);
            $insert_stmt->bindParam(':doctor_id', $doctor_id);
            $insert_stmt->bindParam(':patient_name', $patient_name);
            $insert_stmt->bindParam(':patient_phone', $patient_phone);
            $insert_stmt->bindParam(':message', $message);
            $insert_stmt->bindParam(':appointment_time', $appointment_time);
            $insert_stmt->execute();
            echo "预约成功。";
        } catch (PDOException $e) {
            echo "保存预约信息时出错: ". $e->getMessage();
        }
    }
}
?>