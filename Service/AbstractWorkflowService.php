<?php

namespace YuriTeixeira\WorkflowBundle\Service;

/**
 * Base Plugin Service Class
 *
 * @author Yuri Teixeira <contato@yuriteixeira.com.br>
 */
abstract class AbstractWorkflowService
{
    /**
     * @var string
     */
    protected $host;

    /**
     * @var string
     */
    protected $database;

    /**
     * @var string
     */
    protected $username;

    /**
     * @var string
     */
    protected $password;

    /**
     * @var \YuriTeixeira\WorkflowBundle\AbstractWorkflowExecutionFactory
     */
    protected $workflowExecutionFactory;

    /**
     * Constructor
     *
     * @param string $host
     * @param string $database
     * @param string $username
     * @param string $password
     */
    function __construct($host, $database, $username, $password)
    {
        $this->host = $host;
        $this->database = $database;
        $this->username = $username;
        $this->password = $password;
    }

    /**
     * Returns the full path of plugin's workflow execution factory class
     *
     * @return string
     */
    protected function getWorkflowExecutionFactoryClass()
    {
        $pattern = '/((\\\\[a-zA-Z0-9_]+){2})$/';
        $serviceClassPath = get_class($this);
        $factoryClassPath = preg_replace($pattern, '\\WorkflowExecutionFactory', $serviceClassPath);
        return $factoryClassPath;
    }

    /**
     * Returns a workflow excution factory instance
     *
     * @return \YuriTeixeira\WorkflowBundle\AbstractWorkflowExecutionFactory
     */
    public function getWorkflowExecutionFactory()
    {
        if (!$this->workflowExecutionFactory) {
            $factoryClassName = $this->getWorkflowExecutionFactoryClass();

            $factory = new $factoryClassName(
                $this->host,
                $this->database,
                $this->username,
                $this->password,
                $this
            );

            $this->workflowExecutionFactory = $factory;
        }

        return $this->workflowExecutionFactory ;
    }

    /**
     * Start a workflow
     */
    public function startNewWorkflowExecution()
    {
        $workflowExecution = $this->getWorkflowExecutionFactory()->create();
        $workflowExecution->start();
    }

    /**
     * Cancel a workflow execution
     */
    public function cancelWorkflowExecution($executionId)
    {
        $workflowExecution = $this->getWorkflowExecutionFactory()->create($executionId);
        $workflowExecution->cancel();
    }
}
