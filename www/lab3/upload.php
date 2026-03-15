<html>
<head>
  <title>Результат загрузки файла</title>
</head>
<body>
<?php
print_r ($_FILES);
$name = "img/" . $_FILES["filename"]["name"];
        $image_info = getimagesize($_FILES["filename"]["tmp_name"]);
        $image_width = $image_info[0];
        $image_height = $image_info[1];
        if ($image_width > 1000 ||$image_height > 1000)
        {
            echo "<script>alert('Файл слишком большой');document.location.href='upload.html';</script>";
        
        exit;
        }
        else
        {
       if(move_uploaded_file($_FILES["filename"]["tmp_name"], $name))
       {
        echo "Файл успешно загружен <br>";
        echo("Характеристики файла: <br>");
        echo("Имя файла: ");
        echo($_FILES["filename"]["name"]);
        echo("<br>Размер файла: ");
        echo($_FILES["filename"]["size"]);
        echo("<br>Каталог для загрузки: ");
        echo($_FILES["filename"]["tmp_name"]);
        echo("<br>Тип файла: ");
        echo($_FILES["filename"]["type"]);
       } else 
            {
            echo("Ошибка загрузки файла");
            }
        }
?>

</body>
</html>
