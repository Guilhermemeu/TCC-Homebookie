<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body>
    <div class="ListaTarefas mt-4 container">
        <?php
        if ($_SESSION["id"] == $administrador) {
            echo "
                <div class='InputInicio'>
                <form method='POST' class='FormAdm'>
                <input name='MsgTarefa' autocomplete='off' id='MsgTarefa' class='InputAtividade' style='background-color: #c4c4c4;' type='Text' placeholder='Envie uma Mensagem...'>
                </form>
                <button class='mb-2 CriarAtividade ButtonTemplate2 px-3'data-bs-toggle='modal' data-bs-target='#exampleModal2'>Criar Atividade</button>
                </div>";

            if (isset($_REQUEST['MsgTarefa'])) {
                $nomeT = $_POST['MsgTarefa'];
                try {
                    $EnviaMsgTarefa = $conn->prepare(query: "INSERT INTO tarefas(idtarefas,turma_id,data_tarefa,nome_tarefa,Tarefa) values(null,$idTurma,CURDATE(),:nome,0)");

                    $EnviaMsgTarefa->bindValue(param: ":nome", value: $nomeT);

                    $EnviaMsgTarefa->execute();

                    echo "<script language=javascript>
                    location.href='Redirect.php?page=Turma&turma=$idTurma'
                    </script>";
                } catch (PDOException $e) {
                    echo "" . $e->getMessage() . "";
                }
            }
        }

        $SelectAtividades = $conn->prepare(query: "SELECT * FROM tarefas where turma_id = :idturma ORDER BY idTarefas DESC;");
        $SelectAtividades->bindValue(":idturma", $idTurma);
        $SelectAtividades->execute();

        while ($row = $SelectAtividades->fetch(mode: PDO::FETCH_ASSOC)) {

            $idTarefas = $row['idtarefas'];
            $dataTarefa = $row['data_tarefa'];
            $nomeTarefa = $row['nome_tarefa'];
            $tarefa = $row['Tarefa'];


            if (isset($row['Arquivo'])) {
                $ArquivoTarefa = $row['Arquivo'];
            }

            if ($_SESSION['id'] == $administrador) {
                if (isset($ArquivoTarefa) && $ArquivoTarefa != '' or $tarefa == "1") {
                    echo "<div class='InputInicio d-flex justify-content-between flex-row' style='background-color:#fff; margin-top:15px; max-width:93%;'>
                                <h3 style='margin-left:35px;'>$nomeTarefa</h3>
                                <h5 style='opacity:0.5; position:relative;'>$dataTarefa</h5>
                                <a href='index.php?page=Turma&turma=$idTurma&Tab=Inicio&edit=$idTarefas'>
                                    <i class='fa-solid fa-pen-to-square' style='margin-right:35px; font-size:1.2rem; color:#0000FF;'></i>
                                </a>
                            </div>";
                } else {
                    echo "<div class='InputInicio d-flex justify-content-between' style='background-color:#fff; margin-top:15px; max-width:93%;'>
                                <h3 style='margin-left:35px; '>$nomeTarefa</h3>
                                <h5 style='opacity:0.5; margin-right:35px;'>$dataTarefa</h5>
                            </div>";
                }
            } else {
                if (isset($ArquivoTarefa) && $ArquivoTarefa != '' && $ArquivoTarefa != null) {
                    echo "
                            <div class='InputInicio d-flex justify-content-between flex-row' style='background-color:#fff; margin-top:15px; max-width:93%;'>
                                <a href='index.php?page=Agenda&Tarefa=$idTarefas'><h3 style='margin-left:35px;'>$nomeTarefa</h3></a>
                                    <h5 style='opacity:0.5; position:relative;'>$dataTarefa</h5>
                                <a href='BancodeDados/Tarefas/$ArquivoTarefa' style='margin-right:35px;'download='' ><i class='fa-solid fa-download'></i></a>
                            </div>";
                } else {
                    if ($tarefa == 1) {
                        echo "<div class='InputInicio d-flex justify-content-between' style='background-color:#fff; margin-top:15px; max-width:93%;'>
                                <a href='index.php?page=Agenda&Tarefa=$idTarefas'><h3 style='margin-left:35px;'>$nomeTarefa</h3></a>
                                <h5 style='opacity:0.5; margin-right:35px;'>$dataTarefa</h5>
                            </div>";
                    } else {
                        echo "<div class='InputInicio d-flex justify-content-between' style='background-color:#fff; margin-top:15px; max-width:93%;'>
                                <h3 style='margin-left:35px;'>$nomeTarefa</h3>
                                <h5 style='opacity:0.5; margin-right:35px;'>$dataTarefa</h5>
                            </div>";
                    }

                }
            }
        }






        if ($_SESSION['id'] == $administrador) {
            echo "
        <form method='POST' enctype='multipart/form-data' action=''>
            <div class='modal fade' id='exampleModal2' tabindex='-1' aria-labelledby='exampleModalLabel'
                aria-hidden='true'>
                <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content glass-card'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='exampleModalLabel'>Adicione uma tarefa</h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>

                            <label for='nomeA' class='col-form-label'>Nome:</label>
                            <input autocomplete='off' required placeholder='Adicione um nome da Tarefa' type='text'
                                class='form-control' name='nomeA' id='nomeA'>


                            <label for='Arquivo' class='ButtonTemplate2 mt-2 col-form-label'>Anexo <i
                                    class='fa-solid fa-link'></i></label>
                            <input class='' placeholder='Adicione um arquivo' type='file' id='Arquivo' class='my-3'
                                accept='.txt , .rar , .zip' name='Arquivo'><br>


                            <label for='descricaoA' class='col-form-label'>Descrição:</label>
                            <textarea autocomplete='off' placeholder='(opcional)' class='form-control' name='descricaoA'
                                id='descricaoA'></textarea>

                        </div>
                        <div class='modal-footer'>
                            <button type='submit' value='CriarAtividade' id='CriarAtividade' name='CriarAtividade'
                                class='ButtonTemplate1'>Adicionar</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>";
        }

        if (isset($_REQUEST['CriarAtividade'])) {

            $nomeA = $_POST['nomeA'];

            $descricaoA = $_POST['descricaoA'];

            $extensao = strtolower(string: substr(string: $_FILES['novo_arquivo']['name'], offset: -4));
            $novo_nome = md5(string: time()) . $extensao;
            $diretorioA = "BancodeDados/Tarefas/";
            if (isset($extensao) or $extensao == "") {
                move_uploaded_file(from: $_FILES['Arquivo']['tmp_name'], to: $diretorioA . $novo_nome);
                $novo_nome = null;
            }



            try {

                $criarTarefaAnexo = $conn->prepare(query: "INSERT INTO tarefas(idtarefas,turma_id,data_tarefa,nome_tarefa,Tarefa,descricao_tarefa,Arquivo) values(null,$idTurma,CURDATE(),:nome,1,:descricao_tarefa,:arquivo)");

                $criarTarefaAnexo->bindValue(param: ":nome", value: $nomeA);
                $criarTarefaAnexo->bindValue(param: ":descricao_tarefa", value: $descricaoA);

                if ($novo_nome != "") {
                    $criarTarefaAnexo->bindValue(param: ":arquivo", value: $novo_nome);
                } else {
                    $criarTarefaAnexo->bindValue(param: ":arquivo", value: "");
                }

                if ($criarTarefaAnexo->execute()) {

                    time_sleep_until(time() + 1);

                    echo "<script language=javascript>
                    location.href='Redirect.php?page=Turma&turma=$idTurma'
                    </script>";
                }

            } catch (PDOException $e) {
                echo $e->getMessage();
            }
        }

        if (isset(($_GET['edit']))) {

            $editartarefa = $conn->prepare("SELECT * FROM tarefas where idTarefas = :idtarefa");
            $editartarefa->bindValue(":idtarefa", $_GET['edit']);
            $editartarefa->execute();

            $rowT = $editartarefa->fetch(PDO::FETCH_ASSOC);


            $nome_Tarefa = $rowT['nome_tarefa'];
            $descricao_Tarefa = $rowT['descricao_tarefa'];
            $arquivo_editar = $rowT['Arquivo'];
            echo "
            <form method='POST' enctype='multipart/form-data'>
            <div class='modal fade' id='myModal' tabindex='-1' aria-labelledby='exampleModalLabel'
                aria-hidden='true'>
                <div class='modal-dialog modal-dialog-centered'>
                    <div class='modal-content glass-card'>
                        <div class='modal-header'>
                            <h1 class='modal-title fs-5' id='exampleModalLabel'>Editar Tarefa: <i>$nome_Tarefa</i></h1>
                            <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                        </div>
                        <div class='modal-body'>

                            <label for='nomeA' class='col-form-label'>Nome:</label>
                            <input autocomplete='off' required placeholder='Adicione um nome da Tarefa' type='text'
                                class='form-control' name='nomeE' id='nomeE' value='$nome_Tarefa'>

                            <label class='col-form-label'>Arquivo:</label><br>
                            <label for='novo_arquivo' class='ButtonTemplate2 mt-2 col-form-label'>Anexo 
                            <i class='fa-solid fa-link'></i></label>

                            <input class='' placeholder='Adicione um arquivo' type='file' id='novo_arquivo' class='my-3'
                                accept='.txt , .rar , .zip' name='novo_arquivo' value='$arquivo_editar'><br>

                            <label for='descricaoA' class='col-form-label'>Descrição:</label>
                            <textarea autocomplete='off' placeholder='(opcional)' class='form-control' name='descricaoE'
                            id='descricaoE'>$descricao_Tarefa</textarea>

                        </div>
                        <div class='modal-footer'>
                            <button type='submit' value='AlterarAtividade' id='AlterarAtividade' name='AlterarAtividade'
                                class='ButtonTemplate1'>Alterar</button>
                        </div>
                    </div>
                </div>
            </div>
            </form>

            <script language=javascript>
            document.addEventListener('DOMContentLoaded', function() {
            var myModalElement = document.getElementById('myModal');
            var myModal = new bootstrap.Modal(myModalElement);
            myModal.show();
    });
            </script>

            ";

        }
        if (isset($_REQUEST['AlterarAtividade'])) {

            if (isset($_FILES['novo_arquivo']) && $_FILES['novo_arquivo']['error'] === UPLOAD_ERR_OK) {
                $extensao = strtolower(substr($_FILES['novo_arquivo']['name'], -4));
                $extensoes_permitidas = ['.txt', '.rar', '.zip'];

                if (in_array($extensao, $extensoes_permitidas)) {
                    $novo_arquivo = md5(time()) . $extensao;
                    $diretorioA = "BancodeDados/Tarefas/";
                    if (move_uploaded_file($_FILES['novo_arquivo']['tmp_name'], $diretorioA . $novo_arquivo)) {
                    } else {
                        echo "<script>alert('Erro ao salvar o arquivo!');</script>";
                        $novo_arquivo = $arquivo_editar;
                    }
                } else {
                    echo "<script>
                    alert('Extensão de arquivo não permitida');
                    </script>";
                    $novo_arquivo = $arquivo_editar;
                }
            }
            $AlterarAtividade = $conn->prepare("UPDATE tarefas set nome_tarefa = :nome_tarefa, Arquivo = :arquivo, descricao_tarefa = :descricao_tarefa WHERE idtarefas = :idtarefa");
            $AlterarAtividade->bindValue(":nome_tarefa", $_POST['nomeE']);
            $AlterarAtividade->bindValue(":arquivo", $novo_arquivo);
            $AlterarAtividade->bindValue(":descricao_tarefa", $_POST['descricaoE']);
            $AlterarAtividade->bindValue(":idtarefa", $_GET['edit']);

            if ($AlterarAtividade->execute()) {
                echo "<script language=javascript>
            location.href='Redirect.php?page=Turma&turma=$idTurma';
        </script>";
            } else {
                echo "<script>alert('Erro ao atualizar no banco de dados!');</script>";
            }
        }
        ?>






    </div>
</body>

</html>