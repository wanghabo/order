<?php
// 引入数据库连接文件
include('db.php');

// 定义每页显示的记录数
$limit = 20;

// 获取当前页码，若未设置则默认为第 1 页
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// 计算偏移量
$offset = ($page - 1) * $limit;

// 定义获取预约信息的函数
function getAppointments($PDO, $limit, $offset) {
    try {
        // 修改排序规则为按预约时间降序排列，让时间最近的信息在最上面
        $sql = "SELECT d.name AS doctor_name, a.patient_name, a.patient_phone, a.message, a.appointment_time, 
                a.registration_confirmed, a.doctor_confirmed, a.id as appointment_id
                FROM appointments a
                JOIN doctors d ON a.doctor_id = d.id
                ORDER BY a.appointment_time DESC
                LIMIT :limit OFFSET :offset";
        $stmt = $PDO->prepare($sql);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        // 若出现错误，输出错误信息
        echo "数据库查询出错: ". $e->getMessage();
        return [];
    }
}

// 获取总记录数
try {
    $countSql = "SELECT COUNT(*) as total FROM appointments a JOIN doctors d ON a.doctor_id = d.id";
    $countStmt = $PDO->prepare($countSql);
    $countStmt->execute();
    $totalRecords = $countStmt->fetch(PDO::FETCH_ASSOC)['total'];
} catch (PDOException $e) {
    echo "数据库查询出错: ". $e->getMessage();
    $totalRecords = 0;
}

// 计算总页数
$totalPages = ceil($totalRecords / $limit);
?>

<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>实时预约信息显示</title>
    <style>
        /* 重置默认的 margin 和 padding */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }

        .main-container {
            max-width: 1080px;
            margin: 0 auto;
            padding: 20px;
            border: 2px solid #ccc;
            box-shadow: 0 0 0 5px #fff, 0 0 0 7px #ccc;
            border-radius: 10px;
            background-color: #fff;
        }

        .appointment-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            justify-content: center;
            margin-top: 20px;
        }

        .info-box {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 5px;
            padding: 20px;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            width: 100%;
            min-height: 100px;
            height: auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .info-content-box {
            border: 1px solid #eee;
            border-radius: 5px;
            padding: 10px;
            width: 70%;
        }

        .button-container {
            display: flex;
            flex-direction: column;
            gap: 10px;
            width: 25%;
        }

        .confirm-button,
        .doctor-confirm-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
            border-radius: 5px;
        }

        .confirm-button:hover,
        .doctor-confirm-button:hover {
            background-color: #45a049;
        }

        .disabled {
            background-color: #ccc;
            cursor: not-allowed;
        }

        .pagination {
            margin-top: 20px;
            text-align: center;
        }

        .pagination a {
            display: inline-block;
            padding: 8px 16px;
            text-decoration: none;
            color: #000;
            border: 1px solid #ddd;
            margin: 0 4px;
        }

        .pagination a.active {
            background-color: #4CAF50;
            color: white;
            border: 1px solid #4CAF50;
        }

        .pagination a:hover:not(.active) {
            background-color: #ddd;
        }
    </style>
</head>

<body>
    <div class="main-container">
        <div class="appointment-container">
            <?php
            // 获取预约信息
            $appointments = getAppointments($PDO, $limit, $offset);
            // 跳过第一个元素
            foreach (array_slice($appointments, 1) as $index => $appointment) {
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
                    echo '<button class="confirm-button" data-appointment-id="' . $appointment['appointment_id'] . '" data-type="registration">登记确认</button>';
                }
                if ($appointment['doctor_confirmed']) {
                    echo '<button class="doctor-confirm-button disabled" disabled>已确认</button>';
                } else {
                    echo '<button class="doctor-confirm-button" data-appointment-id="' . $appointment['appointment_id'] . '" data-type="doctor">医生确认</button>';
                }
                echo '</div>';
                echo '</div>';
            }
            ?>
        </div>
        <!-- 翻页按钮 -->
        <div class="pagination">
            <?php
            if ($page > 1) {
                echo '<a href="?page='. ($page - 1). '">上一页</a>';
            }
            for ($i = 1; $i <= $totalPages; $i++) {
                if ($i == $page) {
                    echo '<a class="active" href="?page='. $i. '">'. $i. '</a>';
                } else {
                    echo '<a href="?page='. $i. '">'. $i. '</a>';
                }
            }
            if ($page < $totalPages) {
                echo '<a href="?page='. ($page + 1). '">下一页</a>';
            }
            ?>
        </div>
    </div>

    <script>
        function handleButtonClick(button) {
            const appointmentId = button.dataset.appointmentId;
            const type = button.dataset.type;
            fetch('confirm_appointment.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded'
                },
                body: `appointment_id=${appointmentId}&type=${type}`
            })
           .then(response => response.json())
           .then(data => {
                if (data.success) {
                    button.textContent = '已确认';
                    button.classList.add('disabled');
                    button.disabled = true;
                }
            });
        }

        function bindButtonEvents() {
            const confirmButtons = document.querySelectorAll('.confirm-button:not(.disabled)');
            const doctorConfirmButtons = document.querySelectorAll('.doctor-confirm-button:not(.disabled)');

            confirmButtons.forEach(button => {
                button.addEventListener('click', function () {
                    handleButtonClick(this);
                });
            });

            doctorConfirmButtons.forEach(button => {
                button.addEventListener('click', function () {
                    handleButtonClick(this);
                });
            });
        }

        function updateAppointments() {
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'appointment_display.php?page=<?php echo $page; ?>', true);
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(xhr.responseText, 'text/html');
                    const newAppointmentContainer = doc.querySelector('.appointment-container');
                    const oldAppointmentContainer = document.querySelector('.appointment-container');
                    oldAppointmentContainer.innerHTML = newAppointmentContainer.innerHTML;

                    // 重新绑定点击事件
                    bindButtonEvents();
                }
            };
            xhr.send();
        }

        // 初始化绑定事件
        bindButtonEvents();

        // 每隔 10 秒更新一次预约信息
        setInterval(updateAppointments, 10000);
    </script>
</body>

</html>