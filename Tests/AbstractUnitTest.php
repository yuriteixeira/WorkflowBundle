<?php

namespace YuriTeixeira\WorkflowBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Abstract unit test class
 */
abstract class AbstractUnitTest extends WebTestCase
{
    protected function setUp()
    {
        parent::setUp();
        $this->bootKernel();
    }

    protected function bootKernel()
    {
        static::$kernel = $this->createKernel();
        static::$kernel->boot();
    }

    protected function getContainer()
    {
        return static::$kernel->getContainer();
    }

    protected function getKernelRootDir()
    {
        return static::$kernel->getContainer()->get('kernel')->getRootDir();
    }
}
