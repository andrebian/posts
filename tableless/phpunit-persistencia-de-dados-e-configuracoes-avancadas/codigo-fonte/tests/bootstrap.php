<?php

// utilizando o bootstrap de produção
require __DIR__ . '/../bootstrap.php';
 
use Doctrine\ORM\EntityManager;


/*
 * Sobrescrevendo a conexão com banco de dados.
 * 
 * Isto faz-se necessário para que ao rodar os testes 
 * o banco de produção não sofra alterações
 */
 
$conn = array(
    'driver'   => 'pdo_sqlite',
    'dbname'   => ':memory:',
);

return $entityManager = EntityManager::create($conn, $config);