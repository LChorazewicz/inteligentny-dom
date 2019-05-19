<?php

namespace App\Command;

use App\Repository\ConsumerRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class AppInstallCommand extends Command
{
    protected static $defaultName = 'refresh-system';

    /**
     * @var ConsumerRepository
     */
    private $consumer;

    protected function configure()
    {
        $this
            ->setDescription('sudo bin/console refresh-system')
        ;
    }

    public function __construct(ConsumerRepository $consumerRepository)
    {
        parent::__construct();
        $this->consumer = $consumerRepository;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $env = $_ENV['APP_ENV'];
        $supervisiorDir = $_ENV['SUPERVISOR_DIR'];
        if ($env && $supervisiorDir) {
            try{
                $io->block($this->clearLogs());
                $io->block($this->updateSupervisiorConfigFiles($supervisiorDir));
                $io->block($this->supervisorRestart());
                $io->block($this->addPermissions());
                $io->success('Installed!');
            }catch (\Throwable $throwable){
                $io->error($throwable->getMessage());
            }
        }

    }

    private function updateSupervisiorConfigFiles(string $supervisorDir): string
    {
        $consumers = $this->consumer->findAll();

        $projectDir = $_ENV['PROJECT_DIR'];
        $msg = '';
        foreach ($consumers as $consumer){
            $name = $consumer->getName();
            $quantity = $consumer->getProcessNumber();
            if($consumer->getStatus() === true){
                $configContent = <<<HEREDOC
[program:$name]
command=php bin/console rabbitmq:consumer -m 100 $name
directory=$projectDir
autostart=true
autorestart=true
startretries=5
startsecs=0
numprocs=$quantity
process_name=$name-%(process_num)s
stderr_logfile=$projectDir/var/log/supervisor.log
HEREDOC;
                file_put_contents($supervisorDir . $name . '.conf', $configContent);
                $msg = 'config added to ' . $supervisorDir . $name . '.conf';
            }elseif($consumer['status'] === 0){
                unlink($supervisorDir . $name . '.conf');
                $msg = 'config removed from ' . $supervisorDir . $name . '.conf';
            }
        }

        return $msg;
    }

    private function clearLogs(): string
    {
        $msg = '';
        $logDir = $GLOBALS['kernel']->getLogDir();
        if(is_dir($logDir)){
            $msg = exec("rm -rf $logDir/*");
        }
        return !empty($msg) ? $msg : 'Log directory cleared';
    }

    private function supervisorRestart(): string
    {
        $msg = exec('service supervisor restart');
        return !empty($msg) ? $msg : 'Supervisor service restated';
    }

    private function addPermissions()
    {
        $projectDir = $_ENV['PROJECT_DIR'];
        $msg = exec('chmod 777 ' . $projectDir . '/var/* && sudo chown www-data:www-data ' . $projectDir . '/var/* -R');
        return !empty($msg) ? $msg : 'Permissions added to ' . $projectDir . '/var/*';
    }
}
