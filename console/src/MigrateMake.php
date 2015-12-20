<?php
/**
 * Created by PhpStorm.
 * User: Andreas
 * Date: 20.12.2015
 * Time: 21:11
 */

namespace Simplified\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Simplified\Config\Config;

define ("BASE_PATH",   getcwd() . DIRECTORY_SEPARATOR);
define ("VENDOR_PATH", BASE_PATH . "vendor" . DIRECTORY_SEPARATOR);
define ("PUBLIC_PATH", BASE_PATH . "public" . DIRECTORY_SEPARATOR);
define ("APP_PATH", BASE_PATH . "app" . DIRECTORY_SEPARATOR);
define ("STORAGE_PATH", APP_PATH . "storage" . DIRECTORY_SEPARATOR);
define ("I18N_PATH", APP_PATH . "i18n" . DIRECTORY_SEPARATOR);
define ("RESOURCES_PATH", APP_PATH . "resources" . DIRECTORY_SEPARATOR);
define ("RESOURCES_VENDOR_PATH", RESOURCES_PATH . "vendor" . DIRECTORY_SEPARATOR);
define ("CONFIG_PATH", APP_PATH . "config" . DIRECTORY_SEPARATOR);

class MigrateMake extends Command {
    protected function configure() {
        $this
            ->setName('migrate:make')
            ->setDescription('migrate database (create script)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $migrations_path = APP_PATH . 'database/migrations';
        if (file_exists($migrations_path)) {
            $output->writeln('create script in ' . $migrations_path);
        } else {
            $output->writeln('unable to create script in ' . $migrations_path . ": not a directory");
        }
    }
}