<?php
if ($_SESSION['id'] == 0) {
    echo "<script language='javascript'>
    location.href='Entrar.php'
    </script>";
} else {
    $selectusuario = $conn->prepare(query: "SELECT * FROM usuario where idusuario = :usuario");
    $selectusuario->bindvalue(":usuario", $_SESSION['id']);
    $selectusuario->execute();

    $rowUsuario = $selectusuario->fetch(mode: PDO::FETCH_ASSOC);

    if (isset($rowUsuario['participa']) && $rowUsuario['participa'] != null) {
        $Participa = $rowUsuario['participa'] ? explode(" ", $rowUsuario['participa']) : [];
    } else {
        $Participa = 0;
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body id="fundop">
    <?php
    if (isset($_GET['p'])) {
        $p = $_GET['p'];
    } else {
        $p = "";
    }
    ?>
    <svg xmlns="http://www.w3.org/2000/svg" style="position:absolute;" viewBox="0 0 1440 320">
        <path fill="#65b3699d" fill-opacity="1"
            d="M0,256L48,245.3C96,235,192,213,288,186.7C384,160,480,128,576,144C672,160,768,224,864,234.7C960,245,1056,203,1152,202.7C1248,203,1344,245,1392,266.7L1440,288L1440,0L1392,0C1344,0,1248,0,1152,0C1056,0,960,0,864,0C768,0,672,0,576,0C480,0,384,0,288,0C192,0,96,0,48,0L0,0Z">
        </path>
    </svg>
    <form method="get">
        <div style='background-color:#659d73;' class="container-fluid PesquisaDiv d-flex justify-content-center py-2">
            <input type="hidden" name="page" value="Turmas" style="font-display: none;">
            <input class="PesquisaBox px-1" type="text" placeholder="Pesquise Por Suas Turmas..." type="submit" name="p"
                value="<?php
                if (isset($_GET['p'])) {
                    echo "$p";
                }
                ?>">">
            <button type="submit" class="iconeSearch">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </form>
    <div class="SelectAllTurmas">
        <!-- | Select | -->
        <?php
        try {

            if (isset($_GET['p']) && $_GET['p'] != "") {
                $p = $_GET['p'];

                $sqlSelect = $conn->prepare(query: "SELECT * FROM turma WHERE nome LIKE '%" . $p . "%'");

            } else {
                $sqlSelect = $conn->prepare(query: "SELECT * FROM turma");
            }
            $sqlSelect->execute();

            $padrao = 3;

            while ($row = $sqlSelect->fetch(mode: PDO::FETCH_ASSOC)) {

                $nome = $row['nome'];
                $idturma = $row['idturma'];
                $membrosAtuais = $row['Participantes'];
                $imagem = $row['imagem'];

                if (isset($membrosAtuais)) {
                    $arraymembros = explode(" ", $membrosAtuais);
                }

                if ($padrao == 3) {
                    echo "<div class='container d-flex justify-content-evenly flex-wrap'>";
                }

                if ($row['administrador'] == $_SESSION['id']) {

                    echo "  
                <div class='TurmaSelect bg-success d-flex flex-column fadeInUp2-animation'>
                <img src='BancodeDados/BannerTurma/$imagem' style='border:none;' class='TurmaSelectIMG'>
                <div class='TurmaSelectNome container-fluid d-flex justify-content-between align-items-center'>
                <a class='LinkTurma ms-4' href='index.php?page=Turma&turma=$idturma'><h1>$nome</h1></a>
                <i class='fa-solid fa-crown MoreTurma me-1'></i>
                </div>
                </div>
                ";


                    $padrao = $padrao + 1;
                }
                if (!is_array($Participa)) {
                    $Participa = [0, 0];
                }

                if (in_array($idturma, $Participa)) {

                    if ($padrao == 6) {
                        echo "</div>";
                        $padrao = 3;
                    }
                    echo "  
                <div class='TurmaSelect bg-success d-flex flex-column fadeInUp2-animation'>
                <img src='BancodeDados/BannerTurma/$imagem' style='border:none;' class='TurmaSelectIMG'>
                <div class='TurmaSelectNome container-fluid d-flex justify-content-between align-items-center'>
                <a class='LinkTurma ms-4' href='index.php?page=Turma&turma=$idturma'><h1>$nome</h1></a>
                    <div class='dropdown'>
                <button style='background:none;border:none;'type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                    <i class='fa-solid MoreTurma me-1 fa-ellipsis-vertical'></i>
                </button>
                    <ul class='dropdown-menu'>
                        <li><a class='dropdown-item text-danger' href='Redirect.php?sair=$idturma'>Sair da Turma <i class='fa-solid text-danger fa-door-open'></i></a></li>
                    </ul>
                    </div>
                </div>
                </div>
                    ";


                    $padrao = $padrao + 1;
                }


                if ($padrao == 6) {
                    echo "</div>";
                    $padrao = 3;
                }
                ;
            }
        } catch (PDOException $ERRO) {
            echo $ERRO->getMessage();
        }


        ?>

    </div>


</body>

</html>