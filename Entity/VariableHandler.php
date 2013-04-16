<?php

namespace YuriTeixeira\WorkflowBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * VariableHandler
 *
 * @ORM\Table(name="variable_handler")
 * @ORM\Entity
 */
class VariableHandler
{
    /**
     * @var integer
     *
     * @ORM\Column(name="workflow_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $workflowId;

    /**
     * @var string
     *
     * @ORM\Column(name="class", type="string", length=255, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $class;

    /**
     * @var string
     *
     * @ORM\Column(name="variable", type="string", length=255, nullable=false)
     */
    private $variable;


}
