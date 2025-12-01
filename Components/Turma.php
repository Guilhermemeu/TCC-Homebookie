<?php
if ($_SESSION['id'] == 0) {
    echo "<script language='javascript'>
    location.href='Entrar.php'
    </script>";
} else {

    if (is_int((int) $_GET["turma"])) {
        $idTurma = $_GET["turma"];
    } else {
        echo "<script language=javascript>
            location.href='Index.php?page=Turmas'
        </script>";
    }
    $SelectinTurma = $conn->prepare(query: "SELECT * FROM turma where idturma = :idturma");
    $SelectinTurma->bindValue(":idturma", $idTurma);
    $SelectinTurma->execute();

    $row = $SelectinTurma->fetch(mode: PDO::FETCH_ASSOC);

    $nome = $row["nome"];
    $materia = $row["materia"];
    $descricao = $row["descricao"];
    $administrador = $row["administrador"];


    $membrosAtuais = $row["Participantes"];

    if ($membrosAtuais != null) {
        $arraymembros = explode(" ", $membrosAtuais);
    } else {
        $membrosAtuais = "0 0";
        $arraymembros = explode(" ", $membrosAtuais);
    }


    if (!in_array($_SESSION['id'], $arraymembros) && $_SESSION['id'] != $administrador) {
        echo "<script language=javascript>
            location.href='Index.php'
        </script>";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body id="fundop">

    <div class="BannerTurma container-fluid d-flex bg-success align-items-center pb-5">
        <?php echo "<div class='NomeTurma'>
                    <h1 style='color:#fff;font-size: 3rem;' class='pt-2'>$nome</h1> 
                    <i><h2 style='color:#fff;'>$materia</h2></i>
                    </div>";

        if ($_SESSION["id"] == $administrador) {
            echo "
            <button class='EditarTurma align-self-start mt-1' data-bs-toggle='modal' data-bs-target='#exampleModal'>
            <i class='fa-solid fa-pen-to-square' style='color:#964ca2;'></i>
            </button>
            
                <form method='POST' enctype='multipart/form-data' action=''>
        <div class='modal fade' id='exampleModal' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content glass-card'>
                    <div class='modal-header'>
                        <h1 class='modal-title fs-5' id='exampleModalLabel'>Altere a sua Turma</h1>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body'>

                        <label for='nomeT' class='col-form-label'>Nome:</label>
                        <input required placeholder='Altere o nome da turma' type='text' class='form-control'
                            name='nomeT' id='nomeT' value='$nome'>

                        <label for='fotob' class='ButtonTemplate2 file mt-3 col-form-label'>alterar imagem</label>
                        <input type='file' id='fotob' class='my-3 btn btn-sucess' accept='image/png .jpeg .jpg .webp'
                            name='fotob'><br>

                        <label for='materiaT' class='col-form-label'>Matéria:</label>
                        <input placeholder='Altere a Materia...' class='form-control' name='materiaT' id='materiaT'
                            value='$materia'>

                        <label for='descricaoT' class='col-form-label'>Descrição:</label>
                        <textarea placeholder='Altere a Descrição...' class='form-control' name='descricaoT'
                            id='descricaoT'>$descricao</textarea>

                    </div>
                    <div class='modal-footer'>
                        <button type='submit' value='alterTurma' id='alterTurma' name='alterTurma'
                           class='ButtonTemplate1'>Alterar</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
            
            
            ";
        }

        ?>
        <button class="EditarTurma align-self-start mt-1" data-bs-toggle='modal' data-bs-target='#exampleModal1'>
            <i class="fa-solid fa-circle-user" style='color:#964ca2;'></i>
        </button>
    </div>

    <!-- | Modal de integrantes | -->


    <div enctype='multipart/form-data' action=''>
        <div class='modal fade' id='exampleModal1' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content glass-card'>
                    <div class='modal-header'>
                        <h1 class='modal-title fs-5' id='exampleModalLabel'>Integrantes da Turma</h1>
                        <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                    </div>
                    <div class='modal-body CountUsers'>

                        <?php

                        $j = 0;
                        while ($j < count($arraymembros) + 1 && isset($arraymembros[$j]) && $arraymembros[0] != 0) {

                            $selectuserinfo = $conn->prepare("SELECT * FROM usuario where idusuario = :idusuario");
                            $selectuserinfo->bindValue(":idusuario", $arraymembros[$j]);
                            $selectuserinfo->execute();
                            $RowU = $selectuserinfo->fetch(PDO::FETCH_ASSOC);

                            $nomeUsuario = $RowU['nome'];
                            $fotopR = $RowU['fotop'];
                            $idusuario = $RowU['idusuario'];
                            echo "
                            <div class='container UserList d-flex mt-2 pt-1'>
                            <img class='FotoPerfilChat' src='BancodeDados/FotosdePerfil/$fotopR'>
                            <a href='index.php?page=Perfil&userid=$idusuario'><h1 style='text-decoration:underline; '>$nomeUsuario#$idusuario</h1></a>
                            </div>
                            ";
                            
                        $j += 1;

                        }

                        if ($arraymembros[0] == 0){
                            echo "<h1 style='font-size:1.6rem;'>Convide Pessoas Para sua Turma !!</h1>";
                        }




                        ?>

                    </div>
                </div>
            </div>
        </div>
    </div>



    <div class="container-fluid py-2 " style="background-color:#693072;"> </div>

    <div class="MenuTurma container mt-3 d-flex justify-content-center flex-wrap">
        <div class="d-flex flex-row">
            <a href="<?php echo "index.php?page=Turma&turma=$idTurma&Tab=Inicio" ?>" class="px-2<?php if (isset($_GET["Tab"]) && $_GET['Tab'] == 'Inicio') {
                                                                                                    echo " ActiveTab";
                                                                                                } ?>">
                Inicio
            </a>
            <a href="<?php echo "index.php?page=Turma&turma=$idTurma&Tab=Comunidade" ?>" class="px-2<?php if (isset($_GET["Tab"]) && $_GET['Tab'] == 'Comunidade') {
                                                                                                        echo " ActiveTab";
                                                                                                    } ?>">
                Comunidade
            </a>
        </div>
        <div class="d-flex flex-row">
            <a href="<?php echo "index.php?page=Turma&turma=$idTurma&Tab=Conteudos" ?>" class="px-2<?php if (isset($_GET["Tab"]) && $_GET['Tab'] == 'Conteudos') {
                                                                                                        echo " ActiveTab";
                                                                                                    } ?>">
                Conteudos
            </a>

            <?php
            if ($_SESSION["id"] == $administrador) {
                echo "<a href='index.php?page=Turma&turma=$idTurma&Tab=Recebidos'";
                if (isset($_GET["Tab"]) && $_GET['Tab'] == 'Recebidos') {
                    echo "class='px-2 ActiveTab'>Recebidos</a>";
                } else {
                    echo "class='px-2'>Recebidos</a>";
                }
            }
            ?>
        </div>
    </div>

    <div class="DashTurma pb-1 mt-1 container" style="background-color:#964ca2;"></div>



    <?php

    if (isset($_REQUEST['alterTurma'])) {

        $nomeT = $_POST['nomeT'];
        $nomeTFixed = preg_replace(pattern: '/[^\p{L}\p{N} ]/u', replacement: '', subject: $nomeT);
        $materiaT = $_POST['materiaT'];
        $descricaoT = $_POST['descricaoT'];

        if ($_FILES['fotob']['error'] !== UPLOAD_ERR_OK) {
            $fotob = "Turma.png";
        } else {

            $extensao = strtolower(string: substr(string: $_FILES['fotob']['name'], offset: -4));
            $fotob = md5(string: time()) . $extensao;
            $diretorioR = "BancodeDados/BannerTurma/";
        }

        try {

            $TurmaUpdate = $conn->prepare("UPDATE turma SET nome = :nome, materia = :materia, descricao = :descricao, imagem = :imagem WHERE idturma = $idTurma");
            $TurmaUpdate->bindValue(":nome", $nomeTFixed);
            $TurmaUpdate->bindValue(":materia", $materiaT);
            $TurmaUpdate->bindValue(":descricao", $descricaoT);
            $TurmaUpdate->bindValue(":imagem", $fotob);

            if ($TurmaUpdate->execute()) {

                if ($_FILES['fotob']['name'] != "") {
                    move_uploaded_file($_FILES['fotob']['tmp_name'], $diretorioR . $fotob);
                }
                echo "<script language=javascript>
                location.href='Index.php?page=Turma&turma=" . $idTurma . "'
                </script>";
            }
        } catch (PDOException $e) {
            echo $e->getMessage();
        }
    }
    ?>

    <?php

    if (isset($_GET['Tab'])) {

        switch ($_GET['Tab']) {
            case 'Inicio':
                include 'SubComponents/TurmaInicio.php';
                break;
            case 'Comunidade':
                include 'SubComponents/TurmaComunidade.php';
                break;
            case 'Conteudos':
                include 'SubComponents/TurmaConteudos.php';
                break;
            case 'Recebidos':
                if ($_SESSION['id'] == $administrador) {
                    include 'SubComponents/TurmaRecebidos.php';
                } else {
                    include 'SubComponents/TurmaInicio.php';
                }
                break;
        }
    } else {
        echo "<script language=javascript>
            location.href='index.php?page=Turma&turma=$idTurma&Tab=Inicio'
        </script>
        ";
    }
    ?>

</body>

</html>