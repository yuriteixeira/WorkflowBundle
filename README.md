# About Workflow Bundle

If you ever needed to implement certain parts of your symfony2 application in a workflow fashion (like a payment state machine or a cms publishing flow), you came to the right place.

This bundle was built upon Sebastian Bergman's [ezcWorkflow component](http://zetacomponents.org/documentation/trunk/Workflow/tutorial.html). Before start, we **strongly recommend** you to understand the concepts behind it (otherwise, your are gonna fell a little bit lost to define your own workflow).

# Installation

1. Add the requirement `yuriteixeira/workflow-bundle` in your composer.json and run `composer update`:
2. Register the bundle on your AppKernel.php, by adding `new Yuriteixeira\WorkflowBundle\WorkflowBundle()` on `$bundles` array.
3. Run `php app/console`. You should see a section called `yuriteixeira` there with workflow related commands.
4. Run `php app/console doctrine:schema:update` to add the tables needed by our workflow in your database.

All right! Let's get our hands dirty.

# Usage

## Generating your own workflow

This bundle intended to make your life easy. So, instead of give you complex instructions of how to create your workflow manually, we took this boring and error-prone job from your hands!

All you have to do is run `yuriteixeira:workflow:generate`. This code generator will generate all the needed file structure, update your AppKernel and boom, your just need to customize the code to your needs. 

## Understanding the generated files structure

```
├── MyWorkflow
│   ├── Action
│   │   ├── CheckResultAction.php
│   │   ├── DoSomethingAction.php
│   │   ├── WorkflowBeginAction.php
│   │   └── WorkflowEndAction.php
│   ├── Command
│   │   └── GetWorkflowImageCommand.php
│   ├── DependencyInjection
│   │   ├── Configuration.php
│   │   └── MyWorkflowExtension.php
│   ├── MyWorkflow.php
│   ├── Resources
│   │   └── config
│   │       └── services.yml
│   ├── Service
│   │   └── MyWorkflowService.php
│   ├── Tests
│   │   └── Integration
│   │       └── Service
│   │           └── MyWorkflowServiceTest.php
│   ├── WorkflowDefinition.php
│   ├── WorkflowExecution.php
│   └── WorkflowExecutionFactory.php
```
Important namespaces and classes:

* **WorkflowDefinition.php** - Here you define your workflow in the ezcWorkflow way (Define a workflow through php code instead of a simple YAML or XML way is a little bit odd, but I promise to make it up soon).
* **Service\MyWorkflowService.php** - Here you define your core functionality, that will be accessible by your **Action** classes. This class will be managed by Symfony's Dependency Injection Container, and it's id can be found on `Resources/config/services.yml`
* **GetWorkflowImageCommand.php** - This CLI Command exposes, in this case, `workflow-my-workflow:generate-workflow-image`, that generates a graphical representation of your workflow definition, like this:

![image](http://f.cl.ly/items/3H3J2T0L36010Y1b450M/my-workflow_workflow_20130421_210946.png)


## Customizing the workflow definition

Out of the box, after you generated your workflow, there is a workflow definition to exemplify how to do it in `WorkflowDefinition.php`. 

Your workflow business should reside on `MyWorkflow\Action` namespace. Just follow the examples and you will be good.

Change the `WorkflowDefinition` class according to your needs (check this [tutorial](http://zetacomponents.org/documentation/trunk/Workflow/tutorial.html) to know how to link your workflow nodes).

## Starting

Each workflow you generate has a service exposed on Symfony's Dependency Injection Container. Check it's name on your workflow's `services.yml` file. Supposing that it's name is `workflow.my_workflow`, to start it inside a Controller:

```
$this->get('workflow.my_workflow')->startNewWorkflowExecution();
```

## Resuming paused workflows

Workflows can be paused and resumed arbitrary. 

To pause it, inside your `Action` class `run` method, return `false` instead of `true`.

To resume it, run `yuriteixeira:workflow:resume`.

# Techinal Debits (you may help here!)

* Tests
* Build tasks (phpcs, phpmd, code coverage, etc)
* Add repo to Travis CI 
* Resume workflows through `resumeWorkflowExecution($executionId)` method of service
* CLI argument to resume only one workflow

# Future features

* Define workflow through a YAML config
* Be independent of workflow-database-tiein, that limits the options of database drivers (should use PDO/DBAL)
* Be independent of Sebastian Bergman's ezcWorkflow (it is awesome, but I would like to modernize and simplify it a lil bit)
