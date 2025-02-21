<?php
// 设置上传目录
$uploadDir = 'uploads/';
if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0777, true); // 确保目录存在，如果不存在则创建
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    $uploadFile = $uploadDir . basename($_FILES['image']['name']);
    $fileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));

    // 检查文件类型，仅允许jpg和jpeg文件
    if ($fileType != 'jpg' && $fileType != 'jpeg') {
        echo "<p>仅支持上传jpg或jpeg格式的图片文件。</p>";
    } else {
        // 检查文件是否上传成功
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
            // 读取EXIF信息
            $exifData = exif_read_data($uploadFile, 0, true);

            if ($exifData === false) {
                echo "<p>该图片没有EXIF信息。</p>";
            } else {
                // 输出EXIF信息（调试用）
                // echo '<pre>';
                // print_r($exifData);
                // echo '</pre>';

                // 提取所需的信息
                // 使用isset确保字段存在
                $fileName = isset($exifData['FILE']['FileName']) ? $exifData['FILE']['FileName'] : 'Unknown';
                $fileDateTime = isset($exifData['FILE']['FileDateTime']) ? $exifData['FILE']['FileDateTime'] : 'Unknown';
                $fileSize = isset($exifData['FILE']['FileSize']) ? $exifData['FILE']['FileSize'] : 'Unknown';
                $fileMimeType = isset($exifData['FILE']['MimeType']) ? $exifData['FILE']['MimeType'] : 'Unknown';
                $artist = isset($exifData['IFD0']['Artist']) ? $exifData['IFD0']['Artist'] : 'Unknown';

                // 读取图像尺寸
                $imageWidth = isset($exifData['COMPUTED']['Width']) ? $exifData['COMPUTED']['Width'] : 'Unknown';
                $imageHeight = isset($exifData['COMPUTED']['Height']) ? $exifData['COMPUTED']['Height'] : 'Unknown';

                // 输出信息
                echo "<!DOCTYPE html>
                <html>
                <head>
                    <meta http-equiv='content-type' content='text/html;charset=utf-8'>
                    <title>level14 - EXIF Information</title>
                </head>
                <body>
                    <h1>EXIF Information</h1>
                    <p>File Name: $fileName</p>
                    <p>File Date Time: $fileDateTime</p>
                    <p>File Size: $fileSize</p>
                    <p>MIME Type: $fileMimeType</p>
                    <p>Image Width: $imageWidth</p>
                    <p>Image Height: $imageHeight</p>
                    <p>Author: $artist</p>

                    <h2>Uploaded Image</h2>
                    <img src='$uploadFile' alt='Uploaded Image' style='max-width:100%;'>

                    <p><a href='level14.php'>返回上传页面</a></p>
                    <p><strong>注意：</strong> 本页面仅用于演示目的，上传的图片将不会被存储或发送到服务器。</p>
                    <p>成功后不会自动跳转。成功者<a href='level15.php?src=1.gif'>点我进level15</a></p>
                </body>
                </html>";
            }
        } else {
            // 获取上传错误代码
            $errorCode = $_FILES['image']['error'];
            $errorMessage = '';

            // 根据错误代码设置错误信息
            switch ($errorCode) {
                case UPLOAD_ERR_INI_SIZE:
                    $errorMessage = '上传的文件超过了php.ini中upload_max_filesize的限制。';
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    $errorMessage = '上传的文件超过了HTML表单中MAX_FILE_SIZE的限制。';
                    break;
                case UPLOAD_ERR_PARTIAL:
                    $errorMessage = '文件只有部分被上传。';
                    break;
                case UPLOAD_ERR_NO_FILE:
                    $errorMessage = '没有文件被上传。';
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    $errorMessage = '找不到临时文件夹。';
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    $errorMessage = '文件写入失败。';
                    break;
                case UPLOAD_ERR_EXTENSION:
                    $errorMessage = '文件上传被PHP扩展阻止。';
                    break;
                default:
                    $errorMessage = '未知错误，上传失败。';
                    break;
            }

            // 输出错误信息
            echo "<!DOCTYPE html>
            <html>
            <head>
                <meta http-equiv='content-type' content='text/html;charset=utf-8'>
                <title>Upload Failed</title>
            </head>
            <body>
                <h1>上传失败</h1>
                <p>失败原因：$errorMessage</p>
                <p><a href='level14.php'>返回上传页面</a></p>
            </body>
            </html>";
        }
    }
} else {
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta http-equiv="content-type" content="text/html;charset=utf-8">
        <title>level14 Upload Image to View EXIF</title>
    </head>
    <body>
    <h1>欢迎来到level14 - 查看图片EXIF信息</h1>
    <p>你可以试试使用图片的作者名称属性进行XSS攻击哦。</p>
    <form action="level14.php" method="post" enctype="multipart/form-data">
        <label for="image">Choose image to upload:</label>
        <input type="file" name="image" id="image" accept="image/jpeg">
        <input type="submit" value="Upload Image">
    </form>
    <p><strong>注意：</strong> 本页面仅用于演示目的，上传的图片将不会被存储或发送到服务器。</p>
    <p>成功后不会自动跳转。成功者<a href="/level15.php?src=1.gif">点我进level15</a></p>
    </body>
    </html>
    <?php
}
?>