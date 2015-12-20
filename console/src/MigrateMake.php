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

class MigrateMake extends Command {
    protected function configure() {
        $this
            ->setName('migrate:make')
            ->setDescription('migrate database (create script)')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {

        $path = getcwd();
        $dbpath = $path . '/app/database/migrations';
        $output->writeln('create script in ' . $dbpath);
    }
}