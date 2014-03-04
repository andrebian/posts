<?php

namespace Tableless\Entity;

use Tableless\Entity\User;
use Tableless\Test\TestCase;

class UserTest extends TestCase
{
    protected $entity;
    
    public function setUp()
    {
        $this->entity = 'Tableless\Entity\User';
        
        parent::setUp();
    }
    
    public function testIfIsSavingAsExpected()
    {
        // Criando os dados necessários para salvar o usuário
        $userData = array(
            'id' => 1,
            'name' => 'Nome do usuário',
            'email' => 'usuario@dominio.com',
            'password' => 'xpto',
            'profilePic' => 'image.png'
        );
        
        /* O Id é gerado automaticamente pelo Doctrine, neste caso estou forçando 
	*  um Id desejado, mas somente para o teste, para o código de produção
	* isto não se faz necessário
	*/
        
        // Instanciando a entidade usuário definindo todos os atributos à ela
        $user = new User( $userData );
        
        // salvando o usuário no banco de dados
        $this->getEntityManager()->persist( $user );
        $this->getEntityManager()->flush();
        
        // Obtendo o usuário salvo
        $registeredUser = $this->getEntityManager()
                                ->getRepository($this->entity)
                                ->findOneBy(array('email' => 'usuario@dominio.com'));
        
        // Garantindo que tudo funcionou conforme o esperado
        $this->assertInstanceOf($this->entity, $registeredUser);
        $this->assertEquals($userData['name'], $registeredUser->getName());
        
        // verificando se hash de senha funcionou
        $this->assertNotEquals($userData['password'], $registeredUser->getPassword());
        
        $this->assertEquals(1, $registeredUser->getId());
        $this->assertEquals('usuario@dominio.com', $registeredUser->getEmail());
        $this->assertNotNull($registeredUser->getSalt());
        $this->assertEquals('image.png', $registeredUser->getProfilePic());
        
    }
    
}
