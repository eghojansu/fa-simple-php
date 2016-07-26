<?php

namespace commands\database;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App;
use BatchInsert;

class SeedCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('database:seed')
            ->setDescription('Seeding database content')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app = App::instance()->debug();
        $db = $app->service('database');
        $recordInserted = 0;
        $tableCounter = 0;

        // ---------------------------------------------------------------------

        // insert user
        $tableCounter++;
        $batch = $app->service('batchInsert', ['user']);
        $db->pdo()->exec('alter table user auto_increment = 1');
        for ($i=0; $i < 100; $i++) {
            $data = [];
            $data['name'] = 'User '.$i;
            $data['username'] = 'user-'.$i;
            $data['password'] = $data['username'];
            $batch->add($data);
        }
        $recordInserted += $batch->execute()?$batch->count():0;

        // ---------------------------------------------------------------------

        $error = trim($db->getError($asString = true, $delimiter = PHP_EOL));
        if ($error) {
            $output->writeln("<error>\n$error\n</error>");
        }
        $output->writeln('<info>'.PHP_EOL.$db->getLog().PHP_EOL.'</info>', OutputInterface::VERBOSITY_DEBUG);

        $output->writeln("  <fg=yellow>$recordInserted record(s) inserted on $tableCounter table(s)</>");
    }
}