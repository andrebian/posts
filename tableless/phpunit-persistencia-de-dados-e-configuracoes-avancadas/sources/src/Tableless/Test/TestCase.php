<?php
 
namespace Tableless\Test;
 
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit_Framework_TestCase as PHPUnit;
 
abstract class TestCase extends PHPUnit
{
    
    protected $entityManager = null;
    
    /**
     * Executado antes de cada teste unitário
     */
    public function setUp() 
    {
        $entityManager = $this->getEntityManager();        
        $tool = new SchemaTool($entityManager);
        
        //Obtem informações das entidades que encontrar em Tableless\Entity
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();
        
        // Cria a base de dados necessária com suas determinadas tabelas
        $tool->createSchema($classes);
        
        parent::setup();
    }   
 
    /**
     * Executado após a execução de cada um dos testes unitários
     */
    public function tearDown() 
    {
        $entityManager = $this->getEntityManager();        
        $tool = new SchemaTool($entityManager);
        
        //Obtem informações das entidades que encontrar em Tableless\Entity
        $classes = $entityManager->getMetadataFactory()->getAllMetadata();
        
        // Desfaz o banco criado no setUp
        $tool->dropSchema($classes);
        
        parent::tearDown();
    }
 
    /**
     * 
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager() 
    {
        if (! $this->entityManager) {
            $this->entityManager = require __DIR__ . '/../../../tests/bootstrap.php';
        }
        return $this->entityManager;    
    } 
}