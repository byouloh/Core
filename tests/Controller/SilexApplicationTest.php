<?php

namespace OpenTribes\Core\Test\Controller;


use Silex\Application;

abstract class SilexApplicationTest extends \PHPUnit_Framework_TestCase {
    /**
     * @return Application
     */
    public function getApplication(){
        return include __DIR__.'/../../bootstrap.php';
    }
}
 