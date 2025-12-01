<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Homebookie</title>
</head>

<body>


    <h1>Caso não for redirecionado:</h1>
    <a href="index.php">Retornar</a>

    <?php
    require_once("Conexão/Conexao.php");
    session_start();

    if (isset($_GET['turma'])) {
        $idTurma = $_GET['turma'];
    }
    if (isset($_GET['page'])) {
        switch ($_GET['page']) {
            case 'Turma';
                echo "<script language=javascript>
        location.href='index.php?page=Turma&turma=$idTurma'
        </script>";
                break;
            case 'Comunidade':
                echo "<script languge=javascript>
        location.href='index.php?page=Turma&turma=$idTurma&Tab=Comunidade'
        </script>";
                break;

        }
        if (isset($_GET['tarefa'])) {
            $tarefa = $_GET['tarefa'];
            if (isset($_GET['user'])) {
                $isAdm = $_GET['user'];
                echo "<script languge=javascript>
        location.href='index.php?page=Agenda&Tarefa=$tarefa&user=$isAdm'
        </script>";
            } else {
                echo "<script languge=javascript>
        location.href='index.php?page=Agenda&Tarefa=$tarefa'
        </script>";
            }
        }
    }


    if (isset($_GET["join"])) {

        $idTurma = $_GET["join"];
        $id = $_SESSION["id"];

        $selectmembros = $conn->prepare("SELECT Participantes FROM turma WHERE idturma = :idTurma");
        $selectmembros->bindParam(':idTurma', $idTurma);
        $selectmembros->execute();

        $row = $selectmembros->fetch(PDO::FETCH_ASSOC);

        $membrosAtuais = $row["Participantes"] ? explode(" ", $row["Participantes"]) : [];

        if (!in_array($id, $membrosAtuais)) {
            $membrosAtuais[] = $id;
        }

        $novosMembros = implode(" ", $membrosAtuais);

        $alterarMembros = $conn->prepare("UPDATE turma SET Participantes = :novosMembros WHERE idturma = :idTurma");
        $alterarMembros->bindParam(':novosMembros', $novosMembros);
        $alterarMembros->bindParam(':idTurma', $idTurma);
        $alterarMembros->execute();

        /*-----------------------| USUARIO UPDATE |------------------------------*/


        $participaselect = $conn->prepare("SELECT participa FROM usuario WHERE idusuario = :id");
        $participaselect->bindValue(":id", $id);
        $participaselect->execute();

        $rowP = $participaselect->fetch(PDO::FETCH_ASSOC);

        $participa = $rowP['participa'] ? explode(" ", $rowP['participa']) : [];

        if (!in_array($idTurma, $participa)) {
            $participa[] = $idTurma;
        }

        $participaNew = implode(" ", $participa);

        $alterarMembros = $conn->prepare("UPDATE usuario SET participa = :participaNew WHERE idusuario = :id");
        $alterarMembros->bindParam(':participaNew', $participaNew);
        $alterarMembros->bindParam(':id', $id);
        $alterarMembros->execute();

        echo "<script language=javascript>
                location.href = 'Index.php?page=Turmas'
            </script>
        ";
    }

    if (isset($_GET["entrega"])) {
        echo "<script language=javascript>
            location.href='index.php?page=Agenda'
        </script>";
    }

    /*-----------------------| SAIR TURMA |------------------------------*/

    if (isset($_GET['sair'])) {
        $idTurma = $_GET["sair"];
        $id = $_SESSION["id"];

        $selectmembros = $conn->prepare("SELECT Participantes FROM turma WHERE idturma = :idTurma");
        $selectmembros->bindParam(':idTurma', $idTurma);
        $selectmembros->execute();

        $row = $selectmembros->fetch(PDO::FETCH_ASSOC);

        $Membros = explode(" ", $row['Participantes']);
        $RemoverUser = array_search($_SESSION['id'], $Membros);

        if ($RemoverUser !== false) {
            unset($Membros[$RemoverUser]);
        }
        $Membros = implode(" ", $Membros);


        $updateSair = $conn->prepare("UPDATE turma SET Participantes = :membros WHERE idTurma = :idTurma");
        $updateSair->bindParam(':idTurma', $idTurma);
        $updateSair->bindValue(':membros', $Membros);
        $updateSair->execute();

        /*-----------------------| USUARIO UPDATE |------------------------------*/

        $participasair = $conn->prepare("SELECT participa FROM usuario WHERE idusuario = :id");
        $participasair->bindValue(":id", $id);
        $participasair->execute();

        $rowP = $participasair->fetch(PDO::FETCH_ASSOC);

        $participa = $rowP['participa'] ? explode(" ", $rowP['participa']) : [];

        $RemoverParticipa = array_search($idTurma, $participa);
        if ($RemoverParticipa !== false) {
            unset($participa[$RemoverParticipa]);
        }
        $participa = implode(" ", $participa);

        $alterarMembros = $conn->prepare("UPDATE usuario SET participa = :participa WHERE idusuario = :id");
        $alterarMembros->bindParam(':participa', $participa);
        $alterarMembros->bindParam(':id', $id);
        $alterarMembros->execute();


        echo "<script language=javascript>
            location.href='index.php?page=Turmas'
        </script>";
    }
    /*-----------------------| CONFIRMAR EMAIL |------------------------------*/


    if (isset($_GET['confirm'])) {
        $codexist = $conn->prepare("SELECT * from vcodes WHERE code = :vcode and used = 0");
        $codexist->bindValue(":vcode", $_GET['confirm']);
        $codexist->execute();

        $rowvcode = $codexist->fetch(PDO::FETCH_ASSOC);

        if (isset($rowvcode)) {
            $confirmado = $conn->prepare(query: "UPDATE usuario SET ativada = 1 WHERE idusuario = :idusuario");
            $confirmado->bindvalue(":idusuario", $rowvcode['idneeder']);

            if ($confirmado->execute()) {

                $carimbo = $conn->prepare("UPDATE vcodes SET used = 1 WHERE idneeder = :idusuario");
                $carimbo->bindvalue(":idusuario", $rowvcode['idneeder']);

                if ($carimbo->execute()) {

                    echo " <script language=javascript>
                    location.href='entrar.php?success=1'
                    </script>";
                }
            }
        } else {
            echo " <script language=javascript>
            location.href='entrar.php?error=code'
            </script>";
        }
    }
    /*-----------------------| DONE WHAT |--------------------------*/

    if (isset($_GET['who']) && isset($_GET['what']) && isset($_GET['where']) && !isset($_GET['remove'])) {
        $entregar = $conn->prepare("INSERT recebidos(id_recebido,id_sender,id_turma,id_tarefa,mensagem,entrega) VALUES(null,:who,:where,:what,null,null)");
        $entregar->bindValue(":who", $_GET['who']);
        $entregar->bindValue(":what", $_GET['what']);
        $entregar->bindValue(":where", $_GET['where']);
        if ($entregar->execute()) {

            $where = $_GET['where'];
            $what = $_GET['what'];
            echo "<script language=javascript>
                    location.href='index.php?page=Turma&turma=$where&Tab=Recebidos&tarefa=$what'
                </script>
               ";
        }
    } else if (isset($_GET['what']) && isset($_GET['remove']) && !isset($_GET['fromA'])) {
        $what = $_GET['what'];
        if (isset($_GET['from'])) {
            $from = $_GET['from'];
        }
        $tarefa = $_GET['tarefa'];

        $delete = $conn->prepare(" DELETE FROM recebidos WHERE id_recebido = :what");
        $delete->bindValue(":what", $what);
        if ($delete->execute()) {
        }
        if (isset($_GET['what']) && isset($_GET['remove']) && !isset($_GET['fromA'])) {
            echo "<script language=javascript>
                location.href='index.php?page=Agenda&Tarefa=$tarefa';
                </script>";
        } else {
            echo "<script language=javascript>
                location.href='index.php?page=Turma&turma=$from&Tab=Recebidos&tarefa=$tarefa';
                </script>";
        }
    }


    ?>

</body>

</html>