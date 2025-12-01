<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body id="fundop">
    <?php
    if (isset($_GET["p"])) {
        $p = $_GET["p"];
    }
    ?>
    <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;" viewBox="0 0 1440 320">
        <path fill="#8d65b39d" fill-opacity="1"
            d="M0,128L48,122.7C96,117,192,107,288,122.7C384,139,480,181,576,186.7C672,192,768,160,864,154.7C960,149,1056,171,1152,160C1248,149,1344,107,1392,85.3L1440,64L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
        </path>
    </svg>
    <form method="get" action="index.php">
        <input type="hidden" name="page" value="Inicio" style="font-display: none;">
        <div class="container-fluid PesquisaDiv d-flex justify-content-center py-2">
            <input class="PesquisaBox px-1" type="text" placeholder="Pesquise Por Turmas..." type="submit" name="p" ?
                value="<?php
                if (isset($_GET["p"])) {
                    echo "$p";
                }
                ?>">
            <button type="submit" class="iconeSearch">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>

        </div>
    </form>

    <div class="container-fluid d-flex my-1 justify-content-end py-2" style="position: absolute;">
        <button type="button" class="CT ButtonTemplate1" name="teste" id="teste" data-bs-toggle="modal"
            data-bs-target="#exampleModal"> + Criar turma </button>
    </div>


    <!-- | Modal | -->


    <form method='POST' enctype='multipart/form-data' action="index.php?page=Inicio">
        <div class='modal fade' id='exampleModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content  glass-card'>
                    <div class='modal-header'>
                        <h1 class='modal-title fs-5' id='exampleModalLabel'>Crie Sua Turma!</h1>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>

                        <label for='nome' class='col-form-label'>Nome*:</label>
                        <input autocomplete="off" required placeholder='Insira o nome da turma...' type='text'
                            class='form-control' name='nome' id='nome'>

                        <label for='fotob' class='ButtonTemplate2 file mt-3 col-form-label'>adicionar imagem</label>
                        <input type='file' id='fotob' class='my-3 btn btn-sucess' accept='image/png .jpeg .jpg .webp'
                            name='fotob'><br>

                        <label for='materia' class='col-form-label'>Matéria:</label>
                        <input autocomplete="off" placeholder='(opcional)' class='form-control' name='materia'
                            id='materia'>

                        <label for='descricao' class='col-form-label'>Descrição:</label>
                        <textarea autocomplete="off" placeholder='(opcional)' class='form-control' name='descricao'
                            id='descricao'></textarea>

                    </div>
                    <div class='modal-footer'>
                        <button type='submit' value='cturma' id='cturma' name='cturma'
                            class='ButtonTemplate1'>Criar!</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="SelectAllTurmas">
        <!-- | Criar Turma | -->
        <?php
        if (isset($_REQUEST['cturma'])) {

            $nomeC = $_POST['nome'];
            $materia = $_POST['materia'];
            $descricao = $_POST['descricao'];

            if ($_FILES['fotob']['error'] !== UPLOAD_ERR_OK) {
                $fotob = "Turma.png";
            } else {

                $extensao = strtolower(string: substr(string: $_FILES['fotob']['name'], offset: -4));
                $fotob = md5(string: time()) . $extensao;
                $diretorioR = "BancodeDados/BannerTurma/";
            }
            if ($nomeC != "") {
                try {
                    $cturma = $conn->prepare(query: "INSERT INTO turma(idturma,nome,materia,descricao,administrador,imagem) values(null,:nome,:materia,:descricao," . $_SESSION['id'] . ",:imagem)");

                    $cturma->bindValue(param: ":nome", value: $nomeC);
                    $cturma->bindValue(param: ":materia", value: $materia);
                    $cturma->bindValue(param: ":descricao", value: $descricao);
                    $cturma->bindValue(param: ":imagem", value: $fotob);

                    if ($cturma->execute()) {
                        if ($_FILES['fotob']['name'] != "") {
                            move_uploaded_file($_FILES['fotob']['tmp_name'], $diretorioR . $fotob);
                        }

                        echo "<script language=javascript>
                location.href='index.php?page=Turmas'
                </script>";
                    }
                } catch (PDOException $e) {
                    echo "" . $e->getMessage() . "";
                }
            }
        }
        ?>
        <!-- | Select | -->
        <?php
        try {

            if (isset($_GET['p']) && $_GET['p'] != "") {
                $p = $_GET['p'];

                $sqlSelect = $conn->prepare(query: "SELECT * FROM turma WHERE nome LIKE '%" . $p . "%'");
            } else {
                $sqlSelect = $conn->prepare(query: "SELECT * FROM turma WHERE idturma = 0 ");
            }
            $sqlSelect->execute();

            $padrao = 3;

            while ($row = $sqlSelect->fetch(mode: PDO::FETCH_ASSOC)) {

                $nome = $row['nome'];
                $idturma = $row['idturma'];
                $descricao = $row['descricao'];
                $imagem = $row['imagem'];

                if ($padrao == 3) {
                    echo "<div class='container d-flex justify-content-evenly flex-wrap'>";
                }


                echo "  
                <div class='TurmaSelect bg-success d-flex flex-column'>
                <img src='BancodeDados/BannerTurma/$imagem' style='border:none;' class='TurmaSelectIMG'>
                <div class='TurmaSelectNome container-fluid d-flex justify-content-start align-items-end'>
                <a href='?page=Inicio&join=$idturma' style=''><button class='LinkTurma ms-4'><h1>" . $nome . "</h1></button></a>
                </div>
                </div>
                    ";

                $padrao = $padrao + 1;



                if ($padrao == 6) {
                    echo "</div>";
                    $padrao = 3;
                }
                if ($padrao % 3 != 0) {
                }
                ;
            }
        } catch (PDOException $ERRO) {
            echo $ERRO->getMessage();
        }

        if (!isset($_GET['p']) or $_GET['p'] = "") {
            echo "
        <div class='container mt-5 d-flex justify-content-center'>
        <strong><h1 class='mt-3'style='color:#674a693;'>Procure por turmas que você tem interesse!</h1></strong>
        </div>
        ";
        }
        ?>

    </div>

    <?php

    // <!-- | Juntar a Turma | -->
    
    if (isset($_GET["join"])) {

        $selectTurmajoin = $conn->prepare("SELECT * FROM turma where idturma = :joinT ");
        $selectTurmajoin->bindValue(":joinT", $_GET['join']);
        $selectTurmajoin->execute();

        $rowJ = $selectTurmajoin->fetch(mode: PDO::FETCH_ASSOC);

        $membrosAtuais = $rowJ["Participantes"] ? explode(" ", $rowJ["Participantes"]) : [];

        if (in_array($_SESSION['id'], $membrosAtuais) || $_SESSION['id'] == $rowJ['administrador']) {
            echo "<script language=javascript>
                location.href='index.php?page=Turma&turma=" . $_GET['join'] . "'
            </script>";
        }

        $nomeJ = $rowJ['nome'];
        $descricaoJ = $rowJ['descricao'];
        $joining = $_GET['join'];

        echo "
            <script language=javascript>
                Swal.fire({
                    title: '<h1>Deseja entrar na turma?</h1><h2>$nomeJ</h2><br>',
                    text: '$descricaoJ',
                    showDenyButton: true,
                    confirmButtonText: 'Entrar',
                    denyButtonText: `Cancelar`
                }).then((result) => {
                if (result.isConfirmed) {
                    location.href='Redirect.php?join=$joining'
                } else if (result.isDenied) {
                    location.href='Index.php'
                }
                });
            </script>
        ";
    }

    ?>





</body>

</html>