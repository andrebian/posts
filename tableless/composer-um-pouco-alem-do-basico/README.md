#Composer um pouco além do básico


Este post visa explicar algumas funcionalidades mais avançadas do composer, se você ainda não conhece o composer por favor leia [composer para iniciantes][1] antes de prosseguir.


--------------------------------------
##Apenas uma cola
###O que será abordado?
* Instalação global
* direcionamento de vendor
* Direcionamento de elementos avulsos
* Diferenças entre require e require-dev
* Criação de seu pacote do composer

----------------------------------

No post anterior, [composer para iniciantes][2] expliquei o que vem a ser o composer, como baixar, criar o arquivo de configurações e instalar pacotes ou bibliotecas. Agora veremos algumas questões um pouco mais avançadas sobre o uso do composer.

##Instalação global

O composer suporta instalação global para que seja utilizado apenas um "executável" para todo e qualquer projeto. Usei o termo executável pois refere-se ao composer.phar, que como explicado no post anterior é uma forma de empacotamento no PHP que transforma a aplicação toda em um único arquivo que é facilmente executável em qualquer local de seu sistema operacional.

O processo de instalação global do composer se dá das mesmas formas que a instalação já mostrada no post anterior com uma pequena diferença, selecionamos um diretório para manter o composer e quando utilizarmos, utilizamos sempre a partir deste diretório.

Mãos na massa!

> Todos os exemplos aqui criados foram realizados em ambiente Linux. No Mac OS X é semelhante e no Windows há algumas pequenas diferenças com relação à execução do PHP, com isso sugiro que leia a [documentação oficial][3] do composer para maiores detalhes. 

Instalarei o composer no diretório /opt de meu Linux, você pode selecionar o diretório de sua preferência pois funcionará da mesma forma, desde que você tenha o PHP instalado é claro.

    $ cd /opt && mkdir composer && cd composer

O comando acima em 3 passos (separados por ***&&***). No passo 1, entro no diretório /opt. No passo 2 crio uma pasta chamada composer e no passo 3 entro na pasta *composer* recém criada.
 

> Lembrando que você deve possuir permissão de escrita no diretório que pretende instalar o composer globalmente.


Agora dentro da pasta /opt/composer basta que baixemos o composer através de uma das opções abaixo:

    curl -sS https://getcomposer.org/installer | php

ou

    php -r "readfile('https://getcomposer.org/installer');" | php

Com isso dentro da pasta /opt/composer deve agora existir o arquivo *composer.phar*. Os passos descritos até aqui são ilustrados na imagem abaixo.

[imagem 1]

Ainda dentro de */opt/composer* rodamos o comando **php composer.phar**, é exibido o menu de ajuda do compóser indicando que foi instalado corretamente.

###Utilizando o composer global em um projeto

Criamos um projeto qualquer em um diretório de sua escolha. Farei o mesmo em meu Desktop em uma pasta chamada *composer-alem-do-basico*. Dentro desta pasta crio um arquivo chamado *composer.json* adicionando a seguinte estrutura:

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

Perceba que não temos nenhum pacote de terceiro como dependência ainda, somente definimos que a versão mínima do PHP para rodarmos a aplicação é a 5.2.8, deixaremos esta versão por enquanto e adicionaremos em "require" o [ORM Doctrine][4]. Não será criado nenhum código utilizando o Doctrine, apenas está sendo incluso por ser um projeto que não possui muitas dependências fazendo a instalação ser mais rápida. Então nosso require agora fica assim:

    "require": {
        "php": ">=5.2.8",
        "doctrine/orm" : "2.4.*"
    }

Note que na versão desejada do Doctrine informei 2.4.**** ***, isto significa que sempre será utilizada a versão mais recente dentro do release 2.4. Caso você queira estar sempre com a mais atual possível basta remover a numeração da versão e adicionar somente "*" *, desta forma nosso require no *composer.json* tem esta estrutura:

    "require": {
        "php": ">=5.2.8",
        "doctrine/orm" : "*"
    }

Agora que está configurada nossa primeira dependência do projeto basta que rodemos o comando **php /opt/composer/composer.phar install**. Atenção ao caminho de onde está sendo rodado o composer, perceba que é a pasta que instalamos ele anteriormente. Com isso não preciso ficar para cada projeto baixando o *composer.phar*.

[imagem 2]

O Doctrine assim como todas as suas dependências são instaladas e temos agora esta estrutura dentro de nosso projeto:

[imagem 3]

###Qual a vantagem do composer com instalação global?
Apesar de muitos pensarem e economia de espaço isso é irrelevante pois o *composer.phar* "pesa" apenas 1MB aproximadamente. Há a vantagem que o composer sempre estará disponível para qualquer aplicação eliminando possíveis erros de tentar rodar o comando **php composer.phar alguma-coisa** e o composer.phar não estar presente, ou seja, basicamente a vantagem em possuir uma instalação global é você nunca esquecer de instalá-lo para cada aplicação sua.

No demais não há vantagens pois para cada aplicação o composer realizará o download de todas suas dependências individualmente para cada aplicação, ou seja, se você possuir 3 aplicações, o composer.phar será somente 1 (na pasta /opt/composer/composer.phar) no entanto os vendors serão específicos para cada aplicação como mostra a imagem abaixo:

[imagem 4]


##Direcionamento de vendors

Por padrão o composer entende que as bibliotecas de terceiros devem ficar dentro do diretório *vendor* mas é possível alterar. Pense em uma situação em que você está trabalhando com algum framework que fornece uma estrutura de diferente da estabelecida pelo composer, o CakePHP por exemplo, por padrão neste framework as bibliotecas de terceiros são instaladas em *vendors* (no plural mesmo).

Isto é facilmente configurado através do arquivo *composer.json*. Decalararei que minhas bibliotecas de terceiros serão acondicionadas em 3rdparty apenas para fins didáticos. Para direcionar os vendors do composer precisamos adicionar a informação de onde nossos vendors serão acondicionados:

    ....
    "require": {
        "php": ">=5.2.8",
        "doctrine/orm": "*"
    },
    "config": {
        "vendor-dir": "3rdparty"
    }
    .....

Feito isto basta rodar o comando **php /opt/composer/composer.phar install** caso não tenha instalado ainda ou **php /opt/composer/composer.phar update** caso já tenha realizado alguma instalação anteriormente. 

> Caso você execute o update do composer ( ... composer.phar update) e alterou a pasta de vendors, esteja ciente de que a pasta que existia antes permanecerá em sua aplicação e você terá de removê-la manualmente pois o composer perdeu a referência da mesma a partir do momento que você alterou o "vendor-dir".

A nova estrutura de nossa aplicação será esta:

[imagem 5]


##Direcionando elementos

Cada pacote que você define como uma dependência de sua aplicação possui uma série de configurações e podem conter dependências também que são listadas em seus composer.json. Ou seja, cada pacote possui (comumente) dentro dele um json informando do que eles dependem, se são plugins de algum framework ou CMS entre outras configurações.

Um bom exemplo de plugins que são instalados em seus diretórios corretos são os plugins do wordpress, desde que você informe que estará utilizando os instaladores do composer `"composer/installers": "*"`. Caso você não esteja utilizando os instaladores do composer pode simplesmente direcionar cada pacote para onde bem entender.

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

Mesmo que você esteja utilizando os instaladores ainda sim pode personalizar pacote por pacote onde quer que eles sejam instalados dentro de sua aplicação.

No novo exemplo do composer estou informando que o plugin de upload do CakePHP será instalado na pasta *plugins/Upload* ao invés de *app/Plugin/Upload* como seria instalado pelo CakePHPInstaller do composer.

[imagem 6]


  [1]: http://tableless.com.br/composer-para-iniciantes/
  [2]: http://tableless.com.br/composer-para-iniciantes/
  [3]: https://getcomposer.org/doc/
  [4]: http://www.doctrine-project.org/