<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Consultar Visitante</title>
        <link type="text/css" rel="stylesheet" href="estilo.css">
    
    </head>

     <body>
                <h1>Consultar Visitantes Cadastrados</h1>
                <table
                   width="100%"
                   border="1" 
                   bordercolor="black"
                   cellspacing="2" 	
                   cellpadding="5"
                   >
                    <tr>
                        <td align="center"> <strong>ID</strong></td>	
                        <td align="center"> <strong>Nome do Visitante</strong></td>		
                        <td align="center"> <strong>Numero do Documento</strong></td>
                        <td align="center"> <strong>Telefone</strong></td>
                        <td align="center"> <strong>Email</strong></td>
                        <td align="center"> <strong>Data de Nascimento</strong></td>
                        <td align="center"> <strong>Foto</strong></td>

                        <td width="10"> <strong>Editar</strong></td>
                        <td width="10"> <strong>Deletar</strong></td>
                    </tr>

                    <?php
                        include("../conectarbd.php");
                        $selecionar= mysqli_query($conn, "SELECT * FROM tb_visitantes");
                        while ($campo= mysqli_fetch_array($selecionar)){?>
                            <tr>
                                <td align="center"><?=$campo["id_visitantes"]?></td>
                                <td align="center"><?=$campo["nome_visitante"]?></td>
                                <td align="center"><?=$campo["num_documento"]?></td>
                                <td align="center"><?=$campo["telefone"]?></td>
                                <td align="center"><?=$campo["email"]?></td>
                                <td align="center"><?=$campo["data_nascimento"]?></td>
                                <td align="center"><?=$campo["foto"]?></td>


                                <td align="center"><a href="FormEditarVisitante.php?editarid=<?php echo $campo ['id_visitantes'];?>">Editar</a></td>
                                <td align="center"><i><a href="ExcluirVisitante.php?p=excluir&Visitantes=<?php echo $campo['id_visitantes'];?>">Excluir</i></a></td>
                            </tr>
                    <?php }?>
                </table><br>
                    <a href="../index.php"><input type="button" class="botoes" value="Cancelar"/></a>
    </body>
</html>
