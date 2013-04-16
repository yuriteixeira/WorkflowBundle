<?php

namespace YuriTeixeira\WorkflowBundle\Command;

use Sensio\Bundle\GeneratorBundle\Command\Validators as BaseValidators;

/**
 * Validator used when executing workflow generation task
 */
class Validators extends BaseValidators
{
    /**
     * Validates workflow namespace
     *
     * @param $namespace
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public static function validateWorkflowNamespace($namespace)
    {
        // Must end with "Workflow"
        if (!preg_match('/Workflow$/', $namespace)) {
            throw new \InvalidArgumentException('The namespace must end with Plugin.');
        }

        // Must have only valid charactes
        $namespace = strtr($namespace, '/', '\\');
        if (!preg_match('/^(?:[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*\\\?)+$/', $namespace)) {
            throw new \InvalidArgumentException('The namespace contains invalid characters.');
        }

        // Must skip reserved words
        $reserved = self::getReservedWords();
        foreach (explode('\\', $namespace) as $word) {
            if (in_array(strtolower($word), $reserved)) {
                throw new \InvalidArgumentException(sprintf('The namespace cannot contain PHP reserved words ("%s").', $word));
            }
        }

        return $namespace;
    }

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