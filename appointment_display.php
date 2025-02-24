<!--实时预约显示按钮状态记录-->
<?php
// 引入数据库连接文件
include('db.php');

// 定义每页显示的记录数
$limit = 20;

// 获取当前页码，若未设置则默认为第 1 页
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 计算偏移量
$offset = ($page - 1) * $limit;

// 获取预约信息
$appointments = getAppointments($PDO, $limit, $offset);
$first = true;
foreach ($appointments as $index => $appointment) {
    if ($first) {
        $first = false;
        continue; // 跳过第一个元素
    }
    echo '<div class="info-box">';
    echo '<div class="info-content-box">';
    echo '<p>医生姓名: ' . $appointment['doctor_name'] . '</p>';
    echo '<p>预约者姓名: ' . $appointment['patient_name'] . '</p>';
    echo '<p>预约者电话: ' . $appointment['patient_phone'] . '</p>';
    echo '<p>预约者留言: ' . $appointment['message'] . '</p>';
    echo '<p>就诊时间: ' . $appointment['appointment_time'] . '</p>';
    echo '</div>';
    echo '<div class="button-container">';
    if ($appointment['registration_confirmed']) {
        echo '<button class="confirm-button disabled" disabled>已确认</button>';
    } else {
        echo '<button class="confirm-button" id="registration-confirm-' . $index . '" data-appointment-id="' . $appointment['id'] . '">登记确认</button>';
    }
    if ($appointment['doctor_confirmed']) {
        echo '<button class="doctor-confirm-button disabled" disabled>已确认</button>';
    } else {
        echo '<button class="doctor-confirm-button" id="doctor-confirm-' . $index . '" data-appointment-id="' . $appointment['id'] . '">医生确认</button>';
    }
    echo '</div>';
    echo '</div>';
}
?>