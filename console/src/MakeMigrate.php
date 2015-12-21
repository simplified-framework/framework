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
use Symfony\Component\Console\Input\InputOption;
use Doctrine\Common\Inflector\Inflector;

class MakeMigrate extends Command {
    protected function configure() {
        $this
            ->setName('make:migrate')
            ->setDescription('migrate database (create script)')
            ->addArgument('script_name', InputArgument::REQUIRED, 'script name')
            ->addOption('table', 't', InputOption::VALUE_REQUIRED)
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $migrations_path = APP_PATH . 'database' . DIRECTORY_SEPARATOR . 'migrations';
        if (!file_exists($migrations_path)) {
            $output->writeln('creating directory ' . $migrations_path);
            mkdir($migrations_path, 0775, true);
        }

        $script = $input->getArgument('script_name');
        $table  = $input->getOption('table');
        $file = $migrations_path . DIRECTORY_SEPARATOR . time() . "_$script.php";
        $output->writeln('creating script ' . $file);

        $table = strtolower($table);
        $className = Inflector::classify($table);
        $fp = fopen($file, "w");
        fwrite($fp, '<?php '
            . PHP_EOL
            . PHP_EOL
            . "use Simplified\\DBAL\\Schema\\Schema;"
            . PHP_EOL
            . "use Simplified\\DBAL\\Schema\\Blueprint;"
            . PHP_EOL
            . "use Simplified\\Console\\MigrateInterface;"
            . PHP_EOL
            . PHP_EOL
            . "class {$className} extends MigrateInterface {"
            . PHP_EOL
            . "\tpublic function up() {"
            . PHP_EOL
            . "\t\t".'Schema::create(\''.$table.'\', function(Blueprint $bp) {'
            . PHP_EOL
            . "\t\t\t // TODO define table structure"
            . PHP_EOL
            . "\t\t});"
            . PHP_EOL
            . "\t}"
            . PHP_EOL
            . PHP_EOL
            . "\tpublic function down() {"
            . PHP_EOL
            . "\t\t".'Schema::drop("'.$table.'");'
            . PHP_EOL
            . "\t}" . PHP_EOL . "}" . PHP_EOL
        );
        fclose($fp);
    }
}