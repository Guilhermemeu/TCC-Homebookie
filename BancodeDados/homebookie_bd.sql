create database homebookie_bd;
use homebookie_bd;


CREATE TABLE usuario (
  idusuario int NOT NULL AUTO_INCREMENT,
  email varchar(100) NOT NULL,
  senha varchar(255) NOT NULL,
  nome varchar(100) NOT NULL DEFAULT 'Usuario',
  fotop varchar(200) NOT NULL DEFAULT 'FotoP.png',
  descricao varchar(500) NOT NULL,
  participa varchar(500),
  ativada boolean DEFAULT 0,

  PRIMARY KEY (idusuario)
) ;
desc usuario;

create table vcodes (
idcode int not null auto_increment,
code varchar(32),
idneeder int not null,
used boolean DEFAULT 0,


primary key (idcode)
);
desc vcodes;

/* TURMA tabela PAI*/
create table turma (
idturma int not null auto_increment,
nome varchar(100),
administrador int not null ,
materia varchar(100),
descricao varchar(500),
Participantes varchar(300),
imagem varchar(255) DEFAULT '',
primary key (idturma)
);
desc turma;


/*TAREFAS, CHAT, CONTEUDOS são filhas da table TURMAS*/

create table tarefas(
idtarefas int primary key NOT NULL AUTO_INCREMENT,
turma_id int,
data_tarefa date,
nome_tarefa varchar(100),
descricao_tarefa varchar(500),
Tarefa boolean DEFAULT 1,
Arquivo varchar(200),

foreign key (turma_id) references turma(idturma)
);
desc tarefas;


create table recebidos(
id_recebido int primary key NOT NULL AUTO_INCREMENT,
id_sender int,
id_turma int,
id_tarefa int,
mensagem varchar(100),
entrega varchar(500),

foreign key (id_sender) references usuario(idusuario),
foreign key (id_turma) references turma(idturma),
foreign key (id_tarefa) references tarefas(idtarefas)
);
desc recebidos;



create table mensagens( 
id_mensagem int AUTO_INCREMENT key,
mensagem text,
data_mensagem date,
from_usuario int,
to_chat int,
foreign key (from_usuario) references usuario(idusuario),
foreign key (to_chat) references turma(idturma)
);
desc mensagens;

create table mensagemtarefas( 
id_mensagem int AUTO_INCREMENT key,
mensagem text,
data_mensagem date,
from_usuario int,
to_tarefa int,
and_user int,
foreign key (from_usuario) references usuario(idusuario),
foreign key (to_tarefa) references tarefas(idtarefas)
);
desc mensagemtarefas;


create table conteudos(
idconteudos int primary key AUTO_INCREMENT,
turma_id int,
data_conteudos date,
postagens text,
arquivos_anx varchar (200),
foreign key (turma_id) references turma(idturma)
);
desc conteudos;
