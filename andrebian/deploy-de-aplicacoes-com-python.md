#Deploy de aplicações com python (fabric)

Recentemente li o post do Elton Minetto sobre deploy automatizado utilizando o git. Não vou entrar em mais detalhes, caso queira pode conferir [aqui][1].

Muito bem, pretendo apresentar outra forma de deploy de aplicações (neste caso, web) de forma automatizada. Pensando um pouco diferente da indicação do Minetto, este post visa “automatizar” sem automatizar. Confuso? Deixa eu explicar. A forma como o post referenciado realiza o deploy é a cada push, ou seja, você está trabalhando em seu código, dá seu commit e o push. Neste momento o mesmo executa o deploy (pull) no servidor de produção. A forma de trabalho que quero apresentar pensa um pouco diferente. O deploy somente é executado quando você tem a certeza de que o quer.

Partindo deste princípio percebe-se que podem haver vantagens e desvantagens na forma de deploy que apresentarei. Elas serão listados ao final do post porque agora vamos botar a mão na massa.

 

###Do que precisamos

* Computador com Linux (não será realizado este procedimento no Windows) e acesso a root
* Python
* Fabric
* Acesso via SSH no servidor de produção
* Dois pares de chaves SSH (Um em seu PC e outro no servidor de produção)
* Sua conta e um projeto no github ou bitbucket já inicializados tanto em sua máquina como no servidor de produção
 

###Instalação

Primeiramente logado como root em seu terminal precisamos instalar o python (versão mais recente disponível para sua distribuição). Este post foi baseado na instalação do Ubuntu que é meu desktop atual (vergonha) mas pode ser realizado em qualquer Debian-like.

Python

    #apt-get install python-dev python-setuptools

 

Fabric

     #easy_install fabric

 

Pronto, a partir de agora basta criarmos o arquivo em que será definido nosso deploy.

 

###Criando o script de deploy

Crie um arquivo chamado fabfile.py na pasta de sua preferência da aplicação. Costumo deixar na raiz mas nada impede que você deixe em outra pasta, até mesmo fora de sua aplicação. Este arquivo deve iniciar informando que é um interpretável pelo python, preferencialmente setando o charset como utf-8 e importando as bibliotecas do fabric.

 

    #!/usr/bin/env python
    # -*- coding: utf-8 -*-
    
    from fabric.api import *

 

Agora informamos algumas configurações de ambiente necessárias para o deploy

    env.hosts = ['user@host'] 
    env.path = '/home/user_path/public_html' 
    
    env.port = '22'
    # Opcional. Se a porta SSH do server for a 22 a definição acima não se faz necessária 

Ok, até o momento temos o python, fabric, já definimos nosso ambiente (de produção) então agora vamos definir o que será executado ao rodarmos nosso deploy.

 

    # Criado originalmente po Arthur Furlan - http://arthurfurlan.org/
    
    def _git_pull(path, user, remote='origin', branch='master'):
        '''Executa "git pull" no repositório da sua aplicação '''
        run('su %s -c "cd %s; git pull %s %s"' % (user,path,remote,branch))

 
Como já descrito no comentário, tal definição entra no repositório de produção como o usuário especificado, executa o comando git pull de origin master.

Agora basta que adicionemos a chamada à tal definição

    # Criado originalmente por Arthur Furlan - http://arthurfurlan.org/
    
    def deploy(version=None):
        '''Executa o deploy no ambiente de produção '''
        _git_pull(env.path,'username')

Prontinho, seu script de deploy deve ficar semelhante à este : https://gist.github.com/andrebian/7441399

 

Legal agora já estamos com o python e fabric instalados e nosso script criado com o pull em ambiente de produção. Neste exemplo simples apenas executamos via shell o git pull origin master dentro do repositório de nossa aplicação. Outras necessidades também são atendidas com este script. Um bom exemplo disto é o Migrations do CakePHP, se quisessemos rodar o migrations, assim como o _git_pull, criaríamos um _migrations para tal apenas mandando executar  ’$./cake Migrations.migration run all‘ do Cake.

 

Mas acalme-se, ainda não está tudo pronto. Temos ainda o trabalho de configurarmos as chaves SSH em nosso computador. Isto faz-se necessário para que você não tenha que informar sua senha toda vez que quiser realizar o deploy.

 

###Chaves SSH

A criação de chaves SSH no Linux é muito simples, basta o comando $ssh-keygen -t rsa em seu terminal. Detalhe que esta chave deve ser gerada como o SEU usuário e não o root. Por default sempre deixo minhas chaves em /home/[user]/.ssh ( /home/andre/.ssh no meu caso) e não defino senha para as mesmas. Se preferir deixar sem senha também apenas confirme a pasta em que as chaves serão geradas e quando solicitar a senha e sua confirmação apenas deixe em branco e tecle enter. Prontinho, agora você já tem seu par de chaves SSH, uma pública e outra privada. A pública é a chave que você largará ao mundo, todos os serviços que precisam de chaves SSH devem ser configurados com esta, já a privada é SUA, não pode sair de sua máquina de forma alguma. Somente a combinação chave pública-privada é que garantem que o conteúdo criptografado será decirado entre os hosts. Caso já possua seu par de chaves SSH ignore este parágrafo.

Ainda temos o trabalho de adicionar nossa chave pública como uma chave autorizada em nosso servidor de produção. Para isto basta rodar o comando abaixo

`$scp ~/.ssh/id_rsa.pub user@remote.server:/home/user_path/.ssh/authorized_keys` 
informe sua senha (pela última vez).

Agora sim, ao logar no servidor de produção via SSH simplesmente nenhuma senha mais será exigida a partir de seu computador.

 

###Finalmente

Agora que configuramos tudo basta entrarmos na pasta onde o arquivo fabfile.py foi criado, rodar o comando `$fab deploy` e aguardar.

 

![deploy-em-acao][2]

 

###Agora chegamos ao ponto que interessa vantagens e desvantagens.

####Começando com os pontos negativos:

Inicialmente tende a parecer difícil de implementar esta forma de deploy para alguns por ter de instalar python e fabric  sem contar que no Windows essa simples tarefa que no Linux se resolve com um simples apt-get install não é tão simples assim, as vezes requerendo horas de tentativas e fracassos até conseguir (e se conseguir) instalar o fabric no mesmo;

O deploy não é de fato automatizado, e sim é um facilitador que deve ser executado manualmente ou configurado através de hooks em sua máquina para ser executado quando existir um push para o repositório no github ou bitbucket, tornando-o assim de fato automatizado.
 

####Agora sim os pontos positivos

Após o primeiro script de deploy criado, torna-se muito tranquilo replicar o mesmo para quantas aplicações forem necessárias;

Você no controle: Um dos pontos que inclusive o Arthur Furlan argumentou comigo na época em que prestava consultoria na empresa em que trabalho é que ao contrário de scripts que ficam “ouvindo” seus pushs e executando tarefas a toda e qualquer alteração sua, com esta forma de deploy você está no controle. Se algo ainda não está a ponto de ir para o ambiente de produção não irá ao você dar um push. Isto cai muito bem quando se trabalha em equipes de vários desenvolvedores com branches distintos e/ou com commits muito pequenos (que é como gosto de trabalhar).
 

###Finalizando

Como mencionado esta forma de deploy foi apresentada a mim no início do ano de 2013 pelo Arthur Furlan e a uso desde então. Esta mesma forma de deploy (obviamente que com muito mais recursos) é fornecida aos contratantes  da [Configr][3] (sem querer fazer propaganda, apenas dando provas de que ela funciona e muito bem).


  [1]: http://eltonminetto.net/blog/2013/11/11/deploy-estilo-heroku-usando-git/
  [2]: http://www.andrebian.com/wp-content/uploads/2013/11/deploy-em-acao.png
  [3]: https://configr.com/ 
