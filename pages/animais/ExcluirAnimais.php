<?php

include("../conectarbd.php");
$recid= filter_input(INPUT_GET, 'animais');

  if(mysqli_query($conn, "DELETE FROM tb_animais WHERE id_animais=$recid")) {
    echo "<script>alert('Dados excluidos com sucesso!'); window.location = 'FormConsultarAnimais.php';</script>";
  }else {
    echo "Não foi possível excluir os dados no Banco de Dados" . $recid . "<br>" . mysqli_error($conn);
  }
  mysqli_close($conn);

?>