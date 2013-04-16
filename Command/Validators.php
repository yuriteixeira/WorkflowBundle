<?php

namespace YuriTeixeira\WorkflowBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\Validators as BaseValidators;

/**
 * Validator used when executing workflow generation task
 */
class Validators extends BaseValidators
{
    /**
     * Validates workflow name
     *
     * @param $workflow
     *
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public static function validateWorkflowName($workflow)
    {
        // Must end with "Workflow"
        if (!preg_match('/Workflow$/', $workflow)) {
            throw new \InvalidArgumentException('The workflow name must end with \"Workflow\".');
        }
    }
}