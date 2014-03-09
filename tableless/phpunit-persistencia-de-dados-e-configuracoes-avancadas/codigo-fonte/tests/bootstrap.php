<?php

// utilizando o bootstrap de produção
require '../bootstrap.php';

use Doctrine\ORM\EntityManager;

/*
 * Sobrescrevendo a conexão com banco de dados.
 * 
 * Isto faz-se necessário para que ao rodar os testes 
 * o banco de produção não sofra alterações
 */
$conn = array(
    'driverClass' => 'Doctrine\DBAL\Driver\PDOSqlite\Driver',
    'path' => ':memory:',
);

// obtaining the entity manager
$entityManager = EntityManager::create($conn, $config);

return $entityManager;
