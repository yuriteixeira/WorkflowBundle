<?php

namespace YuriTeixeira\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Workflow
 *
 * @ORM\Table(name="workflow")
 * @ORM\Entity
 */
class Workflow
{
    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $workflowId;

    /**
     * @var string
     *
     * @ORM\Column(name="workflow_name", type="string", length=255, nullable=false)
     */
    private $workflowName;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_version", type="integer", nullable=false)
     */
    private $workflowVersion;

    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_created", type="integer", nullable=false)
     */
    private $workflowCreated;


}
