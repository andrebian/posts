<?php 

namespace Tableless\Filter;

use Tableless\Test\TestCase;

/**
* @group Filter
*/
class CurrencyTest extends TestCase 
{

  public function testIfClassExists()
  {
    $this->assertTrue(class_exists('Tableless\Filter\Currency'));
  }

}
