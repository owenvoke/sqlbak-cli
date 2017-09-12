<?php

namespace pxgamer\SQLBak;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class BackupCommand
 * @package pxgamer\SQLBak
 */
class BackupCommand extends Command
{
    /**
     * @var object|Config
     */
    private $config;
    /**
     * @var Backup
     */
    private $backup;

    /**
     * Configure the command options.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('backup')
            ->setDescription('Backup an array of databases.')
            ->addOption('config', 'c', InputOption::VALUE_OPTIONAL, 'Path to a configuration file.')
            ->addOption('username', 'u', InputOption::VALUE_OPTIONAL, 'The username for running the backups.')
            ->addOption('password', 'p', InputOption::VALUE_OPTIONAL, 'The password for running the backups.')
            ->addOption('outputDirectory', 'o', InputOption::VALUE_OPTIONAL, 'A directory for outputting the backups.')
            ->addOption('compress', null, InputOption::VALUE_OPTIONAL, 'Choose whether to compress the SQL files.', true)
            ->addOption('databases', 'd', InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY, 'A list of databases.', []);
    }

    /**
     * Execute the command.
     *
     * @param  \Symfony\Component\Console\Input\InputInterface   $input
     * @param  \Symfony\Component\Console\Output\OutputInterface $output
     * @return void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        if ($input->getOption('config')) {
            $this->config = new Config($input->getOption('config'));
        } elseif ($this->validateInput($input)) {

        } else {
            throw new \Exception('Missing required config parameters.');
        }

        $username = $input->getOption('username') ?? $this->config->username ?? null;
        $password = $input->getOption('password') ?? $this->config->password ?? null;

        $config = [
            'compress' => $input->getOption('compress') ?? $this->config->compress ?? true,
            'tmpPath'  => $this->config->tmpPath ?? sys_get_temp_dir(),
        ];

        $this->backup = new Backup($username, $password, $config);
        $databases = $this->config->databases ?? $input->getOption('databases') ?? [];

        foreach ($databases as $database) {
            $this->backup->addDatabase($database);
        }

        $output->writeln([
            '<comment>Backing up databases:</comment>',
            '<comment>-------------------------------------------------------------</comment>',
            ''
        ]);

        $results = $this->backup->execute();

        $table = new Table($output);

        $table
            ->setHeaders(['Database', 'Status'])
            ->setRows($results);

        $table->render();
    }

    /**
     * @param InputInterface $input
     * @return bool
     */
    private function validateInput($input)
    {
        if (
            $input->getOption('username')
            && $input->getOption('password')
            && $input->getOption('databases')
        ) {
            return true;
        }

        return false;
    }
}