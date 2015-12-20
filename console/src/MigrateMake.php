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
use Symfony\Component\Console\Input\InputArgument;
use Simplified\Config\Config;

class MigrateMake extends Command {
    protected function configure() {
        $this
            ->setName('migrate:make')
            ->setDescription('migrate database (create script)')
            ->addArgument('table_name', InputArgument::REQUIRED, 'table name');
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $migrations_path = APP_PATH . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        if (!file_exists($migrations_path)) {
            $output->writeln('creating directory ' . $migrations_path);
            mkdir($migrations_path, 0775, true);
        }

        $table = $input->getArgument('table_name');
        $file = $migrations_path . DIRECTORY_SEPARATOR . time() . "_$table.php";
        $output->writeln('creating script ' . $file);

        $fp = fopen($file, "w");
        fwrite($fp, '<?php ' . PHP_EOL . "class XXX extends Migration {" . PHP_EOL . "\tpublic function up() {" . PHP_EOL . "\t}" . PHP_EOL . "}");
        fclose($fp);
    }
}