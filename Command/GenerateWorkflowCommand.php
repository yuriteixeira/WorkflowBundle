<?php

namespace YuriTeixeira\WorkflowBundle\Command;

use Sensio\Bundle\GeneratorBundle\Manipulator\KernelManipulator;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Sensio\Bundle\GeneratorBundle\Command\Helper\DialogHelper;
use YuriTeixeira\WorkflowBundle\Generator\WorkflowGenerator;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * CLI command that generates a workflowDefinition stub.
 */
class GenerateWorkflowCommand extends ContainerAwareCommand
{
    const OPTION_NAME = 'name';
    const OPTION_NAMESPACE = 'namespace';

    /**
     * @var WorkflowGenerator
     */
    protected $generator;

    /**
     * Configure the CLI command
     */
    protected function configure()
    {
        $help =
            "The <info>generate:workflow</info> command helps you generates new workflows." .
            "By default, the command interacts with the developer to tweak the generation." .
            "Note that the workflow namespace must end with \"Workflow\".";

        $this
            ->setName('workflow:generate')
            ->setDescription('Generates the core structure for a new workflow')
            ->setHelp($help)
            ->setDefinition(array(
                new InputOption(static::OPTION_NAME, '', InputOption::VALUE_REQUIRED, 'The workflow name'),
                new InputOption(static::OPTION_NAMESPACE, '', InputOption::VALUE_REQUIRED, 'The workflow namespace'),
            ));
    }

    /**
     * Execute CLI command
     *
     * @throws \InvalidArgumentException When namespace doesn't end with Bundle
     * @throws \RuntimeException         When bundle can't be executed
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->confirmGeneration($input, $output);

        // Getting needed info
        $workflowName = $this->getWorkflowName($input);
        $workflowNamespace = $this->getWorkflowNamespace($input);
        $workflowDir = $this->getWorkflowDir($workflowNamespace);

        // Generation
        $dialog->writeSection($output, 'Workflow generation');

        $generator = $this->getGenerator();
        $generator->generate($workflowNamespace, $workflowName, $workflowDir);

        $output->writeln('Generating the workflow stub code: <info>OK</info>');

        $errors = array();
        $runner = $dialog->getRunner($output, $errors);

        // App kernal update
        $this->updateKernel($dialog, $input, $output, $this->getContainer()->get('kernel'), $workflowNamespace, $workflowName);

        // Check that the namespace is already autoloaded
        $runner($this->checkAutoloader($output, $workflowNamespace, $workflowName, $workflowDir));

        // Check cache
        $command = $this->getApplication()->find('cache:clear');
        $cacheInput = new ArrayInput(array('command' => 'cache:clear'));
        $command->run($cacheInput, $output);

        // Summary
        $dialog->writeGeneratorSummary($output, $errors);
    }

    /**
     * Gets workflow dir
     *
     * @param $workflowNamespace
     *
     * @return string
     */
    protected function getWorkflowDir($workflowNamespace)
    {
        $workflowDir =
            $this->getContainer()->get('kernel')->getRootDir() .
            '/../src/' .
            str_replace('\\', DIRECTORY_SEPARATOR, $workflowNamespace);

        return $workflowDir;
    }

    /**
     * Gets workflow namespace
     *
     * @param InputInterface $input
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function getWorkflowNamespace(InputInterface $input)
    {
        $workflowNamespace = $input->getOption(static::OPTION_NAMESPACE);

        if (!$workflowNamespace) {
            throw new \RuntimeException('Inform a valid workflow namespace (--namespace).');
        }

        return $workflowNamespace;
    }

    /**
     * Gets workflow name
     *
     * @param InputInterface $input
     *
     * @return string
     * @throws \RuntimeException
     */
    protected function getWorkflowName(InputInterface $input)
    {
        $workflowName = $input->getOption(static::OPTION_NAME);

        if (!$workflowName && !Validators::validateWorkflowName($workflowName)) {
            throw new \RuntimeException('Inform a valid workflow name (--name).');
        }

        return $workflowName;
    }

    /**
     * Confirming generation
     *
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return DialogHelper|\Symfony\Component\Console\Helper\HelperInterface
     * @throws \RuntimeException
     */
    protected function confirmGeneration(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();

        $confirmed = $dialog->askConfirmation(
            $output,
            $dialog->getQuestion('Do you confirm generation', 'yes', '?'),
            true
        );

        if ($input->isInteractive() && !$confirmed) {
            throw new \RuntimeException('Command aborted.');
        }

        return $dialog;
    }

    /**
     * Defines interaction with CLI command
     *
     * @param InputInterface  $input
     * @param OutputInterface $output
     */
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $dialog = $this->getDialogHelper();
        $dialog->writeSection($output, 'Welcome to the workflow generator');

        $collectors[static::OPTION_NAME] = array(
            'question' => 'Workflow name',
            'value' => $input->getOption(static::OPTION_NAME),
            'default' => null,
            'message_lines' => array(
                '',
                'The workflow name is required. Please, use a CamelCase name with the "Workflow" suffix.',
                'Example: <comment>PaypalWorkflow</comment> or <comment>MegaWorkflow</comment>.',
                ''
            )
        );

        $collectors[static::OPTION_NAMESPACE] = array(
            'question' => 'Workflow namespace',
            'value' => $input->getOption(static::OPTION_NAMESPACE),
            'default' => function() use (&$collectors) { return "Your\\Namespace\\Workflow\\{$collectors[static::OPTION_NAME]['value']}"; },
            'message_lines' => array(
                '',
                'The workflow namespace is required. Please, use a CamelCase name.',
                'Example: <comment>Your\Namespace\Workflow</comment>.',
                ''
            )
        );

        foreach ($collectors as $collectorKey => $collector) {
            if (!$collector['value']) {
                $output->writeln($collector['message_lines']);

                $default = is_callable($collector['default']) ? $collector['default']() : $collector['default'];

                $collectors[$collectorKey]['value'] =
                    $dialog->ask(
                        $output,
                        $dialog->getQuestion($collector['question'], $default),
                        $default
                    );

                $input->setOption($collectorKey, $collectors[$collectorKey]['value']);
            }
        }

        $summaryMessage =
            'You are going to generate Workflow "<info>%1$s</info>"'. PHP_EOL .
            'This workflow will be stored on <info>%2$s</info>';

        $workflowName = $collectors[static::OPTION_NAME]['value'];
        $workflowDir = $this->getWorkflowDir($collectors[static::OPTION_NAMESPACE]['value']);

        $output->writeln(array(
            '',
            $this->getHelper('formatter')->formatBlock('Summary before generation', 'bg=blue;fg=white', true),
            '',
            sprintf(
                $summaryMessage,
                $workflowName,
                $workflowDir
            ),
            '',
        ));
    }

    /**
     * Check if workflow will be autoloaded correctly
     *
     * @param OutputInterface $output
     * @param                 $namespace
     * @param                 $bundle
     *
     * @return array List of messages that will be displayed
     */
    protected function checkAutoloader(OutputInterface $output, $namespace, $bundle)
    {
        $output->write('Checking that the workflow bundle will be autoloaded correctly: ');

        if (!class_exists($namespace . '\\' . $bundle)) {
            return array(
                '- Edit the <comment>composer.json</comment> file and register the bundle',
                '  namespace in the "autoload" section:',
                '',
            );
        }
    }

    /**
     * Returns the generator instance
     *
     * @return WorkflowGenerator
     */
    protected function getGenerator()
    {
        if (null === $this->generator) {
            $this->generator = new WorkflowGenerator(
                $this->getContainer()->get('filesystem'),
                __DIR__ . '/../Resources/skeleton/workflow'
            );
        }

        return $this->generator;
    }

    /**
     * Returns a dialog helper instance
     *
     * @return DialogHelper|\Symfony\Component\Console\Helper\HelperInterface
     */
    protected function getDialogHelper()
    {
        $dialog = $this->getHelperSet()->get('dialog');

        if (!$dialog || ! $dialog instanceof DialogHelper) {
            $this->getHelperSet()->set($dialog = new DialogHelper());
        }

        return $dialog;
    }

    /**
     * Update AppKernel class (small modifications over Fabien Potencier implementation found on GenerateBundleCommand)
     *
     * @param                 $dialog
     * @param InputInterface  $input
     * @param OutputInterface $output
     * @param KernelInterface $kernel
     * @param                 $namespace
     * @param                 $bundle
     *
     * @return array
     */
    protected function updateKernel($dialog, InputInterface $input, OutputInterface $output, KernelInterface $kernel, $namespace, $bundle)
    {
        $auto = true;

        if ($input->isInteractive()) {
            $auto = $dialog->askConfirmation($output, $dialog->getQuestion('Confirm automatic update of your Kernel', 'yes', '?'), true);
        }

        $output->write('Enabling the bundle inside the Kernel: ');
        $manip = new KernelManipulator($kernel);

        try {
            $ret = $auto ? $manip->addBundle($namespace.'\\'.$bundle) : false;

            if (!$ret) {
                $reflected = new \ReflectionObject($kernel);

                return array(
                    sprintf('- Edit <comment>%s</comment>', $reflected->getFilename()),
                    '  and add the following bundle in the <comment>AppKernel::registerBundles()</comment> method:',
                    '',
                    sprintf('    <comment>new %s(),</comment>', $namespace.'\\'.$bundle),
                    '',
                );
            }
        } catch (\RuntimeException $e) {
            return array(
                sprintf('Bundle <comment>%s</comment> is already defined in <comment>AppKernel::registerBundles()</comment>.', $namespace.'\\'.$bundle),
                '',
            );
        }
    }
}
