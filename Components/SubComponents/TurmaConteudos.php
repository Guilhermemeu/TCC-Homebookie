<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body>

    <!DOCTYPE html>
    <html lang="pt-br">

    <head>
    </head>

    <body>
        <div class="ListaTarefas mt-4 container">
            <?php
            if ($_SESSION["id"] == $administrador) {
                echo "<div class='InputInicio d-flex justify-content-center '>
                    <button class='CriarAtividade AdicionarConteudo px-5 py-2 ButtonTemplate2 px-3'data-bs-toggle='modal' data-bs-target='#exampleModal2'>Criar</button>
                </div>";
            }

            $Selectconteudos = $conn->prepare(query: "SELECT * FROM conteudos where turma_id = " . $idTurma . " ORDER BY idconteudos DESC;");
            $Selectconteudos->execute();

            while ($row = $Selectconteudos->fetch(mode: PDO::FETCH_ASSOC)) {

                $idconteudo = $row['idconteudos'];
                $dataconteudo = $row['data_conteudos'];
                $nomeconteudo = $row['postagens'];

                $Arquivoconteudo = $row['arquivos_anx'];

                echo "<div class='InputInicio d-flex justify-content-between' style='background-color:#fff; margin-top:15px; max-width:93%;'>
                        <h3 style='margin-left:35px;'>$nomeconteudo</h3>
                        <h5 style='opacity:0.5; position:relative;'>$dataconteudo</h5>
                        <a href='BancodeDados/Conteudos/$Arquivoconteudo' style='margin-right:35px;'download=''><i class='fa-solid fa-download'></i></a>
                    </div>";
            }

            ?>

            <form method='POST' enctype='multipart/form-data' action="">
                <div class='modal fade' id='exampleModal2' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
                    <div class='modal-dialog modal-dialog-centered'>
                        <div class='modal-content glass-card'>
                            <div class='modal-header'>
                                <h1 class='modal-title fs-5' id='exampleModalLabel'>Adicione um Conteudo</h1>
                                <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                            </div>
                            <div class='modal-body'>

                                <label for='nomeC' class='col-form-label'>Nome:</label>
                                <input required placeholder='Adicione um nome do conteudo' type='text' class='form-control' name='nomeC' id='nomeC'>


                                <label for='Arquivo' class='ButtonTemplate2 mt-2 col-form-label'> Anexo <i class="fa-solid fa-link"></i></label>
                                <input required class='' placeholder='Adicione um arquivo' type='file' id='Arquivo' class='my-3' accept='.txt , .rar , .zip' name='Arquivo' onchange="Arquivo(this)"><br>

                            </div>
                            <div class='modal-footer'>
                                <button type='submit' value='CriarAtividade' id='CriarAtividade' name='CriarAtividade'class='ButtonTemplate1'>Adicionar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
            <?php
            if (isset($_REQUEST['CriarAtividade'])) {

                $nomeC = $_POST['nomeC'];

                $extensao = strtolower(string: substr(string: $_FILES['Arquivo']['name'], offset: -4));
                $novo_nome = md5(string: time()) . $extensao;
                $diretorioC = "BancodeDados/Conteudos/";

                move_uploaded_file(from: $_FILES['Arquivo']['tmp_name'], to: $diretorioC . $novo_nome);


                try {

                    $Criarconteudo = $conn->prepare(query: "INSERT INTO conteudos(idconteudos,turma_id,data_conteudos,Postagens,arquivos_anx) values(null,$idTurma,CURDATE(),:nome,:arquivo)");

                    $Criarconteudo->bindValue(param: ":nome", value: $nomeC);
                    $Criarconteudo->bindValue(param: ":arquivo", value: $novo_nome);

                    $Criarconteudo->execute();

                    time_sleep_until(time() + 1);

                    echo "<script language=javascript>
                    location.href='Redirect.php?page=Turma&turma=$idTurma'
                    </script>";
                } catch (PDOException $e) {
                    echo $e->getMessage();
                }
            }
            ?>






        </div>
    </body>

    </html>

</body>

</html>