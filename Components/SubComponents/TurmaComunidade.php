<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body>

    <div class=" mt-4 container d-flex justify-content-center align-items-center flex-column AreaChat">
        <form method="POST" class="FormChat">
            <input class="InputChat" placeholder="Envie uma Mensagem..." type="text" id="ChatMensagem"
                name="ChatMensagem" autocomplete="off" required maxlength="100">
        </form>
        <div class="ChatMensagens mb-5 d-flex flex-column">

            <?php

            $SelectChat = $conn->prepare(query: "SELECT * FROM Mensagens where to_chat = :idturma ORDER BY id_mensagem DESC;");
            $SelectChat->bindValue(":idturma", $idTurma);
            $SelectChat->execute();

            while ($row = $SelectChat->fetch(mode: PDO::FETCH_ASSOC)) {

                $mensagem = $row["mensagem"];
                $usuario = $row["from_usuario"];

                $sqlSelect = $conn->prepare(query: "SELECT nome, fotop from usuario where idusuario = :usuario");
                $sqlSelect->bindValue(":usuario", $usuario);
                $sqlSelect->execute();
                $rowuser = $sqlSelect->fetch(mode: PDO::FETCH_ASSOC);

                $nome = $rowuser['nome'];
                $fotop = $rowuser['fotop'];

                if ($usuario == $administrador) {
                    $nome = "<i class='fa-solid fa-crown MoreTurma me-1'></i>$nome";
                }

                echo "
                <div class='Mensagem d-flex flex-row mx-2 mt-3 ps-2 pb-2 p-1 align-items-center'>
                <img class='FotoPerfilChat' src='BancodeDados/FotosdePerfil/$fotop'>
                <h5 class='TextoMensagem mt-2 ms-md-3 ms-1'><strong><a style='color:black;' href='index.php?page=Perfil&userid=$usuario&wasfrom=$idTurma'>$nome#$usuario</a></strong>:$mensagem</h5>
                </div>
                ";
            }
            ?>

        </div>

    </div>

    <?php


    if (isset($_REQUEST["ChatMensagem"])) {
        $mensagem = $_POST["ChatMensagem"];
        $mensagemfix = preg_replace(pattern: '/[^\p{L}\p{N} ]/u', replacement: '', subject: $mensagem);
        if (strlen($mensagemfix) > 100) {
            $mensagemfix = "EU SOU UM IDIOTA QUE MEXE NO SITE DOS OUTROS";
        }
        $iduser = $_SESSION["id"];
        try {
            $sendMessage = $conn->prepare(query: "INSERT INTO mensagens(id_mensagem,mensagem,data_mensagem,from_usuario,to_chat) values(null,:mensagem,CURDATE(),:iduser,$idTurma)");

            $sendMessage->bindValue(param: ":mensagem", value: $mensagemfix);
            $sendMessage->bindValue(param: ":iduser", value: $iduser);


            $sendMessage->execute();

            echo "<script language=javascript>
                    location.href='Redirect.php?page=Comunidade&turma=$idTurma'
                    </script>";
        } catch (Exception $e) {
            echo "" . $e->getMessage() . "";
        }
    }


    ?>
</body>

</html>