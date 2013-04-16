<?php

namespace YuriTeixeira\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 *
 * @ORM\Table(name="node")
 * @ORM\Entity
 */
class Node
{
    /**
     * @var integer
     *
     * @ORM\Column(name="node_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $nodeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=false)
     */
    private $workflowId;

    /**
     * @var string
     *
     * @ORM\Column(name="node_class", type="string", length=255, nullable=false)
     */
    private $nodeClass;

    /**
     * @var string
     *
     * @ORM\Column(name="node_configuration", type="blob", nullable=true)
     */
    private $nodeConfiguration;


}
