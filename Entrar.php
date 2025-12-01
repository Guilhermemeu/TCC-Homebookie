<?php
session_start()
    ?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1., user-scalable=no">
    <!-- Formatação -->
    <link rel="stylesheet" href="!Css/style.css">
    <script src="interação.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
    <script src="https://kit.fontawesome.com/3fed99b926.js" crossorigin="anonymous"></script>
    <!-- | Icone do Site | -->
    <link rel="icon" href="imagens/favicom.ico">
    <!-- | Titulo | -->
    <title>HomeBookie</title>

</head>

<body id="fundog">
    <div class=" mt-4 d-flex container-fluid justify-content-center" id="caixalogin">
        <div class="align-items-center d-flex" id="loginbox">
            <form method="POST" class="d-flex mx-sm-5 mx-2 flex-column" id="leftlogin">
                <a href="index.php"><i id="voltar" class="fa-solid fa-arrow-left"></i></a>
                <h1 class="mb-4" id="entrar">Inicie sua sessão</h1>
                <?php
                if (isset($_GET["error"])) {

                    switch ($_GET["error"]) {
                        case '404':
                            echo "<label class='erro'>Email ou Senha errado tente novamente</label>";
                            break;
                        case 'confirmar':
                            echo "<label class='erro'>Olhe o email para confirmação da conta</label>";
                            break;
                        case 'code':
                            echo "<label class='erro'>O codigo expirou ou ja foi ultilizado</label>";
                            break;
                        default:
                            echo "<label class='erro'>Ocorreu algum erro ao entrar</label>";
                            break;
                    }
                }
                if (isset($_GET["success"])) {
                    if ($_GET["success"] == '1') {
                        echo "<label class='confirmado'>o email foi confirmado!</label>";
                    }   
                }

                ?>
                <input class="mt-4 mb-2 inputlogin" placeholder="Email" id="email" name="email" type="text"
                    autocomplete="off" required>
                <input class="mt-2 mb-4 inputlogin" placeholder="Senha" id="senha" name="senha" type="password"
                    autocomplete="off" required>
                <button class="my-4" type="submit" value="entrar" name="entrar" id="botaologin">Entrar</button>
                <div class="d-flex flex-row align-items-center justify-content-center ms-2">
                    Não tem uma conta?<a class="my-4" href="cadastro.php">Registre-se!</a>
                </div>
            </form>
            <div class="d-flex align-items-center justify-content-end" id="fillerlogin">
                <img class="img-fluid" src="imagens/fillerlogin.png" id="imagefillerlogin" alt="Bem vindo de volta">
                <a href="cadastro.php"></a>
            </div>

        </div>
    </div>
    <?php
    require_once "Conexão/Conexao.php";

    try {
        if (isset($_REQUEST["entrar"])) {

            $email = $_REQUEST["email"];
            $senha = $_REQUEST["senha"];

            $sqlSelect = $conn->prepare(query: "SELECT * from usuario where email = :email");
            $sqlSelect->bindValue(param: ':email', value: $email);

            $sqlSelect->execute();

            $row = $sqlSelect->fetch(mode: PDO::FETCH_ASSOC);

            if (isset($row['idusuario']) && $row['idusuario'] != 0) {
                if (password_verify($senha, $row['senha'])) {
                    if ($row['ativada'] == 1) {

                        $_SESSION['id'] = $row['idusuario'];

                        echo "<script language=javascript>
                            location.href = 'index.php';
                            </script>";
                    } else {
                        echo "<script language=javascript>
                            location.href = 'Entrar.php?error=confirmar';
                            </script>";
                    }
                }
            } else if ($row['ativada'] == 0) {
                echo "<script language=javascript>
                  location.href = 'Entrar.php?error=404';
               </script>";
            }
        }
    } catch (PDOException $erro) {
        $erro->getMessage();
    }
    ?>


</body>

</html>