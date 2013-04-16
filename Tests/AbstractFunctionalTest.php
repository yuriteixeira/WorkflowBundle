<?php

namespace YuriTeixeira\WorkflowBundle\Tests;

/**
 * Abstract functional test class
 */
abstract class AbstractFunctionalTest extends AbstractIntegrationTest
{
    /**
     * Web Test Client
     *
     * @var \Symfony\Bundle\FrameworkBundle\Client
     */
    protected $client;

    protected function setUp()
    {
        parent::setUp();

        $this->client = static::createClient();
    }
}
