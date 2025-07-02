<?php

include("../conectarbd.php");
$recid= filter_input(INPUT_POST, 'id');
$recnome= filter_input(INPUT_POST, 'nome');
$rectipo= filter_input(INPUT_POST, 'tipo');
$recporte= filter_input(INPUT_POST, 'porte');
$recobservacao= filter_input(INPUT_POST, 'observacao');

  if(mysqli_query($conn, "UPDATE tb_animais SET nome='$recnome', tipo='$rectipo', porte='$recporte', observacao='$recobservacao' WHERE id_animais=$recid")) {
    echo "<script>alert('Dados alterado com sucesso!'); window.location = 'FormConsultarAnimais.php';</script>";
  }else {
    echo "Não foi possível alterar os dados no Banco de Dados" . $recid . "<br>" . mysqli_error($conn);
  }
  mysqli_close($conn);

?>