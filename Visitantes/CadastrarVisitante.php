<?php include_once "../conectarbd.php"; ?>
<html>
    <body>
        <?php
        $local = $_POST["local"];
        $num_documento = $_POST["num_documento"];
        $telefone = $_POST["telefone"];
        $email = $_POST["email"];
        $data_nascimento = $_POST["data_nascimento"];
        $foto = $_POST["foto"];
        $conn = mysqli_connect($servidor, $dbusuario, $dbsenha, $dbname);
        mysqli_select_db($conn, 'db_stillocarros');
        $sql = "INSERT INTO tb_veiculos(local, num_documento, telefone, email, data_nascimento, foto) VALUES ('$local', '$num_documento', '$telefone', '$email', '$data_nascimento', '$foto')";

        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Seus dados foram salvos !'); window.location = '../index.php';</script>";
        } else {
            echo "Deu erro: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);
        ?>
    </body>
</html>


