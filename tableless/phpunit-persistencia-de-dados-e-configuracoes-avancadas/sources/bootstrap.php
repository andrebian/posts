<?php

// Carregando o autoload que o composer gerou
require __DIR__ . '/vendor/autoload.php';


// indicando tudo que usaremos no bootstrap
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Mapping\Driver\AnnotationDriver;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;


/**
 * Definindo se é modo desenvolvimento
 * 
 * Caso true: o cache do Doctrine é realizado em formato de array
 * Caso false: o cache é conforme configuração (memcache, APC..)
 * 
 * Somente trabalharemos aqui com o modo TRUE, cache em array
 */
$config = Setup::createConfiguration( true );


// pasta onde encontra-se nossas entidades
$entitypath = array( __DIR__ . '/src/Tableless/Entity' );


// registrando as entidades
$driver = new AnnotationDriver(new AnnotationReader(), $entitypath);
$config->setMetadataDriverImpl($driver);

/**
 * indicando que trabalharemos com o modo annotations para
 * as entidades. Pode ser também via arquivo yaml e xml
 * 
 */
AnnotationRegistry::registerFile(__DIR__ . '/vendor/doctrine/orm/lib/Doctrine/ORM/Mapping/Driver/DoctrineAnnotations.php');


// configurando a conexão com o banco de dados
$conn = array(
    'driver' => 'pdo_mysql',
    'user' => 'root',
    'password' => 'root',
    'dbname' => 'tableless_tdd',
);

// E finalmente criando o manipulador de entidades
$entityManager = EntityManager::create($conn, $config);