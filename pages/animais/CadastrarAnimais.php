<?php include_once "../conectarbd.php"; ?>
<html>
    <body>
        <?php
        $nome = $_POST["nome"];
        $tipo = $_POST["tipo"];
        $porte = $_POST["porte"];
        $observacoes = $_POST["observacoes"];
        $conn = mysqli_connect($servidor, $dbusuario, $dbsenha, $dbname);
        mysqli_select_db($conn, 'db_shieldtech');
        $sql = "INSERT INTO tb_reservas(nome, tipo, porte, observacoes) VALUES ('$nome', '$tipo','$porte','$observacoes')";
        if (mysqli_query($conn, $sql)) {
            echo "<script>alert('Seus dados foram salvos !'); window.location = '../index.php';</script>";
        } else {
            echo "Deu erro: " . $sql . "<br>" . mysqli_error($conn);
        }
        mysqli_close($conn);
        ?>
    </body>
</html>

/