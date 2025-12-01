<!DOCTYPE html>
<html lang="pt-br">

<head>
</head>

<body>

    <div class="ListaTarefas mt-4 container">
        <?php
        if (!isset($_GET['tarefa'])) {
            echo "<div class='container-fluid d-flex justify-content-center align-items-center'>
                <h1>Tarefas Postadas:</h1>
            </div>";
        }



        $SelectAtividades = $conn->prepare(query: "SELECT * FROM tarefas where turma_id = :idturma AND Tarefa = 1 ORDER BY idTarefas DESC;");
        $SelectAtividades->bindValue(":idturma", $idTurma);
        $SelectAtividades->execute();

        while ($row = $SelectAtividades->fetch(mode: PDO::FETCH_ASSOC)) {
            $idTarefas = $row['idtarefas'];
            $dataTarefa = $row['data_tarefa'];
            $nomeTarefa = $row['nome_tarefa'];

            if (!isset($_GET['tarefa'])) {

                echo "
                <a href='index.php?page=Turma&turma=$idTurma&Tab=Recebidos&tarefa=$idTarefas'>
                        <div class='InputInicio d-flex justify-content-between' style='background-color:#fff; margin-top:15px; max-width:93%;'>
                        <h3 style='margin-left:35px;'>$nomeTarefa</h3>
                        <h5 style='opacity:0.5; margin-right:35px; position:relative;'>$dataTarefa</h5>
                        </div>
                </a>";
            }
        }
        if (isset($_GET['tarefa'])) {
            $tarefa = $_GET['tarefa'];
            try {

                $countallusers = $conn->prepare("SELECT * FROM turma WHERE idturma = :idturma");
                $countallusers->bindValue(":idturma", $idTurma);
                $countallusers->execute();
                $RowC = $countallusers->fetch(PDO::FETCH_ASSOC);

                $participantes = explode(" ", $RowC['Participantes']);
                $i = 0;

                $selectRecebidos = $conn->prepare(query: "SELECT * FROM recebidos WHERE id_turma = :idturma AND id_tarefa = :idtarefa ORDER BY id_recebido DESC;");
                $selectRecebidos->bindValue(":idturma", $idTurma);
                $selectRecebidos->bindValue(":idtarefa", $tarefa);
                $selectRecebidos->execute();

                $entregou = [];

                while ($RowR = $selectRecebidos->fetch(PDO::FETCH_ASSOC)) {
                    array_push($entregou, $RowR['id_sender']);
                }

                echo "
                <div class='container-fluid d-flex justify-content-between align-items-center'>
                    <a style='' href='index.php?page=Turma&turma=$idTurma&Tab=Recebidos'><i style='font-size:1.5rem;' id='voltar' class='fa-solid fa-arrow-left'></i></a>
                    <h1>Entregas da Tarefa:</h1>
                    <div style='width:0%;'>
                    </div>
                </div>
                ";

                while ($i < count($participantes) + 1 && isset($participantes[$i])) {

                    $selectuserinfo = $conn->prepare("SELECT * FROM usuario where idusuario = :idusuario");
                    $selectuserinfo->bindValue(":idusuario", $participantes[$i]);
                    $selectuserinfo->execute();
                    $RowU = $selectuserinfo->fetch(PDO::FETCH_ASSOC);

                    $nomeUsuario = $RowU['nome'];
                    $fotopR = $RowU['fotop'];
                    $idusuario = $RowU['idusuario'];

                    if (in_array($participantes[$i], $entregou)) {

                        $entrega = $conn->prepare("SELECT * FROM recebidos where id_sender = :idusuario");
                        $entrega->bindValue(":idusuario", $idusuario);
                        $entrega->execute();
                        $RowE = $entrega->fetch(PDO::FETCH_ASSOC);

                        $id_recebido = $RowE['id_recebido'];
                        
                        $mensagem = $RowE['mensagem'];
                        $ArquivoR = $RowE['entrega'];
                        $id_tarefa = $RowE['id_tarefa'];

                        echo "
                            <div class='InputInicio d-flex justify-content-between' style='background-color:#ffffffbd; margin-top:15px; max-width:93%;'>
                            <a href='index.php?page=Perfil&userid=$idusuario' style='min-width:200px;' class='d-flex flex-row align-items-center justify-content-center'>
                                <img class='FotoPerfilChat ms-lg-2 ms-1' src='BancodeDados/FotosdePerfil/$fotopR' alt='$nomeUsuario'>
                                <h3 class='done' style='margin-left:35px;'>$nomeUsuario#$idusuario</h3>
                            </a>
                            <a href='index.php?page=Agenda&Tarefa=$id_tarefa&user=$idusuario'>
                                <h5 style='opacity:0.8;position:relative;'>$mensagem</h5>
                            </a>";
                                if ($ArquivoR != null) {
                            echo "<a href='BancodeDados/Recebidos/$ArquivoR' style='margin-right:35px;'download=''><i class='fa-solid fa-download'></i></a>
                            </div>
                            
                            ";
                        } else {
                            echo "
                            <button style='background:none;border:none;type='button data-bs-toggle='dropdown' aria-expanded='false'>
                                <i style='color:green; margin-right:25px; font-size:1.5rem;' class='fa-solid fa-circle-check'></i>
                            </button>
                            <ul class='dropdown-menu'>
                                <li><a class='dropdown-item text-danger' href='Redirect.php?what=$id_recebido&remove=1&from=$idTurma&tarefa=$tarefa'>Remover Como Feito</a></li>
                            </ul>
                            </div>
                            ";
                        }

                    } else {
                        echo "
                            <div class='InputInicio d-flex justify-content-between' style='background-color:#ffffffbd; margin-top:15px; max-width:93%;'>
                            <a href='index.php?page=Perfil&userid=$idusuario' style='min-width:200px;' class='d-flex flex-row align-items-center justify-content-center'>
                                <img class='FotoPerfilChat ms-lg-2 ms-1' src='BancodeDados/FotosdePerfil/$fotopR' alt='$nomeUsuario'>
                                <h3 class='notdone' style='margin-left:35px;'>$nomeUsuario#$idusuario</h3>
                            </a>
                            <button style='background:none;border:none;'type='button' data-bs-toggle='dropdown' aria-expanded='false'>
                                <i style='color:red; margin-right:25px; font-size:1.5rem;' class='fa-solid fa-circle-xmark'></i>
                            </button>
                            <ul class='dropdown-menu'>
                            <li><a class='dropdown-item text-sucess'href='Redirect.php?who=$idusuario&what=$tarefa&where=$idTurma'>Marcar como Feito</a></li>
                            </ul>
                            </div>
                        ";
                    }

                    $i += 1;
                }
            } catch (PDOException $e) {
                echo "Erro: " . $e->getMessage();
            }
        }


        ?>



    </div>

</body>

</html>