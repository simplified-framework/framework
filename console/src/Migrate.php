<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 20.12.2015
 * Time: 21:11
 */

namespace Simplified\Console;

use Simplified\Config\Config;
use Simplified\Core\IllegalArgumentException;
use Simplified\Database\Connection;
use Simplified\Database\ConnectionException;
use Simplified\Database\Schema\Blueprint;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

function endsWith($haystack, $needle) {
    return $needle === "" ||
        (($temp = strlen($haystack) - strlen($needle)) >= 0 &&
            strpos($haystack, $needle, $temp) !== FALSE);
}

class Migrate extends Command {
    protected function configure() {
        $this
            ->setName('migrate')
            ->setDescription('migrate database')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $output->writeln('start migrating...');
        $migrations_path = APP_PATH . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        if (!file_exists($migrations_path)) // if dir doesn't exists, nothing is to migrate
            return;

        // get (default) database connection
        $conf = Config::getAll('database');
        $default_config = isset($conf['default']) ? $conf['default'] : null;
        if ($default_config == null)
            throw new ConnectionException('No default connection set');
        $conn = new Connection($default_config);

        // find migrations table. if something is going wrong
        // a exception is triggered
        $migrations = $conn->getDatabaseSchema()->table('migrations');
        if ($migrations == null) {
            $migrations = new Blueprint('migrations');
            $migrations->increments('id')
                ->string('name')->unique()
                ->timestamps()
                ->primary('id')
            ;

            // create table migrations
            // if something is going wrong a exception is triggered
            $migrations->build($conn);
        }

        $files = array();
        if ($handle = opendir($migrations_path)) {
            while (false !== ($entry = readdir($handle))) {
                if ($entry != "." && $entry != ".." && !is_dir($entry)) {
                    if (endsWith($entry, ".php")) {
                        $basename = basename($entry, '.php');
                        $ret = $conn->raw("select migration from `migrations` where migration='$basename' limit 1");
                        if ($ret != null && $ret->rowCount() == 1)
                            continue;
                        $files[] = $migrations_path . DIRECTORY_SEPARATOR . $entry;
                    }
                }
            }
            closedir($handle);
        } else {
            return;
        }

        foreach ($files as $file) {
            $content = file_get_contents($file);
            $lines = explode(PHP_EOL, $content);
            foreach ($lines as $line) {
                if (preg_match('/class[\s]+([a-zA-Z]+)/', $line, $matches)) {
                    include $file;

                    $clazz = $matches[1];
                    if (!class_exists($clazz))
                        throw new IllegalArgumentException("Class $clazz doesn't exists");

                    $instance = new $clazz();
                    if (!$instance instanceof MigrateInterface)
                        throw new IllegalArgumentException("Class $clazz doesn't implements MigrateInterface");

                    $instance->up();

                    // register migration in database
                    $conn->raw('insert into migrations (migration) VALUES ("'.basename($file, '.php').'")');
                    $output->writeln('Migrated table class ' . $clazz);
                }
            }
        }
        $output->writeln('finished migrating...');
    }
}