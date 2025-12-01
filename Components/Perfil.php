<?php

if ($_SESSION['id'] == 0) {
    echo "<script language='javascript'>
    location.href='Entrar.php'
    </script>";
} else {

    $userid = $_GET['userid'];

    $sqlSelect = $conn->prepare(query: "SELECT * from usuario where idusuario = :userid");
    $sqlSelect->bindValue(param: ":userid", value: $userid);
    $sqlSelect->execute();
    $row = $sqlSelect->fetch(mode: PDO::FETCH_ASSOC);

    $email = $row['email'];
    $nome = $row['nome'];
    $fotop = $row['fotop'];

    $descricao = $row['descricao'];
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body id="fundop">

    <div class="container-fluid BannerP d-flex justify-content-center">
        <div class="FotodivP d-flex justify-content-center align-items-center">
            <img class="FotoP" src="BancodeDados/FotosDePerfil/<?php echo "$fotop" ?>">
        </div>
    </div>
    <div class="container-fluid mt-5 pt-5 d-flex justify-content-center align-items-center flex-column">
        <div class="d-flex flex-row nomePerfil">
            <?php

            if ($userid == $_SESSION['id']) {

                echo "
            <div>
            <h2 class=''>$nome#$userid</h2>
            </div>
            <button class='buttonNome'data-bs-toggle='modal' data-bs-target='#exampleModal2'>
            <i class='fa-solid fa-pen-to-square' ></i>
            </button>
            <form method='POST' action='index.php?page=Perfil&userid=$userid' enctype='multipart/form-data'>
            <div class='modal fade' id='exampleModal2' tabindex='-1' aria-labelledby='exampleModalLabel' aria-hidden='true'>
            <div class='modal-dialog modal-dialog-centered'>
                <div class='modal-content glass-card'>
                <div class='modal-header'>
                    <h1 class='modal-title fs-5' id='exampleModalLabel'>Editar Perfil</h1>
                    <button type='button' class='btn-close' data-bs-dismiss='modal' aria-label='Close'></button>
                </div>
                <div class='modal-body '>

                    <label for='fotop' class='ButtonTemplate2 file col-form-label'>Alterar imagem</label>
                    <input value='$fotop' type='file' id='fotop' class='my-3 btn btn-sucess' accept='image/png .jpeg .jpg .webp' name='imagem'><br>

                    <label for='nome' class='col-form-label'>Insira um novo nome:</label>
                    <input value='$nome' type='text' maxlength='15' class='form-control' name='nome' id='nome'>

                    <label for='descricao' class='col-form-label'>Descrição:</label>
                    <textarea class='form-control' name='descricao' id='descricao'>$descricao</textarea>

                    </div>
                <div class='modal-footer'>
                            <button type='submit' value='trocanome' id='trocanome' name='trocanome' class='ButtonTemplate1'>Alterar</button>
                </div>
                </div>
            </div>
            </div>
            </form>
            ";
                if (isset($_REQUEST["trocanome"])) {

                    $nome = $_POST["nome"];
                    if (strlen($nome) > 15) {
                        $nome = "Babaca";
                    }
                    $nome = preg_replace(pattern: '/[^\p{L}\p{N} ]/u', replacement: '▯', subject: $nome);

                    $descricao = $_POST["descricao"];
                    $descricao = preg_replace(pattern: '/[^\p{L}\p{N} ]/u', replacement: '', subject: $descricao);

                    $extensao = strtolower(string: substr(string: $_FILES['imagem']['name'], offset: -4));
                    $novo_nome = md5(string: time()) . "." . $extensao;
                    $diretorio = "BancodeDados/FotosdePerfil/";

                    if ($extensao == "" or $extensao == null) {
                        $novo_nome = "FotoP.png";
                    }

                    move_uploaded_file(from: $_FILES['imagem']['tmp_name'], to: $diretorio . $novo_nome);


                    try {
                        $trocanome = $conn->prepare(query: "UPDATE usuario set nome = ' $nome',fotop = '$novo_nome', descricao = '$descricao' WHERE idusuario = " . $_SESSION['id'] . "");

                        $trocanome->execute();

                        echo "<script lenguage=javascript>
                        location.href='Index.php?page=Perfil&userid=$userid'
                        </script>
                    ";
                    } catch (PDOException $e) {
                        echo "" . $e->getMessage() . "";

                    }
                }
            } else {
                if (isset($_GET['wasfrom'])) {
                    $wasfrom = $_GET['wasfrom'];
                    echo "

                <a style='' href='index.php?page=Turma&turma=$wasfrom&Tab=Comunidade'><i style='font-size:1.5rem; color:#8e549c;' id='voltar' class='fa-solid fa-arrow-left'></i></a>";
                }
                echo "<h2 style='margin-left:1rem;'>$nome#$userid</h2>
                ";
            }
            ?>
        </div>
        <div class="NomeP"><?php echo "<h5>$email</h5>" ?></div>
    </div>
    <div class="container-fluid  d-flex justify-content-center">
        <?php echo "<h4 class='descricaotxt'>$descricao</h4>" ?>
    </div>

    <div class="container-fluid d-flex justify-content-end FooterPerfil flex-column">
        <svg class='' xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1440 320">
            <path fill="#702c7a6b" fill-opacity="1"
                d="M0,96L40,101.3C80,107,160,117,240,138.7C320,160,400,192,480,197.3C560,203,640,181,720,170.7C800,160,880,160,960,144C1040,128,1120,96,1200,85.3C1280,75,1360,85,1400,90.7L1440,96L1440,320L1400,320C1360,320,1280,320,1200,320C1120,320,1040,320,960,320C880,320,800,320,720,320C640,320,560,320,480,320C400,320,320,320,240,320C160,320,80,320,40,320L0,320Z">
            </path>
        </svg>
    </div>

</body>

</html>