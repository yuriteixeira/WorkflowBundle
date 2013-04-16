<?php

namespace YuriTeixeira\WorkflowBundle\Generator;

use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\DependencyInjection\Container;
use Sensio\Bundle\GeneratorBundle\Generator\Generator;

/**
 * Generates a workflow stub.
 */
class WorkflowGenerator extends Generator
{
    private $filesystem;

    /**
     * Constructor
     *
     * @param Filesystem $filesystem
     * @param            $skeletonDirs
     */
    public function __construct(Filesystem $filesystem, $skeletonDirs)
    {
        $this->filesystem = $filesystem;
        $this->setSkeletonDirs($skeletonDirs);
    }

    /**
     * Generates workflow
     *
     * @param $namespace
     * @param $workflowName
     * @param $workflowDir
     */
    public function generate($namespace, $workflowName, $workflowDir)
    {
        $this->checkFilesystemRequirements($workflowDir);
        $this->renderFiles($namespace, $workflowName, $workflowDir);
    }

    /**
     * Render files to target workflow directory
     *
     * @param $workflowNamespace
     * @param $workflowName
     * @param $targetDir
     *
     * @throws \Exception
     */
    protected function renderFiles($workflowNamespace, $workflowName, $targetDir)
    {
        $serviceName = $workflowName . 'Service';

        $parameters = $this->generateRenderParameters($workflowNamespace, $workflowName, $serviceName);

        $renderQueue = array(
            array(
                'template_name' => 'Bundle.php.twig',
                'target_path' => $targetDir . '/' . $workflowName . '.php',
            ),
            array(
                'template_name' => 'WorkflowDefinition.php.twig',
                'target_path' => $targetDir . '/WorkflowDefinition.php',
            ),
            array(
                'template_name' => 'WorkflowExecution.php.twig',
                'target_path' => $targetDir . '/WorkflowExecution.php',
            ),
            array(
                'template_name' => 'WorkflowExecutionFactory.php.twig',
                'target_path' => $targetDir . '/WorkflowExecutionFactory.php',
            ),
            array(
                'template_name' => 'Action/WorkflowBeginAction.php.twig',
                'target_path' => $targetDir . '/Action/WorkflowBeginAction.php',
            ),
            array(
                'template_name' => 'Action/DoSomethingAction.php.twig',
                'target_path' => $targetDir . '/Action/DoSomethingAction.php',
            ),
            array(
                'template_name' => 'Action/CheckResultAction.php.twig',
                'target_path' => $targetDir . '/Action/CheckResultAction.php',
            ),
            array(
                'template_name' => 'Action/WorkflowEndAction.php.twig',
                'target_path' => $targetDir . '/Action/WorkflowEndAction.php',
            ),
            array(
                'template_name' => 'Service/WorkflowService.php.twig',
                'target_path' => $targetDir . '/Service/' . $serviceName . '.php',
            ),
            array(
                'template_name' => 'Tests/ServiceTest.php.twig',
                'target_path' => $targetDir . '/Tests/Integration/Service/' . $serviceName . 'Test.php',
            ),
            array(
                'template_name' => 'Resources/config/services.yml.twig',
                'target_path' => $targetDir . '/Resources/config/services.yml',
            ),
            array(
                'template_name' => 'DependencyInjection/Extension.php.twig',
                'target_path' => $targetDir . '/DependencyInjection/' . $workflowName . 'Extension.php',
            ),
            array(
                'template_name' => 'DependencyInjection/Configuration.php.twig',
                'target_path' => $targetDir . '/DependencyInjection/Configuration.php',
            ),
            array(
                'template_name' => 'Command/GetWorkflowImageCommand.php.twig',
                'target_path' => $targetDir . '/Command/GetWorkflowImageCommand.php',
            ),
        );

        foreach ($renderQueue as $renderItem) {
            try {

                $targetPath = $renderItem['target_path'];
                $templateName = $renderItem['template_name'];

                $this->renderFile(
                    $templateName,
                    $targetPath,
                    $parameters
                );

            } catch (\Exception $e) {

                throw new \Exception(
                    sprintf(
                        'Failure rendering %s on path %s',
                        $templateName,
                        $targetPath
                    ),
                    0,
                    $e
                );
            }
        }
    }

    /**
     * Generate parameters needed to render files
     *
     * @param $workflowNamespace
     * @param $workflowName
     * @param $workflowServiceName
     *
     * @return array
     */
    protected function generateRenderParameters($workflowNamespace, $workflowName, $workflowServiceName)
    {
        $workflowNameCamelCase = lcfirst($workflowName);
        $workflowNameSnakeCase = strtolower(preg_replace('/([a-z])([A-Z])/e', "'\${1}_\${2}'", $workflowName));
        $workflowNameSpinalCase = strtolower(str_replace('_', '-', $workflowNameSnakeCase));

        $parameters = array(
            'workflow_namespace'        => $workflowNamespace,
            'workflow_service_name'     => $workflowServiceName,
            'workflow_name'             => $workflowName,
            'workflow_name_camel_case'  => $workflowNameCamelCase,
            'workflow_name_snake_case'  => $workflowNameSnakeCase,
            'workflow_name_spinal_case' => $workflowNameSpinalCase,
        );

        return $parameters;
    }

    /**
     * Verify if target directory has the requirements to be the workflow folder
     *
     * @param $dir Target directory
     *
     * @throws \RuntimeException
     */
    protected function checkFilesystemRequirements($dir)
    {
        if (file_exists($dir)) {

            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf(
                    'Unable to generate the workflow: Target directory "%s" exists but is a file.',
                    realpath($dir)
                ));
            }

            $files = scandir($dir);

            if ($files != array('.', '..')) {
                throw new \RuntimeException(sprintf(
                    'Unable to generate the workflow: Target directory "%s" is not empty.',
                    realpath($dir)
                ));
            }

            if (!is_writable($dir)) {
                throw new \RuntimeException(sprintf(
                    'Unable to generate the workflow: Target directory "%s" is not writable.',
                    realpath($dir)
                ));
            }
        }
    }
}
