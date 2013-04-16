<?php

namespace YuriTeixeira\WorkflowBundle;

use YuriTeixeira\WorkflowBundle\AbstractWorkflowDefinition;
use YuriTeixeira\WorkflowBundle\Service\AbstractWorkflowService;

/**
 * Base class for workflow execution classes
 */
abstract class AbstractWorkflowExecution extends \ezcWorkflowDatabaseExecution
{
    /**
     * @var AbstractWorkflowDefinition
     */
    protected $workflow;

    /**
     * @var AbstractWorkflowService
     */
    protected $workflowService;

    /**
     * @param \ezcDbHandler           $ezcDbHandler
     * @param AbstractWorkflowService $workflowService
     * @param null                    $executionId
     */
    public function __construct(
        \ezcDbHandler $ezcDbHandler,
        AbstractWorkflowService $workflowService,
        $executionId = null
    ) {
        parent::__construct($ezcDbHandler, $executionId);

        $this->workflowService = $workflowService;
    }

    /**
     * @return \YuriTeixeira\WorkflowBundle\Service\AbstractWorkflowService
     */
    public function getWorkflowService()
    {
        return $this->workflowService;
    }

    /**
     * Overides the default start to attach the workflow definition, save it on database and run execution after all.
     */
    public function start($parentId = null)
    {
        $this->workflow = $this->getWorkflowDefinitionInstance();
        $storage = new \ezcWorkflowDatabaseDefinitionStorage($this->db);
        $storage->save($this->workflow);

        return parent::start($parentId);
    }

    /**
     * @return \YuriTeixeira\WorkflowBundle\AbstractWorkflowDefinition
     */
    abstract protected function getWorkflowDefinitionInstance();
}
