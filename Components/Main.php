<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body>

  <!-- | Banner | -->

  <div class="container-fluid d-flex align-items-center " id="Banner">
    <div class="d-flex align-items-center">
      <img src="imagens/logo.png" id="logo" alt="Homebookie Logo">
      <h2 id="Title">Homebookie</h2>
    </div>
    <?php

    if (!isset($_SESSION["id"]) || $_SESSION["id"] == 0) {

      echo "
      <div class='RL'>
      <a href='Cadastro.php'><button class='px-md-3 py-md-2 py-1 ButtonTemplate2'>Registre-se</button></a>
      <a href='entrar.php'><button class='px-md-3 py-md-2 py-1 ButtonTemplate1'>‎ ‎ ‎ ‎ Entrar‎ ‎ ‎ ‎ </button></a>
      </div>";
    } else if (isset($_SESSION["id"]) && $_SESSION["id"] != null) {
      echo "<form method='POST' action='index.php'>
      <button class='px-md-5 py-md-2 py-1 me-2 ButtonTemplate2' name='Sair' id='Sair'>‎Sair‎</button>
      </form>";
      if (isset($_REQUEST["Sair"])) {
        $_SESSION["id"] = 0;
        echo "<script language=javascript>
              location.href = 'index.php';
              </script>";
      }
    }
    ;
    ?>
  </div>

  <div class="container-fluid" style="height:15px;background-color: #8e549c;" id="UnderBanner"></div>


  <!-- | SideBar | -->


  <div class="container-fluid" style="padding-right: 0px;padding-left: 0px !important">
    <div class="col-sm-auto sticky-top">
      <div class="sidebar" id="DivBar">

        <a href="?page=Inicio" class="navi d-flex align-items-center <?php if((isset($_GET['page']) && $_GET['page'] == 'Inicio') || !isset($_GET['page'])) echo " active" ?>">
          <img src="Imagens/Inicio.png" alt="Home">
          <span id="textoside" class="sidebar-text">Inicio</span>
        </a>

        <a href="?page=Perfil&userid=<?php $id = $_SESSION['id'] ;echo "$id"; ?>" class="navi d-flex align-items-center <?php if(isset($_GET['page']) && $_GET['page'] == 'Perfil') echo " active" ?>">
          <img src="Imagens/Perfil.png" alt="Perfil">
          <span id="textoside" class="sidebar-text">Perfil</span>
        </a>

        <a href="?page=Turmas" class="navi d-flex align-items-center <?php if(isset($_GET['page']) && $_GET['page'] == 'Turmas') echo " active" ?>">
          <img src="Imagens/Turmas.png" alt="Turmas">
          <span id="textoside" class="sidebar-text">Turmas</span>
        </a>

        <a href="?page=Agenda" class="navi d-flex align-items-center <?php if(isset($_GET['page']) && $_GET['page'] == 'Agenda') echo " active" ?>">
          <img src="Imagens/Agenda.png" alt="Agenda">
          <span id="textoside" class="sidebar-text">Agenda</span>
        </a>

        <a href="?page=Sobre" class="navi d-flex align-items-center <?php if(isset($_GET['page']) && $_GET['page'] == 'Sobre') echo " active" ?>">
          <img src="Imagens/Sobre.png" alt="Sobre">
          <span id="textoside" class="sidebar-text">Sobre</span>
        </a>

        <div class="fillSidebar">
        </div>

      </div>
    </div>
  </div>

</body>

</html>