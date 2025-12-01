<?php
session_start();


if (isset($_SESSION["id"])) {
  $cookie_nome = "userid";
  $cookie_value = $_SESSION["id"];
  (setcookie(name: $cookie_nome, value: $cookie_value, expires_or_options: time() + 86400, path: "/")); // 86400 = 1 dia
} else {
  $_SESSION['id'] = 0;
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1., user-scalable=no">
  <!-- Formatação -->
  <link rel="stylesheet" href="!Css/style.css">
  <!-- JavaScript -->
  <script src="!Javascript/interacao.js"></script>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
  <!-- Fonte -->
  <script src="https://kit.fontawesome.com/3fed99b926.js" crossorigin="anonymous"></script>
  <!-- JSON -->
  <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>

  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <!-- | Icone do Site | -->
  <link rel="icon" href="imagens/favicom.ico">
  <!-- | Titulo | -->
  <title>HomeBookie</title>
</head>

<body>


  <?php
  require_once "Conexão/Conexao.php";
  include "Components/main.php";

  if (isset($_GET['page'])) {

    switch ($_GET['page']) {
      case 'Inicio':
        if ($_SESSION['id'] != 0) {
          include 'Components/Inicio.php';
        } else {
          include 'Components/join.php';
        }
        break;
      case 'Perfil':
        include 'Components/Perfil.php';
        break;
      case 'Turmas':
        include 'Components/Turmas.php';
        break;
      case 'Agenda':
        include 'Components/Agenda.php';
        break;
      case 'Sobre':
        include 'Components/Sobre.php';
        break;


      case 'Turma':
        include 'Components/Turma.php';
        break;
    }
  }
  //----------------| Pagina Inicial |--------------//


  else {
    if ($_SESSION['id'] != 0) {
      include 'Components/Inicio.php';
    } else {
      include 'Components/join.php';
    }
  }

  ?>

</body>

</html>