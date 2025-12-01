<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require "!lib/vendor/autoload.php";

require_once "Conexão/Conexao.php";

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
  <br>

  <div class=" mt-bg-1 mt-1 d-flex container-fluid justify-content-center flex-row-reverse" id="caixalogin">
    <div class="align-items-center d-flex flex-row-reverse" id="loginbox" style="border-color:#964CA2 !important;">
      <form method="POST" class="d-flex mx-sm-5 mx-2 flex-column" id="leftlogin">
        <a href="index.php"><i id="voltar" class="fa-solid fa-arrow-left"></i></a>
        <h1 class="mt-1 mb-4" id="entrar" style="color:#70607F !important">Crie sua conta</h1>
        <?php
        if (isset($_GET["error"])) {
          switch ($_GET['error']) {

            case 'email':
              echo "<label class='erro'>O Email ja esta sendo ultilizado</label>";
              break;
            case 'senha':
              echo "<label class='erro'>As senhas não são correspondentes</label>";
              break;
            case 'senhalenght':
              echo "<label class='erro'>A senha tem menos de 8 caracteres</label>";
              break;
            default:
              echo "<label class='erro'>Ocorreu algum erro ao cadastrar</label>";
              break;
          }

        }

        ?>
        <input class="mt-4 mb-2 inputlogin" required placeholder="email" type="email"
          style="background-color:#9A83AF !important;" name="email" autocomplete="off">
        <input class="mt-2 mb-2 inputlogin" required placeholder="senha" type="password"
          style="background-color:#9A83AF !important;" name="senha" autocomplete="off" minlength="8">
        <input class="mt-2 mb-4 inputlogin" required placeholder="Confirme a Senha" type="password"
          style="background-color:#9A83AF !important;" name="csenha" autocomplete="off" minlength="8">
        <button type="submit" value="inserir" name="enviar" class="my-4" id="botaologin"
          style="background-color:#6B8D70 !important;">Criar</button>

        <div class="d-flex flex-row align-items-center justify-content-center ms-2">
          já tem uma conta?<a class="my-4" href="Entrar.php">Entre!</a>
      </form>

      <?php
      if (isset($_REQUEST["enviar"])) {
        $email = $_POST["email"];
        $senha = $_POST["senha"];
        $csenha = $_POST["csenha"];



        if ($senha != $csenha) {
          echo "<script language=javascript>
                  location.href = 'Cadastro.php?error=senha';
                </script>";
        } else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
          echo "<script language=javascript>
                  location.href = 'Cadastro.php?error=email404';
                </script>";
        } else if (strlen($senha) < 8) {
          echo "<script language=javascript>
                  location.href = 'Cadastro.php?error=senhalenght';
                </script>";
        } else {
          $email = filter_var($email, FILTER_SANITIZE_EMAIL);

          try {
            $selectemail = $conn->prepare(query: "SELECT * FROM usuario where email = :email");
            $selectemail->bindValue(":email", $email);
            $selectemail->execute();

            $rowE = $selectemail->fetch(PDO::FETCH_ASSOC);

            if (isset($rowE['email'])) {
              echo "<script language=javascript>
              location.href = 'Cadastro.php?error=email';
              </script>";
            } else {

              $cryptsenha = password_hash($senha, PASSWORD_DEFAULT);

              $sqlinsert = $conn->prepare(query: "INSERT into usuario(idusuario,email,senha) values(null,:email,:senha)");


              $sqlinsert->bindValue(param: ":email", value: $email);
              $sqlinsert->bindValue(param: ":senha", value: $cryptsenha);

              if ($sqlinsert->execute()) {
                $mail = new PHPMailer(true);
                try {


                  $pullnewuser = $conn->prepare(query: "SELECT * from usuario where email = :email");
                  $pullnewuser->bindValue(":email", $email);

                  if ($pullnewuser->execute()) {

                    $needer = $pullnewuser->fetch(PDO::FETCH_ASSOC);

                    $Vcode = md5(string: time());

                    $createcode = $conn->prepare(query: "INSERT INTO vcodes(idcode,code,idneeder) values (null,:vcode,:needer)");
                    $createcode->bindValue(":vcode", $Vcode);
                    $createcode->bindValue(":needer", $needer['idusuario']);

                    $createcode->execute();
                  }



                  //Configurações do Server
                  $mail->SMTPDebug = SMTP::DEBUG_SERVER;
                  $mail->isSMTP();
                  //através de:
                  $mail->Host = 'sandbox.smtp.mailtrap.io';
                  $mail->SMTPAuth = true;
                  $mail->Username = 'da56e51128e4bb';
                  $mail->Password = '6702faef5d728e';
                  $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                  $mail->Port = 2525;

                  $mail->setFrom('Homebookie@inc.com.br', 'Homebookie');
                  $mail->addAddress($email);
                  $mail->addReplyTo('info@example.com', 'Information');

                  //Conteudo
                  $mail->isHTML(true);
                  $mail->Subject = 'Confirmação de email';
                  $mail->Body = "O usuario $email foi cadastrado no site <strong>Homebookie</strong>
                  <br>Para confirmar o cadastro em nosso sistema necessitamos que clique no link a seguir:
                  <a href='localhost/homebookie/redirect.php?confirm=$Vcode'>Confirmar</a>";

                  $mail->AltBody = "O usuario $email foi cadastrado no site Homebookie
                  \nPara confirmar o cadastro em nosso sistema necessitamos que clique no link a seguir:
                  <a href='localhost/homebookie'>Confirmar</a>";
                  ;

                  if ($mail->send()) {


                    echo "<script language=javascript>
                    location.href='entrar.php?error=confirmar'
                    </script>";
                  }
                } catch (Exception $e) {
                  echo "Mensagem não pode ser enviada. PHPMailer Erro:{$mail->ErrorInfo}";
                }
              }
            }
          } catch (PDOException $e) {
            echo "" . $e->getMessage() . "";
          }
        }
      }
      ?>

    </div>
    <div class="d-flex align-items-center justify-content-end" id="fillerlogin">
      <img class="img-fluid" src="imagens/fillercadastro.png" id="imagefillerlogin" alt="Crie sua conta">
      <a href="cadastro.php"></a>
    </div>

  </div>
  </div>
</body>

</html>
<!-- 760f8e320d6f0e329905462ee049cf8b -->