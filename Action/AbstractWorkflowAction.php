<?php

namespace YuriTeixeira\WorkflowBundle\Action;

use YuriTeixeira\WorkflowBundle\AbstractWorkflowExecution;

/**
 * Base class for plugin actions
 */
abstract class AbstractWorkflowAction implements \ezcWorkflowServiceObject
{
    /**
     * @var \YuriTeixeira\WorkflowBundle\AbstractWorkflowExecution
     */
    protected $execution;

    /**
     * @var \YuriTeixeira\WorkflowBundle\Service\AbstractWorkflowService
     */
    protected $workflowService;

    /**
     * Returns a textual representation of this service object.
     *
     * @return string
     */
    public function __toString()
    {
        $fullClassPath = get_class($this);
        $fullClassPathParts = explode('\\', $fullClassPath);
        $fullClassPathParts = array_reverse($fullClassPathParts);
        $className = $fullClassPathParts[0];

        return $className;
    }

    public function execute(\ezcWorkflowExecution $execution) {
        if (! $execution instanceof AbstractWorkflowExecution) {
            $correctType = '\YuriTeixeira\WorkflowBundle\AbstractWorkflowExecution';
            $executionType = get_class($execution);
            throw new \Exception("Execution should be an instance of \"{$correctType}\", \"{$executionType} given\"");
        }

        $this->execution = $execution;
        $this->workflowService = $execution->getWorkflowService();

        return $this->run($execution);
    }

    /**
     * Executes a Workflow Execution (needed to avoid execution type checks with "instanceof")
     *
     * @return bool
     */
    abstract protected function run();
}
