#PHPUnit - como iniciar sem dores

Como já mencionei em um artigo anterior, o PHPUnit é um framework de testes unitários para a linguagem PHP. Ele provê um ecossistema para a execução de testes de forma automatizada.

Neste artigo veremos a sua instalação utilizando o gerenciador de pacotes composer, configuração e estrutura de pastas e alguns testes simples sem persistência de dados.

###Instalando o PHPUnit
Para iniciar a instalação do PHPUnit precisamos primeiramente de um diretório que será nosso diretório de trabalho neste exemplo. Após criado o diretório é necessário criar um arquivo chamado composer.json para que seja definida a necessidade do PHP Unit no projeto. O arquivo composer.json é responsável por declarar todas as bibliotecas que serão necessárias para o projeto em questão, em suma todas soluções de terceiros, incluindo suas soluções genéricas que encontrem-se no repositório do composer serão gerenciadas conforme a especificação do arquivo composer.json.

O arquivo para este artigo deverá conter o seguinte conteúdo:

    {
        "require-dev": {
            "phpunit/phpunit": "3.7.*"
        }
    }

Isto quer dizer que estamos registrando como uma dependência de nosso projeto o PHPUnit em sua versão 3.7 sempre solicitando a última atualização. Para que sempre seja utilizada a última versão do PHPUnit basta remover a sequência **3.7.**** por simplesmente ******. O mesmo é possível com qualquer biblioteca gerenciada pelo composer.

Agora já estão prontas as declarações de nossas dependências basta baixar o gerenciador de dependência composer e rodar o comando

    php composer.phar install.

Isto irá de maneira automática baixar todas as dependências que foram especificadas no arquivo composer.json, e neste exemplo trata-se apenas do PHPUnit no entanto o próprio PHP Unit requer algumas bibliotecas de terceiros então outras bibliotecas estarão disponíveis além do mesmo dentro da pasta vendor que será criada.

![enter image description here][1]
Instalação a partir do composer

![enter image description here][2]
Estrutura de pastas

> Existe uma convenção de padrões definidos pela FIG chamada PSR
> (Proposal Standards Recommendation). Para facilitar será utilizada a
> definição do Autoloader para o exemplo que está descrito na PSR-0.
> Após a correta instalação via composer devem ser criadas os diretórios
> src e dentro dele Application.

Com a definição do Autoloader a nova estrutura do composer é a seguinte:

    {
        "autoload": {
            "psr-0": {"Application\\": "src/"}
        },
        "require-dev": {
            "phpunit/phpunit": "3.7.*"
        }
    }

 
No arquivo composer.json agora é dito que o autoloader deve reconhecer o namespace “Application” que encontra-se dentro do diretorio src.
 
![enter image description here][3]
Nova estrutura de pastas

###Iniciando com um simples teste

Como o PHPUnit já está instalado corretamente no projeto agora vem a parte legal que é criar pequenos testes (unitários, obviamente) e colocar em prática o vermelho-verde-refatora já mencionado no meu post anterior [TDD, por que usar?][4].
Primeiramente deve ser criada a pasta tests que servirá para acomodar todos os casos de teste a serem executados.
Começando com um teste simples, e na verdade este artigo somente mostrará o uso simplificado pois a finalidade do mesmo é apenas mostrar o caminho das pedras, como começar, instalar, configurar e rodar os primeiros testes. A partir daí cabe à necessidade de cada desenvolvedor.
Aqui será criado um arquivo PHPNativeElements onde serão testados algumas funções nativas do PHP e seus comportamentos. Obviamente que este caso de teste calha somente em modo didático pois tais testes e classe testada terá muito mais de uma única responsabilidade, é somente em caráter demonstrativo.

Criado o arquivo PHPNativeElementsTest.php dentro do diretório tests, siga o exemplo abaixo.

![Estrutura inicial do primeiro teste][5]
Estrutura inicial do primeiro teste

Para que seja reconhecido como um teste o arquivo deve conter a sufixo Test.

###Executando de forma simples

Como o PHPUnit foi instalado a partir do composer, é a partir da estrutura montada pelo mesmo que este será executado digitando no terminal

    ./vendor/bin/phpunit

Com isto uma tela de ajuda deve aparecer com todas as opções disponíveis para a utilização do PHPUnit. Seguem as definições do comando que será executado neste primeiro momento.

    ./vendor/bin/phpunit --colors --debug tests/PHPNativeElements 

onde:
`./vendor/bin/phpunit`: o próprio executável do PHPUnit
`--colors`: habilita coloração ( assim podemos ver os estágios vermelho-verde de forma mais simples)
`--debug`: habilita o modo debug para detalhamento das ações que estão sendo tomadas durante os testes – Esta ação serve como ótima documentação como já mencionado em meu artigo anterior.
`tests/PHPNativeElements`: o nome da classe de testes a ser testada.

Ao rodarmos o comando acima a mensagem resultante deverá ser a de que não há testes disponíveis na classe testada.

![enter image description here][6]
Informação de que ainda não há testes
 
###Fazendo o primeiro teste passar

O TDD define que o desenvolvimento deve ser orientado a testes, com isso, criaremos primeiramente a expectativa na nossa classe de testes e em seguida a implementação no código de produção.

Após o método tearDown que já encontra-se na classe PHPNativeElementsTest crie um método chamado testOperacaoMatematica. Assim como a classe de teste possui uma convenção com os métodos também é necessário especificar qual trata-se de um teste a partir do prefixo test. Por este motivo nosso primeiro caso de teste se chamar testOperacaoMatematica. Caso não contenha o prefixo test e, não sendo os métodos setUp e tearDown, o PHPUnit simplesmente não executa o método.

Como estamos utilizando o Autoloader, em nossa classe de teste usaremos o namespace “Application\NativeElements\Math” para carregar a nossa classe que será testada a partir da classe de testes. Como atributo de nossa classe de teste adicionaremos “$math” e nele instanciaremos a classe Application\NativeElements\Math dentro do método setUp.

![enter image description here][7]
Nova estrutura da classe de teste

Ao rodarmos novamente o PHPUnit o teste simplesmente quebra. Isto porque a classe Application\NativeElements\Math ainda não existe. Este é o próximo passo, o código que fará o testes passar.

![enter image description here][8]
Quebra do teste por não existir a classe testada
Criamos o arquivo Math.php dentro do diretório Application/NativeElements e no mesmo a classe Math definindo como namespace Application\NativeElements. Por hora nenhum método é criado nesta nova classe.

![enter image description here][9]
Classe de produção, nela os problemas criados nos testes serão solucionados
Rodando nosso teste novamente ele quebra mais uma vez. 

Agora o que está faltando é o método testado ( sum ).


![enter image description here][10]
Faltando método sum


Ao criar o método sum e sua lógica estando correta o teste atual passará, então passamos do estágio vermelho para o estágio verde. Como este exemplo é uma simples operação matemática muito provavelmente não será necessária uma refatoração. No entanto sendo um lógica mais complexa o ideal é que sempre comece testando pequenos passos, que são chamados de baby steps ou passos de bebê. Ao se deparar com uma situação complexa em que o resultado depende de N variáveis, trata-se sempre o meio mais simples e os testes passando passa-se a procurar solucionar uma nova condição para o resultado.

![enter image description here][11]
Método com a lógica necessária e primeiro teste passando

Agora basta adicionar os asserts para as demais operações matemáticas.

![enter image description here][12]
Outros métodos de operações matemáticas simples

Como pode ser percebido, como terceiro parâmetro do assert foi adicionada uma mensagem opcional, isso para que ao dar erro da asserção tal mensagem seja exibida, conforme a imagem seguinte.


![enter image description here][13]
Mensagem de erro de asserção
 
###Refatorando
Agora voltando ao código originado na classe Math, dá pra perceber que há muita repetição pois todos os métodos recebem dois valores e retornam uma operação correspondente. 

Como utilizando TDD temos segurança em desenvolver, podemos tranquilamente remover tais repetições criando uma interface onde é previamente definida a operação a ser realizada e retorna o resultado desta operação. Obviamente com esta atitude o teste também sofrerá alterações e isso é algo comum pois uma aplicação está sempre evoluindo.
Frenta à necessidade de refatoração novamente começamos a partir do teste e ele fica como na imagem a seguir:

![PHPUnit - Alterações na classe de teste][14]
Alterações na classe de teste


Com a refatoração nossa classe Math é modificada e criada uma interface:

![PHPUnit - refatoração da classe Math][15]
Refatoração da classe Math

E agora rodando novamente o teste após a refatoração, simplesmente continuamos com tudo verde, ou seja, alteramos muito a forma de implementação de uma classe e ela continua executando seu papel como deve.


![PHPUnit - Teste passando após refatoração][16]
Teste passando após refatoração

Este é apenas um exemplo didático de refatoração, mas mesmo com ele dá pra perceber como houve a anulação de código repetido e para um futura manutenção basta que mexa-se em um local somente para que surta efeitos à todas as operações matemáticas.


###Finalizando
Neste artigo foi abordado apenas a instalação do PHPUnit e a execução de um teste muito simples. Para testes mais avançados serão criados novos artigos sempre em sequência para que o estudo de desenvolvimento orientado a testes siga um fluxo sadio. Já fora criado um artigo explicando os por ques de se utilizar e não se utilizar TDD que encontra-se [neste link][17] e é o primeiro artigo da sequência.

Os próximos artigos seguirão a sequência abaixo:
* Configurações avançadas – Apenas uma breve abordagem de como realizar configurações avançadas na execução do PHPUnit gerando reports como coverage.
* Persistência – Será utilizado o ORM Doctrine para complementarmos o projeto
* Mockery – Utilizando objetos simulados para atender certos comportamentos


----------
Post original: [Tableless][18]

Publicado em: 07/01/2014


  [1]: http://tableless.com.br/wp-content/uploads/2013/12/01-composer-install-397x310.png
  [2]: http://tableless.com.br/wp-content/uploads/2013/12/02-estrutura-pastas-384x310.png
  [3]: http://tableless.com.br/wp-content/uploads/2013/12/4-nova-estrutura-pastas-588x303.png
  [4]: http://tableless.com.br/tdd-por-que-usar/
  [5]: http://tableless.com.br/wp-content/uploads/2013/12/5-estrutura-primeiro-teste-373x310.png
  [6]: http://tableless.com.br/wp-content/uploads/2013/12/6-falta-de-testes-474x310.png
  [7]: http://tableless.com.br/wp-content/uploads/2013/12/7-nova-estrutura-classe-de-testes1-424x310.png
  [8]: http://tableless.com.br/wp-content/uploads/2013/12/8-quebra-do-teste-488x310.png
  [9]: http://tableless.com.br/wp-content/uploads/2013/12/9-class-504x310.png
  [10]: http://tableless.com.br/wp-content/uploads/2013/12/10-method-missing-588x289.png
  [11]: http://tableless.com.br/wp-content/uploads/2013/12/11-pass-588x262.png
  [12]: http://tableless.com.br/wp-content/uploads/2013/12/12-other-methods-588x272.png
  [13]: http://tableless.com.br/wp-content/uploads/2013/12/13-message-497x310.png
  [14]: http://tableless.com.br/wp-content/uploads/2013/12/14-test-refactor-488x310.png
  [15]: http://tableless.com.br/wp-content/uploads/2013/12/15-refactor-588x284.png
  [16]: http://tableless.com.br/wp-content/uploads/2013/12/16-refactor-pass-577x310.png
  [17]: http://tableless.com.br/tdd-por-que-usar/
  [18]: http://tableless.com.br/phpunit-como-iniciar-sem-dores/