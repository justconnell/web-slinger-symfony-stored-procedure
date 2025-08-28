<?php

namespace WebSlinger\StoredProcedureFactory\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'web-slinger:setup',
    description: 'Set up the WebSlinger Stored Procedure Bundle configuration files',
)]
class InstallCommand extends Command
{
    public function __construct(private string $projectDir)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        
        $io->title('WebSlinger Stored Procedure Bundle Setup');

        $this->createConfigFile($io);
        $this->updateEnvFile($io);
        
        $io->success('WebSlinger Stored Procedure Bundle has been configured successfully!');
        $io->note([
            'Please configure the following environment variables in your .env file:',
            '  WEB_SLINGER_SP_HOST=your-database-server',
            '  WEB_SLINGER_SP_USERNAME=your-username',
            '  WEB_SLINGER_SP_PASSWORD=your-password'
        ]);
        
        return Command::SUCCESS;
    }

    private function createConfigFile(SymfonyStyle $io): void
    {
        $configDir = $this->projectDir . '/config/packages';
        $configFile = $configDir . '/web_slinger.yaml';

        if (!is_dir($configDir)) {
            if (!mkdir($configDir, 0755, true)) {
                $io->error('Could not create config directory: ' . $configDir);
                return;
            }
        }

        if (file_exists($configFile)) {
            $io->note('Configuration file already exists: ' . $configFile);
            return;
        }

        $configContent = $this->getConfigTemplate();
        
        if (file_put_contents($configFile, $configContent) === false) {
            $io->error('Could not create configuration file: ' . $configFile);
            return;
        }

        $io->text('✓ Created configuration file: ' . $configFile);
    }

    private function updateEnvFile(SymfonyStyle $io): void
    {
        $envFile = $this->projectDir . '/.env';
        $envLocalFile = $this->projectDir . '/.env.local';
        
        $envContent = $this->getEnvTemplate();

        // Check if variables already exist in .env
        if (file_exists($envFile)) {
            $existingContent = file_get_contents($envFile);
            if (strpos($existingContent, 'WEB_SLINGER_SP_HOST') !== false) {
                $io->note('Environment variables already exist in .env file');
                return;
            }
        }

        // Try to append to .env.local first, then .env
        $targetFile = file_exists($envLocalFile) ? $envLocalFile : $envFile;
        
        if (file_exists($targetFile)) {
            $existingContent = file_get_contents($targetFile);
            // Add newline if file doesn't end with one
            if (!empty($existingContent) && substr($existingContent, -1) !== "\n") {
                $envContent = "\n" . $envContent;
            }
            
            if (file_put_contents($targetFile, $envContent, FILE_APPEND | LOCK_EX) === false) {
                $io->error('Could not update environment file: ' . $targetFile);
                return;
            }
        } else {
            if (file_put_contents($targetFile, $envContent) === false) {
                $io->error('Could not create environment file: ' . $targetFile);
                return;
            }
        }

        $io->text('✓ Updated environment variables in: ' . $targetFile);
    }

    private function getConfigTemplate(): string
    {
        return <<<YAML
# WebSlinger Stored Procedure Bundle Configuration
web_slinger:
    stored_procedure:
        hostname: '%env(WEB_SLINGER_SP_HOST)%'
        username: '%env(WEB_SLINGER_SP_USERNAME)%'
        password: '%env(WEB_SLINGER_SP_PASSWORD)%'
YAML;
    }

    private function getEnvTemplate(): string
    {
        return <<<ENV

###> web-slinger/stored-procedure-bundle ###
# Configure your SQL Server connection
WEB_SLINGER_SP_HOST=your-database-server
WEB_SLINGER_SP_USERNAME=your-username
WEB_SLINGER_SP_PASSWORD=your-password
###< web-slinger/stored-procedure-bundle ###
ENV;
    }
}