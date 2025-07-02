<!DOCTYPE html>

<html lang="pt-br">

  <head>
    <meta charset="UTF-8">
    <title>Editar Animais</title>
    <link rel="stylesheet" href="css/estiloforms.css"/>
  </head>

  <body>
    <?php
      include("../conectarbd.php");
      $recid=filter_input(INPUT_GET, 'editarid');
      $selecionar= mysqli_query($conn, "SELECT * FROM tb_animais WHERE id_animais=$recid");
      $campo= mysqli_fetch_array($selecionar);
    ?>

    <div class="formulario">
      <form method="post" action="EditarAnimais.php">
     
          <h1>Alterar Animais</h1>
     
        <input type="hidden" name="id" value="<?=$campo["id_animais"]?>"> 

        <label>Nome:</label><br> 
        <input type="text" name="nome" placeholder="Nome" value="<?=$campo["nome"]?>"> <br><br>

        <label>Tipo:</label><br>
        <input type="date" name="tipo" placeholder="Tipo" value="<?=$campo["tipo"]?>"> <br><br>

        <label>Porte:</label><br> 
        <input type="time" name="porte" placeholder="Porte" value="<?=$campo["porte"]?>"> <br><br>

        <label>Observações:</label><br> 
        <input type="time" name="observacoes" placeholder="Observações" value="<?=$campo["observacoes"]?>"> <br><br>

        <input type="submit" class="botoes" value="Salvar" >
        <a href="FormConsultarAnimais.php"><input type="button" class="botoes" value="Cancelar"/></a>

      </form>
    </div>

  </body>
</html>
