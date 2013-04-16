<?php

namespace YuriTeixeira\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * ExecutionState
 *
 * @ORM\Table(name="execution_state")
 * @ORM\Entity
 */
class ExecutionState
{
    /**
     * @var integer
     *
     * @ORM\Column(name="execution_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $executionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="node_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $nodeId;

    /**
     * @var string
     *
     * @ORM\Column(name="node_state", type="blob", nullable=true)
     */
    private $nodeState;

    /**
     * @var string
     *
     * @ORM\Column(name="node_activated_from", type="blob", nullable=true)
     */
    private $nodeActivatedFrom;

    /**
     * @var integer
     *
     * @ORM\Column(name="node_thread_id", type="integer", nullable=false)
     */
    private $nodeThreadId;


}
