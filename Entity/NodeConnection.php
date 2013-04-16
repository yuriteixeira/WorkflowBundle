<?php

namespace YuriTeixeira\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * NodeConnection
 *
 * @ORM\Table(name="node_connection")
 * @ORM\Entity
 */
class NodeConnection
{
    /**
     * @var integer
     *
     * @ORM\Column(name="node_connection_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $nodeConnectionId;

    /**
     * @var integer
     *
     * @ORM\Column(name="incoming_node_id", type="integer", nullable=false)
     */
    private $incomingNodeId;

    /**
     * @var integer
     *
     * @ORM\Column(name="outgoing_node_id", type="integer", nullable=false)
     */
    private $outgoingNodeId;


}
