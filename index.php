<!--预约主页-->
<?php
session_start(); // 开启会话
include('db.php');

// 获取医生列表
$sql = "SELECT * FROM doctors";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$doctors = $stmt->fetchAll();

// 检查是否有预约成功的信息
$showSuccessMessage = false;
$showErrorMessage = isset($_GET['success']) && $_GET['success'] == 0;
$errorMessage = $showErrorMessage ? urldecode($_GET['error']) : '';

if (isset($_SESSION['appointment_success']) && $_SESSION['appointment_success']) {
    $showSuccessMessage = true;
    // 显示提示框后清除会话变量
    unset($_SESSION['appointment_success']);
}
?>
<!DOCTYPE html>
<html lang="zh-CN">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>预约系统</title>
    <style>
        /* 全局样式 */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
            min-height: 100vh;
            background-size: cover;
            background-position: center;
        }

        h2 {
            margin-top: 20px;
            margin-bottom: 20px;
            text-align: center;
        }

        form {
            width: 95%;
            max-width: 400px;
            margin: 0 auto;
            padding: 20px;
            box-sizing: border-box;
        }

        label {
            display: block;
            margin-bottom: 5px;
        }

        input,
        select,
        textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        /* 医生信息显示区域样式 */
        #doctor-info {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-top: 10px;
        }

        #doctor-info img {
            width: 100px;
            height: 100px;
            object-fit: cover;
        }

        #doctor-name {
            display: none;
        }

        /* 媒体查询，为电脑和平板添加双层花边 */
        @media (min-width: 768px) {
            form {
                border: 5px solid #ccc;
                border-radius: 10px;
                position: relative;
            }

            form::before {
                content: "";
                position: absolute;
                top: -15px;
                left: -15px;
                right: -15px;
                bottom: -15px;
                border: 5px solid #eee;
                border-radius: 20px;
                z-index: -1;
            }
        }

        /* 不同天气背景 */
        body.sunny {
            background-image: url('sunny.jpg');
        }

        body.rainy {
            background-image: url('rainy.jpg');
        }

        body.cloudy {
            background-image: url('cloudy.jpg');
        }

        /* 二维码弹出框样式 */
        #qr-code-popup {
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            display: none;
            z-index: 999;
        }

        #qr-code-popup img {
            width: 200px;
            height: 200px;
        }

        /* 按钮样式 */
        .additional-button {
            margin-top: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 8px 16px;
            cursor: pointer;
        }

        .additional-button:hover {
            background-color: #0056b3;
        }

        /* 预约成功提示框样式 */
        #success-message {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: none;
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        #success-message-content {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
            position: relative;
        }

        #close-success-message {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
        }

        /* 预约失败提示框样式 */
        #error-message {
            position: fixed;
            top: 20px;
            left: 50%;
            transform: translateX(-50%);
            background-color: #FF5722;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            z-index: 1000;
            display: none;
        }

        #close-error-message {
            position: absolute;
            top: 5px;
            right: 5px;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- 预约成功提示框 -->
    <div id="success-message">
        <div id="success-message-content">
            <span id="close-success-message" onclick="hideSuccessMessage()">&times;</span>
            <h2>预约成功！</h2>
        </div>
    </div>

    <!-- 预约失败提示框 -->
    <div id="error-message">
        <span id="close-error-message" onclick="hideErrorMessage()">&times;</span>
        <span id="error-text"></span>
    </div>

    <h2>预约系统</h2>
    <form action="save_appointment.php" method="post">
        <label for="doctor">选择医生:</label>
        <select name="doctor_id" id="doctor" onchange="updateDoctorInfo(this)">
            <option value="">请选择医生</option>
            <?php foreach ($doctors as $doctor): ?>
                <option value="<?php echo $doctor['id']; ?>" data-name="<?php echo $doctor['name']; ?>" data-avatar="<?php echo 'uploads/' . basename($doctor['avatar']); ?>">
                    <?php echo $doctor['name']; ?>
                </option>
            <?php endforeach; ?>
        </select>
        <div id="doctor-info" style="display:none;">
            <img id="doctor-avatar" src="" alt="">
            <span id="doctor-name"></span>
        </div>
        <label for="patient_name">预约者姓名:</label>
        <input type="text" name="patient_name" id="patient_name" required>
        <label for="patient_phone">预约者手机:</label>
        <input type="tel" name="patient_phone" id="patient_phone" required>
        <label for="message">预约者留言:</label>
        <textarea name="message" id="message"></textarea>
        <label for="appointment_time">预约时间:</label>
        <input type="datetime-local" name="appointment_time" id="appointment_time" required>
        <input type="submit" value="提交预约">

        <!-- 新增的两个按钮 -->
        <button class="additional-button" id="scan-qr-button" onclick="showQRCode()">点击这里扫描二维码添加微信</button>
        <button class="additional-button" id="call-button" onclick="callPhone()">点击这里拨打电话</button>
    </form>

    <!-- 二维码弹出框 -->
    <div id="qr-code-popup">
        <img src="uploads/kfwx.jpg" alt="微信二维码">
        <button id="close-qr-button" onclick="hideQRCode()">关闭</button>
    </div>

    <script>
        // 替换为你自己的和风天气 API Key
        const API_KEY = 'your_api_key';
        // 这里以北京为例，你可以根据实际情况修改城市代码
        const CITY_ID = '101010100';
        const API_URL = `https://devapi.qweather.com/v7/weather/now?location=${CITY_ID}&key=${API_KEY}`;

        // 管理员预先设置的电话号码 点击按钮“点击这里拨打电话”后自动填入手机拨号盘的号码
        const ADMIN_PHONE_NUMBER = '17621270725';

        function updateDoctorInfo(select) {
            var selectedOption = select.options[select.selectedIndex];
            var doctorName = selectedOption.getAttribute('data-name');
            var doctorAvatar = selectedOption.getAttribute('data-avatar');

            var doctorInfoDiv = document.getElementById('doctor-info');
            var doctorNameSpan = document.getElementById('doctor-name');
            var doctorAvatarImg = document.getElementById('doctor-avatar');

            if (selectedOption.value) {
                doctorNameSpan.textContent = doctorName;
                doctorAvatarImg.src = doctorAvatar ? doctorAvatar : 'default_avatar.jpg';
                doctorInfoDiv.style.display = 'flex';
            } else {
                doctorInfoDiv.style.display = 'none';
            }
        }

        function setBackgroundBasedOnWeather(weatherCode) {
            const body = document.body;
            // 根据和风天气的天气代码判断天气类型
            if (weatherCode >= 300 && weatherCode < 400) {
                // 下雨天气
                body.classList.remove('sunny', 'cloudy');
                body.classList.add('rainy');
            } else if (weatherCode >= 100 && weatherCode < 200) {
                // 晴天天气
                body.classList.remove('rainy', 'cloudy');
                body.classList.add('sunny');
            } else {
                // 其他情况认为是阴天
                body.classList.remove('sunny', 'rainy');
                body.classList.add('cloudy');
            }
        }

        // 显示二维码弹出框
        function showQRCode() {
            document.getElementById('qr-code-popup').style.display = 'block';
        }

        // 隐藏二维码弹出框
        function hideQRCode() {
            document.getElementById('qr-code-popup').style.display = 'none';
        }

        // 拨打电话  点击 “点击这里拨打电话” 按钮时，会检查是否设置了管理员电话号码，若已设置，就会弹出手机拨号盘并自动填入该号码；若未设置，会弹出提示框。
        function callPhone() {
            if (ADMIN_PHONE_NUMBER) {
                window.location.href = `tel:${ADMIN_PHONE_NUMBER}`;
            } else {
                alert('未设置管理员电话号码');
            }
        }

        // 显示预约成功提示框
        function showSuccessMessage() {
            document.getElementById('success-message').style.display = 'flex';
        }

        // 隐藏预约成功提示框
        function hideSuccessMessage() {
            document.getElementById('success-message').style.display = 'none';
        }

        // 显示预约失败提示框
        function showErrorMessage() {
            const errorText = "<?php echo $errorMessage; ?>";
            document.getElementById('error-text').textContent = errorText;
            document.getElementById('error-message').style.display = 'block';
        }

        // 隐藏预约失败提示框
        function hideErrorMessage() {
            document.getElementById('error-message').style.display = 'none';
        }

        fetch(API_URL)
          .then(response => response.json())
          .then(data => {
                const weatherCode = parseInt(data.now.icon);
                setBackgroundBasedOnWeather(weatherCode);
            })
          .catch(error => {
                console.error('获取天气信息失败:', error);
            });

        // 页面加载时检查是否显示预约成功或失败提示框
        if (<?php echo json_encode($showSuccessMessage); ?>) {
            showSuccessMessage();
        } else if (<?php echo json_encode($showErrorMessage); ?>) {
            showErrorMessage();
        }
    </script>
</body>

</html>