<?php

include("../conectarbd.php");
$recid= filter_input(INPUT_POST, 'id');
$recnome_visitante= filter_input(INPUT_POST, 'nome_visitante');
$recnum_documento= filter_input(INPUT_POST, 'num_documento');
$rectelefone= filter_input(INPUT_POST, 'telefone');
$recemail= filter_input(INPUT_POST, 'email');
$recdata_nascimento= filter_input(INPUT_POST, 'data_nascimento');
$recfoto= filter_input(INPUT_POST, 'foto');



  if(mysqli_query($conn, "UPDATE tb_visitantes SET nome_visitante='$recnome_visitante', num_documento='$recnum_documento', telefone='$rectelefone', email='$recemail', data_nascimento='$recdata_nascimento', foto='$recfoto' WHERE id_visitantes=$recid")) {
    echo "<script>alert('Dados alterado com sucesso!'); window.location = 'FormConsultarVisitante.php';</script>";
  }else {
    echo "Não foi possível alterar os dados no Banco de Dados" . $recid . "<br>" . mysqli_error($conn);
  }
  mysqli_close($conn);

?>