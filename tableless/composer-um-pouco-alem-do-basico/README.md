#Composer um pouco além do básico


Este post visa explicar algumas funcionalidades mais avançadas do composer, se você ainda não conhece o composer por favor leia [composer para iniciantes][1] antes de prosseguir.


No post anterior, [composer para iniciantes][2] expliquei o que vem a ser o composer, como baixar, criar o arquivo de configurações e instalar pacotes ou bibliotecas. Agora veremos algumas questões um pouco mais avançadas sobre o uso do composer.

##Instalação global

O composer suporta instalação global para que seja utilizado apenas um "executável" para todo e qualquer projeto. Usei o termo executável pois refere-se ao composer.phar, que como explicado no post anterior é uma forma de empacotamento no PHP que transforma a aplicação toda em um único arquivo que é facilmente executável em qualquer local de seu sistema operacional.

O processo de instalação global do composer se dá das mesmas formas que a instalação já mostrada no post anterior com uma pequena diferença, selecionamos um diretório para manter o composer e quando utilizarmos, utilizamos sempre a partir deste diretório.

Mãos na massa!

> Todos os exemplos aqui criados foram realizados em ambiente Linux. No Mac OS X é semelhante e no Windows há algumas pequenas diferenças com relação à execução do PHP, com isso sugiro que leia a [documentação oficial][3] do composer para maiores detalhes. 

Instalarei o composer no diretório /opt de meu Linux, você pode selecionar o diretório de sua preferência pois funcionará da mesma forma, desde que você tenha o PHP instalado é claro.

```shell
$cd /opt && mkdir composer && cd composer 
```


O comando acima em 3 passos (separados por ***&&***). No passo 1, entro no diretório /opt. No passo 2 crio uma pasta chamada composer e no passo 3 entro na pasta *composer* recém criada.
 

> Lembrando que você deve possuir permissão de escrita no diretório que pretende instalar o composer globalmente.


Agora dentro da pasta /opt/composer basta que baixemos o composer através de uma das opções abaixo:

```shell 
curl -sS https://getcomposer.org/installer | php
```

ou

```shell
php -r "readfile('https://getcomposer.org/installer');" | php
```

Com isso dentro da pasta /opt/composer deve agora existir o arquivo *composer.phar*. Os passos descritos até aqui são ilustrados na imagem abaixo.

![enter image description here][4]

Ainda dentro de */opt/composer* rodamos o comando **php composer.phar**, é exibido o menu de ajuda do compóser indicando que foi instalado corretamente.

###Utilizando o composer global em um projeto

Criamos um projeto qualquer em um diretório de sua escolha. Farei o mesmo em meu Desktop em uma pasta chamada *composer-alem-do-basico*. Dentro desta pasta crio um arquivo chamado *composer.json* adicionando a seguinte estrutura:

```json
{
        "authors": [
            {
                "name": "Seu nome",
                "email": "seu email"
            }
        ],
        "require": {
            "php": ">=5.2.8",
        }
    } 
```

Perceba que não temos nenhum pacote de terceiro como dependência ainda, somente definimos que a versão mínima do PHP para rodarmos a aplicação é a 5.2.8, deixaremos esta versão por enquanto e adicionaremos em "require" o [ORM Doctrine][5]. Não será criado nenhum código utilizando o Doctrine, apenas está sendo incluso por ser um projeto que não possui muitas dependências fazendo a instalação ser mais rápida. Então nosso require agora fica assim:

```json
"require": {
        "php": ">=5.2.8",
        "doctrine/orm" : "2.4.*"
    }
```

Note que na versão desejada do Doctrine informei 2.4.**\***, isto significa que sempre será utilizada a versão mais recente dentro do release 2.4. Caso você queira estar sempre com a mais atual possível basta remover a numeração da versão e adicionar somente "\*", desta forma nosso require no *composer.json* tem esta estrutura:

```json
"require": {
        "php": ">=5.2.8",
        "doctrine/orm" : "*"
    }
```

Agora que está configurada nossa primeira dependência do projeto basta que rodemos o comando **php /opt/composer/composer.phar install**. Atenção ao caminho de onde está sendo rodado o composer, perceba que é a pasta que instalamos ele anteriormente. Com isso não preciso ficar para cada projeto baixando o *composer.phar*.

![\[imagem 2\]][6]

O Doctrine assim como todas as suas dependências são instaladas e temos agora esta estrutura dentro de nosso projeto:

![\[imagem 3\]][7]

###Qual a vantagem do composer com instalação global?
Apesar de muitos pensarem e economia de espaço isso é irrelevante pois o *composer.phar* "pesa" apenas 1MB aproximadamente. Há a vantagem que o composer sempre estará disponível para qualquer aplicação eliminando possíveis erros de tentar rodar o comando **php composer.phar alguma-coisa** e o composer.phar não estar presente, ou seja, basicamente a vantagem em possuir uma instalação global é você nunca esquecer de instalá-lo para cada aplicação sua.

No demais não há vantagens pois para cada aplicação o composer realizará o download de todas suas dependências individualmente, ou seja, se você possuir 3 aplicações, o composer.phar será somente 1 (na pasta /opt/composer/composer.phar) no entanto os vendors serão específicos para cada aplicação como mostra a imagem abaixo:

![\[imagem 4\]][8]


##Direcionamento de vendors

Por padrão o composer entende que as bibliotecas de terceiros devem ficar dentro do diretório *vendor* mas é possível alterar. Pense em uma situação em que você está trabalhando com algum framework que fornece uma estrutura de diferente da estabelecida pelo composer, o CakePHP por exemplo, por padrão neste framework as bibliotecas de terceiros são instaladas em *vendors* (no plural mesmo).

Isto é facilmente configurado através do arquivo *composer.json*. Decalararei que minhas bibliotecas de terceiros serão acondicionadas em 3rdparty apenas para fins didáticos. Para direcionar os vendors do composer precisamos adicionar a informação de onde nossos vendors serão acondicionados:

```json
    ....
    "require": {
        "php": ">=5.2.8",
        "doctrine/orm": "*"
    },
    "config": {
        "vendor-dir": "3rdparty"
    }
    .....
```

Feito isto basta rodar o comando **php /opt/composer/composer.phar install** caso não tenha instalado ainda ou **php /opt/composer/composer.phar update** caso já tenha realizado alguma instalação anteriormente. 

> Caso você execute o update do composer ( ... composer.phar update) e alterou a pasta de vendors, esteja ciente de que a pasta que existia antes permanecerá em sua aplicação e você terá de removê-la manualmente pois o composer perdeu a referência da mesma a partir do momento que você alterou o "vendor-dir".

A nova estrutura de nossa aplicação será esta:

![\[imagem 5\]][9]


##Direcionando pacotes

Cada pacote que você define como uma dependência de sua aplicação possui uma série de configurações e podem conter dependências também que são listadas em seus composer.json. Ou seja, cada pacote possui (comumente) dentro dele um json informando do que eles dependem, se são plugins de algum framework ou CMS entre outras configurações.

Um bom exemplo de plugins que são instalados em seus diretórios corretos são os plugins do wordpress, desde que você informe que estará utilizando os instaladores do composer `"composer/installers": "*"`. Caso você não esteja utilizando os instaladores do composer pode simplesmente direcionar cada pacote para onde bem entender.

```json
    ....
    "require": {
        "php": ">=5.2.8",
        "doctrine/orm": "*",
        "josegonzalez/cakephp-upload": "*"
    },
    "extra" : {
    	"installer-paths" : {
    	    "plugins/Upload" : ["josegonzalez/cakephp-upload"]
    	}
    },
    ....
```

Mesmo que você esteja utilizando os instaladores ainda sim pode personalizar pacote por pacote onde quer que eles sejam instalados dentro de sua aplicação.

No novo exemplo do composer estou informando que o plugin de upload do CakePHP será instalado na pasta *plugins/Upload* ao invés de *app/Plugin/Upload* como seria instalado pelo CakePHPInstaller do composer.

![\[imagem 6\]][10]

Como você pode ver, é possível personalizar a instalação cada pacote com simples configuração através de nosso arquivo *composer.json*.


##Require ou require-dev?

O composer trabalha basicamente com dois tipos de dependências, os *require* que são os estritamente necessários para o funcionamento da aplicação e os *require-dev* que são dependências utilizadas em ambiente de desenvolvimento, são elas ferramentas como PHPUnit, ferramentas de log, entre outras. No exemplo abaixo informamos que para nossa aplicação utilizaremos o ORM Doctrine e para o ambiente de desenvolvimento somente utilizaremos o PHPUnit.

```json
    "require": {
        "php": ">=5.2.8",
        "doctrine/orm": "*",
        "josegonzalez/cakephp-upload": "*"
    },
    "require-dev" : {
	    "phpunit/phpunit" : "4.0.*"
    },
```

Com a configuração distinta podemos instalar no ambiente de produção somente as dependências necessárias para o funcionamento correto da aplicação deixando de lado as dependências de desenvolvimento.

Como já realizamos a primeira instalação através do composer agora apenas utilizamos o comando **php /opt/composer/composer.phar update** (se for a instalação global do composer) ou **php composer.phar update** (se o composer.phar foi baixado na raiz de sua aplicação). Com este comando todos os pacotes serão instalados.

![\[imagem 7\]][11]

Após a atualização (que baixará muitos pacotes) a nova estrutura de nosso projeto é a seguinte.

![\[imagem 8\]][12]

Pensemos agora que estamos no ambiente de produção ou homologação onde não se faz necessário o PHPUnit. Não é necessária a remoção do mesmo no arquivo *composer.json* e sim rodarmos o comando **php composer.phar update --no-dev** e o resultado será como na imagem abaixo.

![\[imagem 9\]][13]

Note apenas que na imagem acima eu rodei o update em meu ambiente de desenvolvimento apenas excluindo os pacotes de modo *dev* para ilustrar o funcionamento. Na imagem também é possível perceber que o PHPUnit e suas dependências que já estavam instalados foram removidos por não serem mais necessários em modo produção. Na imagem abaixo está a nova estrutura de nossa aplicação.

![\[imagem 6\]][14]


##Definição de Autoload


Para quem não conhece existe a [FIG][15] (Framework Interop Group) que visa sugerir padrões de desenvolvimento através de suas PSRs. Atualmente são 4 recomendações sendo a primeira delas ([PSR-0][16]) a que trata de como ocarregamento de sua aplicação deve ocorrer. Basicamente é a informação de onde será definido o namespace de sua aplicação. 

```javascript
"autoload" : {
    "psr-0": {
        "Tableless\\": "src/"
    }
}
```

Note que foi definido o namespace _Tableless_ indicando que o mesmo está dentro da pasta _src_. O nome da pasta pode ser outro qualquer e não somente _src_.

Com isso a nova estrutura de nossa aplicação é esta

![\[imagem 21\]][17]

Para utilizar todo o projeto entitulado como Tableless neste exemplo basta informá-lo onde o mesmo se faça necessário:

```php
use Tableless;
```


##Criando um pacote do composer

Pra finalizar criaremos um pacote do composer. Primeiramente você precisa ter uma conta no [github][18] ou [bitbucket][19] (trabalharemos apenas com versionamento em git). Também será necessária uma conta no [Packagist][20].

Tendo os requisitos atendidos agora deve ser criado um repositório no github, se você não sabe criar ou não utilizou o github ainda leia [este tutorial][21].

Feito isto é hora de clonar o repositório em uma pasta de sua preferência, utilize o comando **git clone git@github.com:username/repo-name.git** no meu caso é: git clone git@github.com:andrebian/exemplo-composer-tableless.git. Na imagem abaixo é possível ver o git realizando o clone e a estrutura inicial do projeto que contém além dos arquivos do git somente o arquivo README.md que foi criado juntamente com a criação do repositório no github.

![\[imagem 10\]][22]

Agora temos de criar nosso arquivo *composer.json* para que sejam adicionadas as informações de nosso novo pacote. Sua estrutura é a seguinte.

```json
    {
        "name": "andrebian/exemplo-composer-tableless",
        "description": "Este pacote foi criado apenas para complementar o post no Tableless",
        "authors": [
            {
                "name": "Andre Cardoso",
                "email": "andrecardosodev@gmail.com"
            }
        ],
        "require": {
            "php": ">=5.3.17",
            "kevinlebrun/slug.php": "1.*"
        }
    }
```

> A chave "name" deve possuir o vendor (seu username) e o slug do nome
> do projeto.

Note que adicionei uma dependência ao meu projeto, com isso mesmo se o pacote slug.php não estiver setado no composer que engloba toda a aplicação será instalado porque eu informei que meu pacote precisa dele para funcionar corretamente.

Feito isto basta que as alterações realizadas sejam enviadas ao github e podemos prosseguir com a criação do pacote no packagist. Não vou explicar o funcionamento do git (commit, pull, push e outros) pois o foco deste post é o composer. Se você ainda não conhece o git sujiro a leitura de [Iniciando no git][23] que foi escrito pelo Diego Eis e está divido em duas partes que lhe mostram conceitos e utilização do mesmo. A imagem abaixo mostra o repositório no github já com a nova estrutura contendo o *composer.json*.

![\[imagem 11\]][24]

Agora que já temos nosso repositório no github basta criarmos nosso pacote no packagist. Acessando https://packagist.org/ e estando logado clique em "Submit package".

![\[imagem 12\]][25]

Informe a URL em que o mesmo se encontra, neste caso https://github.com/andrebian/exemplo-composer-tableless e clique em Check

![\[imagem 13\]][26]

Após a verificação e confirmação de que está tudo ok basta clicar em Submit

![\[imagem 14\]][27]

Na imagem seguinte você pode ver que o pacote foi criado com sucesso e já está disponível para ser adicionado como dependência em qualquer projeto que você desejar.

![\[imagem 15\]][28]

Note apenas que há uma chamada de atenção ali informando que o pacote não é atualizável automaticamente, vamos corrigir isto agora.

Acessando sua conta no github navegue pelos seus repositórios até encontrar o desejado e entre em suas configurações.

![\[imagem 16\]][29]

À esquerda há um menu com algumas opções, clique em **Webhooks & Services** e em seguida em configurar serviços.

![\[imagem 17\]][30]

Role a tela até localizar o serviço **Packagist** e clique no mesmo. Uma nova tela será aberta solicitando os dados de sua conta. Forneça "user" e "token", o "domain" é opcional, em seguida marque a opção "Active" e clique em Update Settings. 

![\[imagem 18\]][31]

Para obter o token, vá até sua conta no Packagist e clique em "Show API Token".

![\[imagem 19\]][32]

Após confirmado o user e token nas configurações de webhooks do github, acesse novamente Webhooks & Services, vá novamente até Packagist e perceba que agora existe um botão de teste para confirmar que o serviço foi habilitado com sucesso, clique sobre o mesmo e certifique-se de que uma mensagem de sucesso foi retornarda.

![\[imagem 20\]][33]

Quase lá, agora falta somente acessarmos nosso pacote no composer para certificar que a mensagem de que o mesmo não é atualizado automaticamente não aparece mais.

![\[imagem 21\]][34]

Prontinho! Tudo funcionando perfeitamente. Agora sempre que você der um push no github o pacote do composer é atualizado automaticamente.


##Concluindo

Como você pode ver o composer é muito versátil, pode (e deve preferencialmente) ser utilizado em todo e qualquer projeto em PHP. Obviamente que existem configurações mais avançadas no entanto elas não vem ao caso neste momento por serem muito específicas de cada projeto/pacote. A ideia deste post era fornecer um pouco mais de informações sobre a utilização do composer que foi iniciada no post anterior [Composer para iniciantes][35] para maiores informações a documentação oficial sempre será a melhor fonte.


  [1]: http://tableless.com.br/composer-para-iniciantes/
  [2]: http://tableless.com.br/composer-para-iniciantes/
  [3]: https://getcomposer.org/doc/
  [4]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/01-download-do-composer.jpg
  [5]: http://www.doctrine-project.org/
  [6]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/02-instalando-primeiras-dependencias.jpg
  [7]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/03-nova-estrutura-vendor.jpg
  [8]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/04-dois-projetos-com-vendors-distintos.jpg
  [9]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/05-nova-estrutura-vendor-path.jpg
  [10]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/06-pacote-em-diretorio-personalizado.jpg
  [11]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/07-atualizando-dev.jpg
  [12]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/08-estrutura-com-dev.jpg
  [13]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/09-update-sem-dev.jpg
  [14]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/06-pacote-em-diretorio-personalizado.jpg
  [15]: http://www.php-fig.org/
  [16]: http://www.php-fig.org/psr/psr-0/
  [17]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/21-namespace.jpg
  [18]: https://github.com/
  [19]: https://bitbucket.org/
  [20]: https://packagist.org/
  [21]: https://help.github.com/articles/create-a-repo
  [22]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/10-clonando-novo-projeto.jpg
  [23]: http://tableless.com.br/iniciando-no-git-parte-1/
  [24]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/11-projeto-no-github.jpg
  [25]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/12-submit-package.jpg
  [26]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/13-package-url.jpg
  [27]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/14-confirm-submit.jpg
  [28]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/15-package-created.jpg
  [29]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/16-settings.jpg
  [30]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/17-webhooks.jpg
  [31]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/18-packagist-token.jpg
  [32]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/19-show-api-token.jpg
  [33]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/20-confirm-service.jpg
  [34]: https://raw.githubusercontent.com/andrebian/posts/master/tableless/composer-um-pouco-alem-do-basico/images/20-success.jpg
  [35]: http://tableless.com.br/composer-para-iniciantes/