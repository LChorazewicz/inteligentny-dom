<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppInstallApplicationCommand extends Command
{
    protected static $defaultName = 'app-install-application';

    protected function configure()
    {
        $this
            ->setDescription('Create app config - run with sudo privileges')
            ->addArgument('env', InputArgument::REQUIRED, 'Environtment - dev or prod')
            ->addArgument('supervisor-path', InputArgument::REQUIRED, 'location of supervisor dir /etc/supervisor/conf.d/')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $env = $input->getArgument('env');
        $supervisiorDir = $input->getArgument('supervisor-path');

        if ($env && $supervisiorDir) {
            try{
                $io->write($this->clearLogs());
                $this->updateSupervisiorConfigFiles($supervisiorDir);
                $io->write($this->supervisorRestart());
                $io->success('Installed!');
            }catch (\Throwable $throwable){
                $io->error($throwable->getMessage());
            }
        }

    }

    private function updateSupervisiorConfigFiles(string $supervisorDir): void
    {
        //todo: from db, current dir
        $consummers = [
            ['name' => 'device', 'status' => 1, 'quantity' => 1],
        ];

        foreach ($consummers as $consummer){
            $name = $consummer['name'];
            $quantity = $consummer['quantity'];
            if($consummer['status'] === 1){
                $configContent = <<<HEREDOC
[program:$name]
command=php bin/console rabbitmq:consumer $name
directory=/var/www/homesystem
autostart=true
numprocs=$quantity
process_name=$name-%(process_num)s
stderr_logfile=/var/www/homesystem/var/log/supervisor.log
HEREDOC;
                file_put_contents($supervisorDir . $name . '.conf', $configContent);
            }elseif($consummer['status'] === 0){
                unlink($supervisorDir . $name . '.conf');
            }
        }
    }

    private function clearLogs(): string
    {
        $msg = '';
        $logDir = $GLOBALS['kernel']->getLogDir();
        if(is_dir($logDir)){
            $msg = exec("rm -rf $logDir/*");
        }
        return $msg;
    }

    private function supervisorRestart(): string
    {
        return exec('service supervisor restart');
    }
}
