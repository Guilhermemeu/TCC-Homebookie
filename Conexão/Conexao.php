<?php

// define as informaçoes de conexao com o BD Msql
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "homebookie_bd";

try {

	// Criando a conexao com Mysql e instanciando a classe PDO
	$conn = new PDO(dsn: "mysql:host=$servername;dbname=$dbname", username: $username, password: $password);

	//trata erros e excessoes
	$conn->setAttribute(attribute: PDO::ATTR_ERRMODE, value: PDO::ERRMODE_EXCEPTION);

	//echo "Conexao com sucesso";

// Exibindo erro caso ocorra

} catch (PDOException $erro) {
	echo "Falha na conexao: " . $erro->getMessage();

}
