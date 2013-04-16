<?php

namespace YuriTeixeira\WorkflowBundle\Command;

use YuriTeixeira\WorkflowBundle\Service\AbstractWorkflowService;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

/**
 * Execute workflows that needs to be resumed
 */
class ExecuteWorkflowCommand extends ContainerAwareCommand
{
    /* @var $em \Doctrine\ORM\EntityManager */
    protected $em;

    /**
     * Configure this command
     */
    protected function configure()
    {
        $this
            ->setName('yuriteixeira:workflow:resume')
            ->setAliases(array('workflow:resume'))
            ->setDescription('Resume all paused workflows')
        ;
    }

    /**
     * Initialize required services
     *
     * @param \Symfony\Component\Console\Input\InputInterface $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $container = $this->getContainer();
        $this->em = $container->get('doctrine.orm.entity_manager');
    }

    /**
     * Query database for workflow executions and run them
     *
     * @param \Symfony\Component\Console\Input\InputInterface   $input
     * @param \Symfony\Component\Console\Output\OutputInterface $output
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $conn = $this->em->getConnection();

        $res = $conn->executeQuery('SELECT * FROM execution e INNER JOIN workflow w ON e.workflow_id = w.workflow_id');
        $executions = $res->fetchAll();

        if (count($executions) > 0) {
            foreach ($executions as $execution) {
                $this->resumeWorkflows($execution);
            }
        }
    }

    /**
     * Resume a workflow execution
     *
     * @param array $execution
     */
    protected function resumeWorkflows(array $execution)
    {
        try {

            $workflowService = $this->getWorkflowService($execution['workflow_name']);
            $workflowFactory = $workflowService->getWorkflowExecutionFactory();
            $workflowExecution = $workflowFactory->create($execution['workflow_id']);
            $workflowExecution->resume();

        } catch (ServiceNotFoundException $e) {

            /** @todo log invalid workflow */
        }
    }

    /**
     * Return the workflow service to the provided plugin name
     *
     * @param string $workflowName
     *
     * @return AbstractWorkflowService
     */
    protected function getWorkflowService($workflowName)
    {
        return $this->getContainer()->get('workflow.' . $workflowName);
    }
}