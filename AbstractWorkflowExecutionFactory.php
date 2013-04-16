<?php

namespace YuriTeixeira\WorkflowBundle;

use Psr\Log\LoggerInterface;
use YuriTeixeira\WorkflowBundle\Service\AbstractWorkflowService;

/**
 * Class
 *
 * @author Yuri Teixeira <contato@yuriteixeira.com.br>
 */
abstract class AbstractWorkflowExecutionFactory
{
    /**
     * @var \ezcDbHandlerMysql
     */
    protected $ezcDbHandler;

    /**
     * @var \YuriTeixeira\WorkflowBundle\Plugin\Service\AbstractPluginService
     */
    protected $workflowService;

    public function __construct(
        $databaseHost,
        $databaseName,
        $databaseUser,
        $databasePassword,
        AbstractWorkflowService $workflowService
    ) {
        $this->ezcDbHandler = new \ezcDbHandlerMysql(
            array(
                'host' => $databaseHost,
                'dbname' => $databaseName,
                'user' => $databaseUser,
                'password' => $databasePassword,
            )
        );

        $this->workflowService = $workflowService;
    }

    /**
     * @param int $executionId
     * @return \YuriTeixeira\WorkflowBundle\AbstractWorkflowExecution
     */
    abstract protected function getWorkflowExecution($executionId = null);

    /**
     * Returns an instance of plugin's workflow execution
     *
     * @param int  $executionId
     *
     * @return \YuriTeixeira\WorkflowBundle\AbstractWorkflowExecution
     */
    public function create($executionId = null)
    {
        $executionId = $executionId ? (int) $executionId : null;
        return $this->getWorkflowExecution($executionId);
    }
}
