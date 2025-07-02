<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Consultar Animais</title>
        <link type="text/css" rel="stylesheet" href="estilo.css">
    
    </head>

     <body>
                <h1>Consultar Animais</h1>
                <table
                   width="100%"
                   border="1" 
                   bordercolor="black"
                   cellspacing="2" 	
                   cellpadding="5"
                   >
                    <tr>
                        <td align="center"> <strong>ID</strong></td>	
                        <td align="center"> <strong>nome</strong></td>		
                        <td align="center"> <strong>tipo</strong></td>
                        <td align="center"> <strong>porte</strong></td>
                        <td align="center"> <strong>observacao</strong></td>
                        <td width="10"> <strong>Editar</strong></td>
                        <td width="10"> <strong>Deletar</strong></td>
                    </tr>

                    <?php
                        include("../conectarbd.php");
                        $selecionar= mysqli_query($conn, "SELECT * FROM tb_animais");
                        while ($campo= mysqli_fetch_array($selecionar)){?>
                            <tr>
                                <td align="center"><?=$campo["id_animais"]?></td>
                                <td align="center"><?=$campo["nome"]?></td>
                                <td align="center"><?=$campo["tipo"]?></td>
                                <td align="center"><?=$campo["porte"]?></td>
                                <td align="center"><?=$campo["observacoes"]?></td>
                                <td align="center"><a href="FormEditarAnimais.php?editarid=<?php echo $campo ['id_animais'];?>">Editar</a></td>
                                <td align="center"><i><a href="ExcluirAnimais.php?p=excluir&animais=<?php echo $campo['id_animais'];?>">Excluir</i></a></td>
                            </tr>
                    <?php }?>
                </table><br>
                    <a href="../index.php"><input type="button" class="botoes" value="Cancelar"/></a>
    </body>
</html>