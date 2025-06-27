<?php

include("../conectarbd.php");
$recid= filter_input(INPUT_GET, 'visitantes');

  if(mysqli_query($conn, "DELETE FROM tb_visitantes WHERE id_visitantes=$recid")) {
    echo "<script>alert('Dados excluidos com sucesso!'); window.location = 'FormConsultarVisitante.php';</script>";
  }else {
    echo "Não foi possível excluir os dados no Banco de Dados" . $recid . "<br>" . mysqli_error($conn);
  }
  mysqli_close($conn);

?>