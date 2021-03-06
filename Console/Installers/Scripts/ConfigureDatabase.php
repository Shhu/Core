<?php namespace Modules\Core\Console\Installers\Scripts;

use Illuminate\Console\Command;
use Illuminate\Contracts\Config\Repository as Config;
use Modules\Core\Console\Installers\SetupScript;
use Modules\Core\Console\Installers\Writers\EnvFileWriter;

class ConfigureDatabase implements SetupScript
{
    /**
     * @var
     */
    protected $config;

    /**
     * @var EnvFileWriter
     */
    protected $env;

    /**
     * @param Config        $config
     * @param EnvFileWriter $env
     */
    public function __construct(Config $config, EnvFileWriter $env)
    {
        $this->config = $config;
        $this->env = $env;
    }

    /**
     * @var Command
     */
    protected $command;

    /**
     * Fire the install script
     * @param  Command $command
     * @return mixed
     */
    public function fire(Command $command)
    {
        $this->command = $command;

        $host = $this->askDatabaseHost();

        $name = $this->askDatabaseName();

        $user = $this->askDatabaseUsername();

        $password = $this->askDatabasePassword();

        $this->setLaravelConfiguration($name, $user, $password, $host);

        $this->env->write($name, $user, $password, $host);

        $command->info('Database successfully configured');
    }

    /**
     * @return string
     */
    protected function askDatabaseHost()
    {
        $host = $this->command->ask('Enter your database host', 'localhost');

        return $host;
    }

    /**
     * @return string
     */
    protected function askDatabaseName()
    {
        do {
            $name = $this->command->ask('Enter your database name');
            if ($name == '') {
                $this->command->error('Database name is required');
            }
        } while (!$name);

        return $name;
    }

    /**
     * @param
     * @return string
     */
    protected function askDatabaseUsername()
    {
        do {
            $user = $this->command->ask('Enter your database username', 'root');
            if ($user == '') {
                $this->command->error('Database username is required');
            }
        } while (!$user);

        return $user;
    }

    /**
     * @param
     * @return string
     */
    protected function askDatabasePassword()
    {
        return $this->command->secret('Enter your database password');
    }

    /**
     * @param $name
     * @param $user
     * @param $password
     */
    protected function setLaravelConfiguration($name, $user, $password, $host)
    {
        $this->config['database.connections.mysql.host'] = $host;
        $this->config['database.connections.mysql.database'] = $name;
        $this->config['database.connections.mysql.username'] = $user;
        $this->config['database.connections.mysql.password'] = $password;
    }
}
