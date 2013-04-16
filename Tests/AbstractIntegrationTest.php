<?php

namespace YuriTeixeira\WorkflowBundle\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Abstract integration test class
 *
 * @author Yuri Teixeira <contato@yuriteixeira.com.br>
 */
abstract class AbstractIntegrationTest extends AbstractUnitTest
{
    protected function setUp()
    {
        parent::setUp();

        $this->resetDatabase();
        $this->resetLogs();
    }

    /**
     * Returns an entity manager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    protected function getEntityManager()
    {
        return $this->getContainer()->get('doctrine.orm.entity_manager');
    }

    /**
     * Resets database
     */
    protected function resetDatabase()
    {
        $connection = $this->getEntityManager()->getConnection();
        // TODO: Data truncation here...
    }

    /**
     * Resets logs
     */
    protected function resetLogs($environment = null)
    {
        $kernel = $this->getContainer()->get('kernel');
        $environment = $environment ? : $kernel->getEnvironment();
        $logFile = $kernel->getLogDir() . '/' . $environment . '.log';
        file_put_contents($logFile, "");
    }

    /**
     * Gets log file content
     *
     * @param string $logFilename
     *
     * @return string
     */
    protected function getLogContents($logFilename = null)
    {
        $kernel = $this->getContainer()->get('kernel');
        $logFilename = $logFilename ? : $kernel->getEnvironment();
        $logPath = "{$kernel->getLogDir()}/{$logFilename}.log";
        $logContents = file_get_contents($logPath);

        return $logContents;
    }

    /**
     * Get the last line from log file
     *
     * @param string $logFilename
     *
     * @return mixed
     */
    protected function getLastLineFromLog($logFilename = null)
    {
        $contents = $this->getLogContents($logFilename);
        $contentArray = explode("\n", $contents);
        for ($i = (count($contentArray) - 1); $i--; $i == 0) {
            if ($contentArray[$i]) {
                return $contentArray[$i];
            }
        }
    }
}
