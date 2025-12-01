<?php

if ($_SESSION['id'] == 0) {
    echo "<script language='javascript'>
    location.href='Entrar.php'
    </script>";
} else {
    $selectusuario = $conn->prepare("SELECT * FROM usuario where idusuario = :usuario");
    $selectusuario->bindValue(":usuario", $_SESSION['id']);
    $selectusuario->execute();
    $rowUsuario = $selectusuario->fetch(PDO::FETCH_ASSOC);

    $participa = $rowUsuario['participa'] ? explode(" ", $rowUsuario['participa']) : [];


    if (empty($participa)) {
        $selectTarefa = $conn->prepare("SELECT * FROM tarefas where turma_id = 0 and Tarefa = 1 ORDER BY idTarefas DESC");
    } else {
        $placeholders = implode(",", array_fill(0, count($participa), "?"));
        $selectTarefa = $conn->prepare("SELECT * FROM tarefas where turma_id IN ($placeholders) and Tarefa = 1 ORDER BY idTarefas DESC");

        foreach ($participa as $index => $turma_id) {
            $selectTarefa->bindValue($index + 1, $turma_id, PDO::PARAM_INT);
        }
    }
    $selectTarefa->execute();
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body id="fundop">

    <div class="container-fluid PCT">
        <div class="BarraTarefa d-flex ps-md-5 ps-0">

            <div class="ListaTarefasAgenda <?php if (!isset($_GET['Tarefa'])) {
                                                echo ' Starefa ';
                                            } else {
                                                echo ' CTarefa ';
                                            } ?>  flex-column align-items-center">

                <div class="d-flex flex-column">

                    <div class="DashAgenda mt-1 fadeInUp-animation"></div>
                    <i>
                        <h1 class="SuasTarefas">Suas Tarefas:</h1>
                    </i>
                    <div class="DashAgenda fadeInUp-animation"></div>
                </div>
                <div class="SelectAllTurmas container d-flex align-items-center flex-column">

                    <?php
                    while ($rowTarefa = $selectTarefa->fetch(PDO::FETCH_ASSOC)) {
                        $id_tarefa = $rowTarefa["idtarefas"];
                        $nome_tarefa = $rowTarefa["nome_tarefa"];
                        $descricao_tarefa = $rowTarefa["descricao_tarefa"];
                        $Arquivo_tarefa = $rowTarefa["Arquivo"];
                        $data_tarefa = $rowTarefa["data_tarefa"];
                        $turma_id = $rowTarefa["turma_id"];

                        $selectturma = $conn->prepare("SELECT * from turma where idturma = :turma_id");
                        $selectturma->bindValue(":turma_id", $turma_id, PDO::PARAM_INT);
                        $selectturma->execute();

                        $rowturma = $selectturma->fetch(PDO::FETCH_ASSOC);
                        $nome_turma = $rowturma["nome"];

                        echo "<a href='index.php?page=Agenda&Tarefa=$id_tarefa' class='TarefaAgenda py-2 mt-4 d-flex align-items-center'>
                            <i class='fa-solid fa-file-invoice ms-2 '></i>
                            <div class='d-flex flex-column ms-4'>
                                <h5>$nome_tarefa</h5>
                                <span class='dct'>adicionou uma tarefa</span>
                                <span>Turma:$nome_turma</span>
                            </div>
                        </a>";
                    }
                    ?>
                </div>
            </div>
            <!-- | Tarefas | -->
            <div class="Tarefa d-flex flex-column">

                <?php
                if (isset($_GET['Tarefa'])) {

                    $tarefa = $_GET['Tarefa'];

                    $feztarefa = $conn->prepare("SELECT * FROM recebidos where id_tarefa = :tarefa and id_sender = :idusuario ");
                    $feztarefa->bindValue(":tarefa", $tarefa);
                    $feztarefa->bindValue(":idusuario", $_SESSION['id']);
                    $feztarefa->execute();

                    $achou = $feztarefa->rowCount();

                    if ($achou > 0) {
                        $rowrecebido = $feztarefa->fetch(PDO::FETCH_ASSOC);
                        $id_recebido = $rowrecebido['id_recebido'];
                    }
                    $idTarefa = filter_var(value: $_GET['Tarefa'], filter: FILTER_VALIDATE_INT);
                    if ($idTarefa === false || $idTarefa <= 0) {
                        die("ID de tarefa inválido");
                    }

                    $tarefaMore = $conn->prepare(query: "SELECT * FROM tarefas WHERE idtarefas = :gettarefa");
                    $tarefaMore->bindValue(param: ":gettarefa", value: $idTarefa, type: PDO::PARAM_INT);
                    $tarefaMore->execute();

                    $tarefarow = $tarefaMore->fetch(PDO::FETCH_ASSOC);

                    $nomeMore = $tarefarow['nome_tarefa'];
                    $descMore = $tarefarow['descricao_tarefa'];

                    $getAdm = $conn->prepare("SELECT administrador from turma where idturma = :idturma");
                    $getAdm->bindValue(":idturma", $tarefarow['turma_id']);
                    $getAdm->execute();

                    $Adm = $getAdm->fetch(PDO::FETCH_ASSOC);

                    if ($achou === 0 && $_SESSION['id'] != $Adm['administrador']) {
                        echo "

                         <div class='d-flex align-items-center flex-column RotuloTarefa'>
                            <h5 class=''>Status: Não Entregue </h5>
                            <h1 class='mt-2'>$nomeMore</h1>
                            <h4 class='mt-3 desctarefa'>$descMore</h4>
                        </div>

                        <form method='POST' enctype='multipart/form-data' class='FormEntrega d-flex align-items-center flex-column p-3'>
                            <input type='text' style='border:0.01rem solid #000;' class='EnviaTarefaInput' id='mensagem' name='mensagem' placeholder='Ensira os detalhes da entrega . . .'>

                            <label for='entrega' style='padding:10px 40px !important;' class='ButtonTemplate1 mt-2 col-form-label'> Anexo <i class='fa-solid fa-link'></i></label>
                            <input required placeholder='Adicione um arquivo' type='file' id='entrega' name='entrega' class='my-3' accept='.rar,.zip'><br>

                            <button class='ButtonTemplate2 mt-4' style='min-width:150px !important;' name='entregar' type='submit'>Enviar</button>
                        </form>

                        ";
                        if (isset($_POST['entregar'])) {
                            $mensagemE = $_POST['mensagem'];

                            if ($_FILES['entrega']['error'] !== UPLOAD_ERR_OK) {
                                die("Erro no upload do arquivo");
                            }

                            $extensao = strtolower(string: substr(string: $_FILES['entrega']['name'], offset: -4));
                            $entrega = md5(string: time()) . $extensao;
                            $diretorioR = "BancodeDados/Recebidos/";

                            try {
                                $selectTarefaTurma = $conn->prepare("SELECT turma_id FROM tarefas where idtarefas = :idtarefas");
                                $selectTarefaTurma->bindValue(":idtarefas", $idTarefa, PDO::PARAM_INT);
                                $selectTarefaTurma->execute();
                                $TurmaTarefaRow = $selectTarefaTurma->fetch(PDO::FETCH_ASSOC);

                                $entregarTarefa = $conn->prepare("INSERT INTO recebidos(id_recebido,id_sender,id_turma,id_tarefa,mensagem,entrega) VALUES(null,:sender,:idturma,:idtarefa,:mensagem,:entrega)");

                                $entregarTarefa->bindValue(":sender", $_SESSION['id']);
                                $entregarTarefa->bindValue(":idturma", $TurmaTarefaRow['turma_id']);
                                $entregarTarefa->bindValue(":idtarefa", $_GET['Tarefa']);
                                $entregarTarefa->bindValue(":mensagem", $mensagemE);
                                $entregarTarefa->bindValue(":entrega", $entrega);

                                if ($entregarTarefa->execute()) {

                                    move_uploaded_file($_FILES['entrega']['tmp_name'], $diretorioR . $entrega);
                                    echo "<script language=javascript>
                                    location.href='Redirect.php?entrega=1'
                                </script>";
                                }
                            } catch (PDOException $e) {
                                echo "Erro: " . $e->getMessage();
                            }
                        }
                    } else {
                        echo "
                    <div class='d-flex align-items-center flex-column RotuloTarefa' style='margin-bottom:0px;'>
                        <h5 class=''>Status: Entregue </h5>
                        <h1 class='mt-2'>$nomeMore</h1>
                        ";
                        if ($_SESSION['id'] != $Adm['administrador']) {
                            echo "<a href='Redirect.php?what=$id_recebido&remove=1&tarefa=$tarefa'><button class='ButtonTemplate2' style='position:absolute; margin-left:20%; padding:.7rem;'>Remover Entrega</button></a>";
                        }
                        echo "<h4 class='mt-3 desctarefa' style='margin-bottom:20%;'>$descMore</h4>
                        </div>

                    <div style='width:100% !important;height:50%;' class='ChatMensagens d-flex flex-column'>";

                        if ($_SESSION['id'] == $Adm['administrador']) {
                            $selectMensagemTarefa = $conn->prepare("SELECT * FROM mensagemtarefas WHERE to_tarefa = :to_tarefa AND (from_usuario = :user OR (from_usuario = :self and and_user = :and_user))ORDER BY id_mensagem DESC");
                            $selectMensagemTarefa->bindValue(":user", $_GET['user']);
                            $selectMensagemTarefa->bindValue(":self", $Adm['administrador']);
                            $selectMensagemTarefa->bindValue(":and_user", $_GET['user']);
                        } else {
                            $selectMensagemTarefa = $conn->prepare("SELECT * FROM mensagemtarefas WHERE to_tarefa = :to_tarefa AND (from_usuario = :user OR (from_usuario = :adm and and_user = :and_user)) ORDER BY id_mensagem DESC");
                            $selectMensagemTarefa->bindValue(":user", $_SESSION['id']);
                            $selectMensagemTarefa->bindValue(":adm", $Adm['administrador']);
                            $selectMensagemTarefa->bindValue(":and_user", $_SESSION['id']);
                        }
                        $selectMensagemTarefa->bindValue(":to_tarefa", $tarefa);
                        $selectMensagemTarefa->execute();

                        while ($RowMT = $selectMensagemTarefa->fetch(PDO::FETCH_ASSOC)) {
                            $mensagem = $RowMT["mensagem"];
                            $usuario = $RowMT["from_usuario"];

                            $sqlSelect = $conn->prepare(query: "SELECT nome, fotop from usuario where idusuario = :usuario");
                            $sqlSelect->bindValue(":usuario", $usuario);
                            $sqlSelect->execute();
                            $rowuser = $sqlSelect->fetch(mode: PDO::FETCH_ASSOC);

                            $nome = $rowuser['nome'];
                            $fotop = $rowuser['fotop'];

                            echo "
                            <div class='Mensagem d-flex flex-row mx-2 mt-3 ps-2 pb-2 p-1 align-items-center'>
                            <img class='FotoPerfilChat' src='BancodeDados/FotosdePerfil/$fotop'>
                            <h5 class='TextoMensagem mt-2 ms-md-3 ms-1'><strong><a style='color:black;' href='index.php ?page=Perfil&userid=$usuario'>$nome#$usuario</a></strong>:$mensagem</h5>
                            </div>
                            ";
                        }
                        echo "
                    </div>
                    <form method='POST' class='FormChat'>
                        <input class='InputChat' placeholder='Envie uma Mensagem...' type='text' id='ChatMensagem'
                        name='ChatMensagem' autocomplete='off' required maxlength='100' style='width:100% !important;margin-bottom:0% !important'>
                    </form>";
                    }

                    if (isset($_REQUEST["ChatMensagem"])) {
                        $mensagem = $_POST["ChatMensagem"];
                        $mensagemfix = preg_replace(pattern: '/[^\p{L}\p{N} ]/u', replacement: '', subject: $mensagem);
                        if (strlen($mensagemfix) > 100) {
                            $mensagemfix = "EU SOU UM IDIOTA QUE MEXE NO SITE DOS OUTROS";
                        }
                        $iduser = $_SESSION["id"];
                        try {
                            $sendMessage = $conn->prepare(query: "INSERT INTO mensagemtarefas(id_mensagem,mensagem,data_mensagem,from_usuario,to_tarefa,and_user) values(null,:mensagem,CURDATE(),:iduser,:idtarefas,:and_user)");

                            $sendMessage->bindValue(param: ":mensagem", value: $mensagemfix);
                            $sendMessage->bindValue(param: ":iduser", value: $_SESSION['id']);
                            $sendMessage->bindValue(param: ":idtarefas", value: $tarefa);


                            if ($_SESSION['id'] == $Adm['administrador'] && isset($_GET['user'])) {
                                $sendMessage->bindValue(param: ":and_user", value: $_GET['user']);

                                $sendMessage->execute();

                                $user = $_GET['user'];
                                echo "<script language=javascript>
                                location.href='Redirect.php?page=Agenda&tarefa=$tarefa&user=$user'
                                </script>";
                            } else {
                                $sendMessage->bindValue(param: ":and_user", value: 0);

                                $sendMessage->execute();

                                echo "<script language=javascript>
                                location.href='Redirect.php?page=Agenda&tarefa=$tarefa'
                                </script>";
                            }
                        } catch (Exception $e) {
                            echo "" . $e->getMessage() . "";
                        }
                    }
                }
                ?>
            </div>
        </div>
    </div>
</body>

</html>