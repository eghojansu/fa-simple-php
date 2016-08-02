<?php

namespace commands\database;

use App;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;

class ResetCommand extends Command
{
    public function configure()
    {
        $this
            ->setName('database:reset')
            ->setDescription('Run drop, create, import and seed with single command')
            ;
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $app = $this->getApplication();
        $outputText = '';
        $commands = [
            'database:drop'=>[],
            'database:create'=>[],
            'database:import'=>[],
            'database:seed'=>[],
        ];

        foreach ($commands as $commandName => $arguments) {
            $i = new ArrayInput($arguments);
            $o = new BufferedOutput;
            $command = $app->find($commandName);
            $code = $command->run($i, $o);
            if ($code === 0) {
                $outputText .= $o->fetch();
            }
            else {
                break;
            }
        }

        $output->writeln("<fg=yellow>$outputText</>");
    }
}