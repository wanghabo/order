<!--实时预约显示按钮状态记录-->
<?php
// 引入数据库连接文件
include('db.php');

// 获取 POST 数据
$appointmentId = $_POST['appointment_id'];
$type = $_POST['type'];

try {
    if ($type === 'registration') {
        // 更新登记确认状态
        $sql = "UPDATE appointments SET registration_confirmed = 1 WHERE id = :appointmentId";
    } else if ($type === 'doctor') {
        // 更新医生确认状态
        $sql = "UPDATE appointments SET doctor_confirmed = 1 WHERE id = :appointmentId";
    }

    $stmt = $PDO->prepare($sql);
    $stmt->bindParam(':appointmentId', $appointmentId, PDO::PARAM_INT);
    $stmt->execute();

    // 返回成功响应
    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    // 若出现错误，返回失败响应
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>