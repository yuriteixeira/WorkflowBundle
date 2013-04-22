<?php

namespace YuriTeixeira\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Execution
 *
 * @ORM\Table(name="execution")
 * @ORM\Entity
 */
class Execution
{
    /**
     * @var integer
     *
     * @ORM\Column(name="execution_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $executionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=false)
     */
    private $workflowId;

    /**
     * @var integer
     *
     * @ORM\Column(name="execution_parent", type="integer", nullable=true)
     */
    private $executionParent;

    /**
     * @var integer
     *
     * @ORM\Column(name="execution_started", type="integer", nullable=false)
     */
    private $executionStarted;

    /**
     * @var integer
     *
     * @ORM\Column(name="execution_suspended", type="integer", nullable=true)
     */
    private $executionSuspended;

    /**
     * @var string
     *
     * @ORM\Column(name="execution_variables", type="blob", nullable=true)
     */
    private $executionVariables;

    /**
     * @var string
     *
     * @ORM\Column(name="execution_waiting_for", type="blob", nullable=true)
     */
    private $executionWaitingFor;

    /**
     * @var string
     *
     * @ORM\Column(name="execution_threads", type="blob", nullable=true)
     */
    private $executionThreads;

    /**
     * @var integer
     *
     * @ORM\Column(name="execution_next_thread_id", type="integer", nullable=false)
     */
    private $executionNextThreadId;


}
