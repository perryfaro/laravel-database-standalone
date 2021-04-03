<?php

namespace LaravelDatabaseStandalone;

use Illuminate\Console\Application;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Database\ConnectionResolver;
use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Database\Migrations\DatabaseMigrationRepository;
use Illuminate\Database\Migrations\MigrationCreator;
use Illuminate\Database\Migrations\MigrationRepositoryInterface;
use Illuminate\Filesystem\Filesystem;

class ConsoleHelper
{

    /**
     * @var Application
     */
    protected $app = null;

    protected $commands = [
        \Illuminate\Database\Console\Factories\FactoryMakeCommand::class,
        \Illuminate\Database\Console\DumpCommand::class,
        \Illuminate\Database\Console\DbCommand::class,
        \Illuminate\Database\Console\WipeCommand::class,
        \Illuminate\Database\Console\Migrations\FreshCommand::class,
        \Illuminate\Database\Console\Migrations\InstallCommand::class,
        \Illuminate\Database\Console\Migrations\MigrateCommand::class,
        \Illuminate\Database\Console\Migrations\MigrateMakeCommand::class,
        \Illuminate\Database\Console\Migrations\RefreshCommand::class,
        \Illuminate\Database\Console\Migrations\ResetCommand::class,
        \Illuminate\Database\Console\Migrations\RollbackCommand::class,
        \Illuminate\Database\Console\Migrations\StatusCommand::class,
        \Illuminate\Database\Console\Seeds\SeedCommand::class,
        \Illuminate\Database\Console\Seeds\SeederMakeCommand::class,
    ];

    protected $container;

    protected $manager = null;

    protected $dispatcher = null;

    public function __construct()
    {
        $this->manager = Manager::getInstance();
        $this->container = $this->manager->getContainer();
        $this->dispatcher = $this->container->get('events');

        $this->manager->getDatabaseManager()->reconnect();
        $connectionResolver = new ConnectionResolver(
            $this->manager->getDatabaseManager()->getConnections()
        );
        $connectionResolver->setDefaultConnection($this->manager->getDatabaseManager()->getDefaultConnection());

        $this->container['db'] = $this->manager;

        $migrationRepository = new DatabaseMigrationRepository($connectionResolver, 'migrations');
        $migrationCreator = new MigrationCreator(new Filesystem(), __DIR__ . '/../migration');
        $this->container->instance(MigrationCreator::class, $migrationCreator);
        $this->container->instance(ConnectionResolverInterface::class, $connectionResolver);
        $this->container->instance(MigrationRepositoryInterface::class, $migrationRepository);

        $this->container->instance(Dispatcher::class, $this->dispatcher);

        $this->createApp();
    }

    public function handle($input, $output = null)
    {
        try {
            return $this->app->run($input, $output);
        } catch (\Exception $e) {

            echo $e->getMessage();

            //$this->reportException($e);

            //$this->renderException($output, $e);

            return 1;
        }
    }

    protected function createApp()
    {
        $this->app = (new Application($this->container, $this->dispatcher, '0.9.0'))
            ->resolveCommands($this->commands);
    }

}