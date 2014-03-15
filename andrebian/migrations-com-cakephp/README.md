Migrations no CakePHP
====================

Este post visa mostrar a utilização de migrations no CakePHP, uma ferramenta indispensável para quando o projeto está crescendo ou mesmo para o trabalho em equipe.

O que é Migrations?
----------------

Migrations é uma ferramenta que visa realizar manutenção da estrutura de banco de dados. Pense na seguinte situação: Você está desenvolvendo uma aplicação onde se faz necessário adicionar um campo novo na tabela de usuários. Como você está em sua máquina local simplesmente abre seu gerenciador de banco de dados favorito e com o comando `ALTER TABLE users ADD COLUMN status TINYINT(1) DEFAULT 1` aí atualiza seu(s) Model(s), renova seu cache em app/tmp/models e realiza todo um trabalho em cima dessa alteração do banco de dados. Ao enviar esta alteração para o ambiente de homologação ou produção, somente o código é alterado e você tem de adicionar a nova coluna na tabela users. Sem o Migrations a tarefa de atualizar a estrutura de tabelas se dá de forma manual, ou seja, você tem que entrar no server e alterar tantas quantas tabelas forem necessárias alterar para que a nova feature da aplicação funcion corretamente. Com o Migrations esta alteração torna-se nada mais que uma classe responsável por realizar tais alterações através de um simples comando que pode ser configurado juntamente com o script de deploy de sua aplicação tornando-se assim automatizada.

O que o Migrations não faz?
-----------------------
Não faz backup de seus dados. O conceito de migrations é que você espelhe sua estrutura de banco de dados da mesma forma que trata o código de sua aplicação. Em suma Migrations opera somente em DDL (Data Definition Language), para DML (Data Manipulation Language) você terá de procurar outras ferramentas.

Precisaremos de
---------------

* CakePHP em sua versão 2.4.*
* Plugin Migrations na pasta app/Plugin
* Plugins carregados CakePlugin::loadAll() em seu bootstrap
* Banco de dados Mysql
* Terminal (shell)
* Netbeans ou algum editor de sua preferência

Neste post presumo que você:
-------------------------

* Tem o CakePHP instalado, conectado ao banco de dados, configurado chaves security salt e security cypherSeed do Cake
* Possua permissão de escrita na pasta app/tmp
* Consiga rodar tranquilamente o shell do cake em app/Console/cake

Com estas "exigências" claras podemos prosseguir somente do ponto que realmente interessa, o uso do Migrations.

Instalando e configurando
--------------------

Inicialmente perceba como encontra-se nossa pasta app/Config, note que ainda não temos nem o schema.php, ele será gerado a seguir.

[imagem 1]

Rodando o comando ./app/Console/cake schema uma tela semelhante à imagem seguinte aparecerá em seu terminal. Aqui há uma lista das opções que o schema nos possibilita fazer.

[imagem 2]

Como ainda nem temos tabelas em nosso banco de dados selecionamos a opção generate. Isto lerá a estrutura do banco de dados e criará um arquivo chamado schema.php em app/Config.

[imagem 3]

Com o plugin Migrations já instalado e carregado no bootstrap se rodarmos o comando `./app/Console/cake Migrations.migration` o resultado será esta tela abaixo.

[imagem 4]

Precisamos iniciar o Migrations, fazemos isto com o comando `./app/Console/cake Migrations.migration run all -p Migrations` o resultado pode ser visto abaixo.

[imagem 5]

Perceba também como ficou o nosso banco de dados. Atualmente temos apenas a tabela schema_migrations com 3 registros que foram inseridos quando rodamos o comando anterior.

[imagem 6]


Utilizando o Migrations
----------------------

Agora de fato estaremos iniciando o uso de Migrations. Rodando o comando ./app/Console/cake Migrations.migration generate os seguintes passos serão necessários:

* Confirmação de comparação com o schema.php -- ao rodar o Migrations tentando criar uma nova migração de sua aplicação sempre será necessário comparar com a situação atual;
* Confirmação de pré visualização do arquivo que o migrations irá gerar -- é opcional, mas você pode querer ver quais alterações serão realizadas;
* Solicitação do nome que identifique esta migração -- uma mensagem curta que descreva bem o que aquela migração faz
* Confirmação de atualização do schema.php -- Isto se faz necessário para que o ambiente de homologação/produção bem como o ambiente de desenvolvimento estejam emparelhados;
* Opção de atualização do schema.php -- para que os ambientes sempre se mantenham alinhados selecione a opção **o** que irá sobrescrever o schema atual. Com isso na próxima geração de migration somente o que foi alterado entre a última atualização do schema será adicionado na nova migração. Caso você não sobrescreva, a comparação será de um tempo maior podendo duplicar tabelas e gerar erro para quem vai importar utilizando o migrations.

A imagem abaixo ilustra todos os passos descritos acima

[imagem 7]

Nova tabela no banco
----------------
Como temos nossa estrutura de banco dados vazia criaremos agora uma tabela chamada users e popularemos com o seguinte conteúdo.

    CREATE TABLE IF NOT EXISTS `users` (
      `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
      `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `email` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
      `password` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;
    
Com isso rodamos o comando `./app/Console/cake Migrations.migration generate`. Seguimos os passos descritos na seção anterior, desta vez selecionamos para ver o que será alterado em nosso banco de produção. Ao aparecer a pergunta "Do you want to preview the file before generation? (y/n)" informe **y** e perceba como as estruturas de up e down estão vazias. Cancele a operação com CTRL+C para que não seja gerada mais uma classe de migração vazia.

[imagem 8]

Isto ocorre porque o Migrations compara o schema.php com todos os migrations que existem na pasta app/Config/Migration e, tan dan, com os models de sua aplicação. Isto significa que não interessa quantas alterações você tenha realizado no banco de dados, somente o que estiver mapeado por algum Model será adicionado na migração.
Com o comando `./app/Console/cake bake all User` criamos toda e estrutura de usuários.

[imagem 9]

Agora se rodarmos novamente o comando ./app/Console/cake Migrations.migration generate e visualizarmos as alterações antes mesmo de gravar o arquivo veremos o seguinte:

[imagem 10]

Agora que percebemos que exitem alterações a serem realizadas em ambiente de homologação/produção ou mesmo para outro programador que entre no projeto basta que se definido um nome descritivo, e atualizado o schema.php como já explicado o motivo em outra seção.

[imagem 11]

Agora em seu editor veja que em app/Config/Migration existem dois arquivos, um que foi a inicialização do banco de dados sem estrutura (este arquivo terá seu *up* e *down* vazios) e um referente à criação da tabela users que terá em seu *up* toda e estrutura da tabela users e em seu *down* um hook para remoção da tabela. O hook para remoção serve para caso a migração venha a ser revertida, o banco voltar ao seu estado anterior.

[imagem 12]

Tá mas... e agora???
--------------

Até o momento vimos o migrations em ação somente de um lado, do desenvolvedor que está trabalhando ativamente no projeto, e como seria do outro lado, seja este outro lado um ambiente de homologação, produção ou mesmo outro desenvolvedor que vai iniciar os trabalhos no projeto?

Para simular este outro lado, criarei um novo projeto (clonando do git - que por sinal deixarei o fonte de todo este projeto ao final deste post) onde começarei a utilizar o migrations sendo alguém que está recebendo uma atualização.

Primeiramente com o projeto em outra "máquina" - instalarei apenas em outra pasta e conectarei com outro banco somente para fins didáticos, na prática este conceito é exatamente o mesmo. Nesta nova instalação nomearei a mesma migrations-cakephp-dev2 como se fosse um segundo desenvolvedor entrando no projeto. 

Nota: como estou fazendo tudo em máquina local, apenas copio os arquivos de uma instalção para a outra, simulando um recebimento via git pull por exemplo.

Abaixo segue uma imagem mostrando o mesmo projeto estando em outra pasta, com outra configuração de banco de dados

[imagem 13]

A seguir a estrutura do banco de dados do novo desenvolvedor

[imagem 14]


Praticando
----------

As ações a seguir podem ser executadas em ambiente de desenvolvimento por um novo desenvoldedor que possa ter adentrado no projeto, em ambiente de homologação e em ambiente de produção.

Iniciando no novo ambiente obviamente que precisamos ter os mesmos requisitos lá do início do post atendidos. Com isso, basta que rodemos o comando `./app/Console/cake Migrations.migration run all -p Migrations` para inicializar o migrations no novo ambiente.

[imagem 15]

Agora basta que rodemos o comando `./app/Console/cake Migrations.migration run all` para que a tabela users seja criada.

[imagem 16]


Agora há apenas uma última dica que eu gostaria de passar que é referente ao novo desenvolvedor realizar alguma alteração no banco de dados. Detalhe, este procedimento nunca deve ser realizado em ambiente de homologação ou produção.

O novo desenolvedor adiciona o campo status na tabela users 

[imagem 17]

E gera um novo migrations através do shell Migrations. Execute o comando ./app/Console/cake Migrations.migration generate e veja as alterações:

[imagem 18]

Perceba como em *up* há a coluna status com suas definições e em *down* a mesma é removida, novamente, o down somente é executado quando você desfaz um migration e isto não será mostrado neste post.

Basta nomear adequadamente e sobrescrever o schema.php comojá feito antes.

[imagem 19]


Agora é o momento do desenvolvedor 1 receber a nova alteração que o desenvolvedor 2 fez e gerou migration.

Perceba a estrutura do banco do desenvolvedor 1

[imagem 20]

Rodando o comando ./app/Console/cake Migrations.migration run all após ter recebido as atualizações do desenvolvedor 2, o desenvolvedor 1 aplica toda e qualquer migração disponível.

[imagem 21]

Também é possível somente rodar o comando ./app/Console/cake Migrations.migration run que uma lista com todas as migrações disponíveis aparecerá e você pode escolher para rodar uma a uma. que não estiver aplicada ainda apenas informando o ID da desejada

[imagem 22]


Este foi apenas um apanhado básico de como utilizar Migrations no CakePHP. Pode parecer astante coisa, e é, de começo é claro pois depois que a migração está configurada tudo corre muito tranquilo.



