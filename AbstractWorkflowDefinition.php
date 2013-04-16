<?php

namespace YuriTeixeira\WorkflowBundle;

/**
 * Base class that will be extended by other classes that defines a workflow for a plugin
 */
abstract class AbstractWorkflowDefinition extends \ezcWorkflow
{
    const VAR_DATA = 'data';
    const VAR_CAN_PAUSE = 'can_pause';

    /**
     * Returns the workflow name
     *
     * @return string
     */
    abstract public function getWorkflowName();

    /**
     * Defines the entire workflow and returns a "Plugin End Node".
     * So, a "Common End Node" can be atached as out node for the "Plugin End Node", performing finishing
     * tasks after the "Plugin End Node" and finish the plugin.
     *
     * @return \ezcWorkflowNode;
     */
    abstract protected function define();

    /**
     * Constructor
     */
    public function __construct() {
        parent::__construct($this->getWorkflowName());

        $pluginFinalNode = $this->define();
        $pluginFinalNode->addOutNode($this->endNode);
    }
}
