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
     * @param $format
     */
    public function generate($namespace, $workflowName, $workflowDir, $format)
    {
        $this->checkRequirements($workflowDir);

        $serviceName = $workflowName . 'Service';
        $this->renderFiles($namespace, $workflowName, $workflowDir, $serviceName, $format);
    }

    /**
     * Generate parameters needed to render files
     *
     * @param $namespace
     * @param $workflowName
     * @param $format
     * @param $serviceName
     *
     * @return array
     */
    protected function generateRenderParameters($namespace, $workflowName, $format, $serviceName)
    {
        $workflowShortName = preg_replace('/([a-z])([A-Z])/e', "'\${1}_\${2}'", $workflowName);
        $workflowSpinalCase = preg_replace('/([a-z])([A-Z])/e', "'\${1}-\${2}'", $workflowName);
        $basename = substr($workflowName, 0, -6);

        $parameters = array(
            'namespace'        => $namespace,
            'workflowName'       => $workflowName,
            'workflowShortName'  => strtolower($workflowShortName),
            'workflowSpinalCase' => strtolower($workflowSpinalCase),
            'workflowCamelCase'  => lcfirst($workflowName),
            'serviceName'      => $serviceName,
            'format'           => $format,
            'bundle_basename'  => $basename,
            'extension_alias'  => Container::underscore($basename),
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
    protected function checkRequirements($dir)
    {
        if (file_exists($dir)) {

            if (!is_dir($dir)) {
                throw new \RuntimeException(sprintf(
                    'Unable to generate the workflow as the target directory "%s" exists but is a file.',
                    realpath($dir)
                ));
            }

            $files = scandir($dir);

            if ($files != array('.', '..')) {
                throw new \RuntimeException(sprintf(
                    'Unable to generate the workflow as the target directory "%s" is not empty.',
                    realpath($dir)
                ));
            }

            if (!is_writable($dir)) {
                throw new \RuntimeException(sprintf(
                    'Unable to generate the workflow as the target directory "%s" is not writable.',
                    realpath($dir)
                ));
            }
        }
    }

    /**
     * Render files to target workflow directory
     *
     * @param $namespace
     * @param $workflowName
     * @param $targetDir
     * @param $serviceName
     * @param $format
     *
     * @throws \Exception
     */
    protected function renderFiles($namespace, $workflowName, $targetDir, $serviceName, $format)
    {
        $parameters = $this->generateRenderParameters($namespace, $workflowName, $format, $serviceName);

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
                'template_name' => 'WorkflowBeginAction.php.twig',
                'target_path' => $targetDir . '/Action/WorkflowBeginAction.php',
            ),
            array(
                'template_name' => 'DoSomethingAction.php.twig',
                'target_path' => $targetDir . '/Action/DoSomethingAction.php',
            ),
            array(
                'template_name' => 'CheckResultAction.php.twig',
                'target_path' => $targetDir . '/Action/CheckResultAction.php',
            ),
            array(
                'template_name' => 'WorkflowEndAction.php.twig',
                'target_path' => $targetDir . '/Action/WorkflowEndAction.php',
            ),
            array(
                'template_name' => 'WorkflowService.php.twig',
                'target_path' => $targetDir . '/Service/' . $serviceName . '.php',
            ),
            array(
                'template_name' => 'ServiceTest.php.twig',
                'target_path' => $targetDir . '/Tests/Integration/Service/' . $serviceName . 'Test.php',
            ),
            array(
                'template_name' => 'services.yml.twig',
                'target_path' => $targetDir . '/Resources/config/services.yml',
            ),
            array(
                'template_name' => 'Extension.php.twig',
                'target_path' => $targetDir . '/DependencyInjection/' . $workflowName . 'Extension.php',
            ),
            array(
                'template_name' => 'Configuration.php.twig',
                'target_path' => $targetDir . '/DependencyInjection/Configuration.php',
            ),
            array(
                'template_name' => 'GetWorkflowImageCommand.php.twig',
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
}
