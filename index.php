<!--  在6index.php基础上修改 要让页面根据天气显示下雨、晴天或阴天的背景，你需要获取当前的天气信息，一般可以通过调用第三方天气 API 来实现。这里以和风天气的免费 API 为例，不过使用前你需要去 [和风天气官网](https://dev.qweather.com/) 注册账号并获取 API Key。-->

<!--以下是修改后的代码示例：-->


<?php
include('db.php');

// 获取医生列表
$sql = "SELECT * FROM doctors";
$stmt = $PDO->prepare($sql);
$stmt->execute();
$doctors = $stmt->fetchAll();
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
    </style>
</head>

<body>
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
    </form>

    <script>
        // 替换为你自己的和风天气 API Key
        const API_KEY = 'your_api_key';
        // 这里以北京为例，你可以根据实际情况修改城市代码
        const CITY_ID = '101010100';
        const API_URL = `https://devapi.qweather.com/v7/weather/now?location=${CITY_ID}&key=${API_KEY}`;

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

        fetch(API_URL)
          .then(response => response.json())
          .then(data => {
                const weatherCode = parseInt(data.now.icon);
                setBackgroundBasedOnWeather(weatherCode);
            })
          .catch(error => {
                console.error('获取天气信息失败:', error);
            });
    </script>
</body>

</html>

<!--### 代码说明：-->
<!--1. **HTML 和 CSS 部分**：-->
<!--    - 在 CSS 中定义了三种不同天气状态（晴天、下雨、阴天）对应的背景图片类名，分别是 `sunny`、`rainy`、`cloudy`，你需要准备对应的图片文件（`sunny.jpg`、`rainy.jpg`、`cloudy.jpg`）并放在与 HTML 文件相同的目录下。-->
<!--2. **JavaScript 部分**：-->
<!--    - 定义了和风天气的 API 请求 URL，你需要将 `API_KEY` 替换为你自己在和风天气官网获取的 API Key，`CITY_ID` 可以根据实际需求修改为对应的城市代码。-->
<!--    - `setBackgroundBasedOnWeather` 函数根据天气代码来为 `body` 元素添加相应的类名，从而改变背景图片。-->
<!--    - 使用 `fetch` 方法发送请求获取天气信息，成功后调用 `setBackgroundBasedOnWeather` 函数设置背景，失败则在控制台输出错误信息。-->

<!--通过以上修改，页面会根据当前的天气情况显示不同的背景图片。 -->