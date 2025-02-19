<?php
// 引入数据库连接文件
include('db.php');

// 定义获取预约信息的函数
function getAppointments($PDO) {
    try {
        $sql = "SELECT d.name AS doctor_name, a.patient_name, a.patient_phone, a.message, a.appointment_time 
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.id
                ORDER BY a.appointment_time ASC";
        $stmt = $PDO->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 若出现错误，输出错误信息
        echo "数据库查询出错: ". $e->getMessage();
        return [];
    }
}
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>实时预约信息显示</title>
    <style>
        /* 全局样式 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background-color: #f4f4f4;
        }

        /* 主容器样式 */
        .main-container {
            width: 90%;
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 20px;
            text-align: center;
        }

        /* 表格容器样式 */
        .table-container {
            width: 100%;
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
        }

        table {
            width: auto;
            border-collapse: collapse;
            min-width: 600px;
            white-space: nowrap;
            margin: 0 auto; /* 表格水平居中 */
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #f2f2f2;
        }

        /* 表头固定样式，仅在大屏幕设备显示 */
        @media (min-width: 768px) {
            .table-container {
                position: relative;
            }

            table thead th {
                position: sticky;
                top: 0;
                background-color: #f2f2f2;
                z-index: 1;
            }
        }

        /* 小屏幕设备样式调整 */
        @media (max-width: 767px) {
            table {
                font-size: 14px;
            }

            th,
            td {
                padding: 8px;
            }
        }
    </style>
</head>

<body>
    <div class="main-container">
        <h2>实时预约信息</h2>
        <div class="table-container">
            <table id="appointment-table">
                <thead>
                    <tr>
                        <th>医生姓名</th>
                        <th>预约者姓名</th>
                        <th>预约者手机</th>
                        <th>预约者留言</th>
                        <th>预约时间</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // 获取预约信息
                    $appointments = getAppointments($PDO);
                    foreach ($appointments as $appointment) {
                        echo '<tr>';
                        echo '<td>' . $appointment['doctor_name'] . '</td>';
                        echo '<td>' . $appointment['patient_name'] . '</td>';
                        echo '<td>' . $appointment['patient_phone'] . '</td>';
                        echo '<td>' . $appointment['message'] . '</td>';
                        echo '<td>' . $appointment['appointment_time'] . '</td>';
                        echo '</tr>';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function updateAppointments() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'appointment_display.php', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(xhr.responseText, 'text/html');
                    const newTableBody = doc.querySelector('#appointment-table tbody');
                    const oldTableBody = document.querySelector('#appointment-table tbody');
                    oldTableBody.innerHTML = newTableBody.innerHTML;
                }
            };
            xhr.send();
        }

        // 每隔 10 秒更新一次预约信息
        setInterval(updateAppointments, 10000);
    </script>
</body>

</html>