<!DOCTYPE html>

<html lang="pt-br">

  <head>
    <meta charset="UTF-8">
    <title>Editar Visitantes</title>
    <link rel="stylesheet" href="css/estiloforms.css"/>
  </head>

  <body>
    <?php
      include("../conectarbd.php");
      $recid=filter_input(INPUT_GET, 'editarid');
      $selecionar= mysqli_query($conn, "SELECT * FROM tb_visitantes WHERE id_visitantes=$recid");
      $campo= mysqli_fetch_array($selecionar);
    ?>

    <div class="formulario">
      <form method="post" action="EditarVisitante.php">
     
          <h1>Alterar Visitante</h1>
     
<!esta linha cria um campo oculto para passar o id_cidade, pois senão ao clicar em Salvar o código não saberá onde salvar.--!>
        <input type="hidden" name="id" value="<?=$campo["id_visitantes"]?>"> 

        <label>Nome do Visitante</label><br> 
        <input type="text" name="nome_visitante" placeholder="nome_visitante" value="<?=$campo["nome_visitante"]?>"> <br><br>
        
        <label>Numero de Documento</label><br>
        <input type="text" name="num_documento" placeholder="num_documento" value="<?=$campo["num_documento"]?>"> <br><br>
        
        <label>Telefone</label><br>
        <input type="text" name="telefone" placeholder="telefone" value="<?=$campo["telefone"]?>"> <br><br>

        <label>Email</label><br>
        <input type="text" name="email" placeholder="email" value="<?=$campo["email"]?>"> <br><br>
        
        <label>Data de Nascimento</label><br>
        <input type="text" name="data_nascimento" placeholder="data_nascimento" value="<?=$campo["data_nascimento"]?>"> <br><br>

        <label>Foto</label><br>
        <input type="text" name="foto" placeholder="foto" value="<?=$campo["foto"]?>"> <br><br>
        

        
        <input type="submit" class="botoes" value="Salvar" >
        <a href="FormConsultarVisitante.php"><input type="button" class="botoes" value="Cancelar"/></a>

      </form>
    </div>

  </body>
</html>
