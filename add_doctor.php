<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <title>添加医生</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 20px;
        }
        form {
            width: 400px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-bottom: 5px;
        }
        input, textarea {
            width: 100%;
            padding: 8px;
            margin-bottom: 15px;
        }
        input[type="file"] {
            margin-bottom: 20px;
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
    </style>
</head>
<body>
    <h2>添加医生</h2>
    <form action="save_doctor.php" method="post" enctype="multipart/form-data">
        <label for="name">医生姓名:</label>
        <input type="text" name="name" id="name" required>
        <label for="department">科室:</label>
        <input type="text" name="department" id="department" required>
        <label for="remarks">备注:</label>
        <textarea name="remarks" id="remarks"></textarea>
        <label for="avatar">医生头像:</label>
        <input type="file" name="avatar" id="avatar">
        <input type="submit" value="添加医生">
    </form>
</body>
</html>