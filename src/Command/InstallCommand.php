<?php

namespace WebSlinger\StoredProcedureFactory\Command;

use Composer\Script\Event;
use Composer\IO\IOInterface;

class InstallCommand
{
    public static function postInstall(Event $event): void
    {
        $io = $event->getIO();
        $vendorDir = $event->getComposer()->getConfig()->get('vendor-dir');
        $projectRoot = dirname($vendorDir);

        self::createConfigFile($io, $projectRoot);
        self::updateEnvFile($io, $projectRoot);
    }

    private static function createConfigFile(IOInterface $io, string $projectRoot): void
    {
        $configDir = $projectRoot . '/config/packages';
        $configFile = $configDir . '/web_slinger.yaml';

        if (!is_dir($configDir)) {
            if (!mkdir($configDir, 0755, true)) {
                $io->writeError('Could not create config directory: ' . $configDir);
                return;
            }
        }

        if (file_exists($configFile)) {
            $io->write('Configuration file already exists: ' . $configFile);
            return;
        }

        $configContent = self::getConfigTemplate();
        
        if (file_put_contents($configFile, $configContent) === false) {
            $io->writeError('Could not create configuration file: ' . $configFile);
            return;
        }

        $io->write('Created configuration file: ' . $configFile);
    }

    private static function updateEnvFile(IOInterface $io, string $projectRoot): void
    {
        $envFile = $projectRoot . '/.env';
        $envLocalFile = $projectRoot . '/.env.local';
        
        $envContent = self::getEnvTemplate();

        // Check if variables already exist in .env
        if (file_exists($envFile)) {
            $existingContent = file_get_contents($envFile);
            if (strpos($existingContent, 'WEB_SLINGER_SP_HOST') !== false) {
                $io->write('Environment variables already exist in .env file');
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
                $io->writeError('Could not update environment file: ' . $targetFile);
                return;
            }
        } else {
            if (file_put_contents($targetFile, $envContent) === false) {
                $io->writeError('Could not create environment file: ' . $targetFile);
                return;
            }
        }

        $io->write('Updated environment variables in: ' . $targetFile);
        $io->write('Please configure the following environment variables:');
        $io->write('  WEB_SLINGER_SP_HOST=your-database-server');
        $io->write('  WEB_SLINGER_SP_USERNAME=your-username');
        $io->write('  WEB_SLINGER_SP_PASSWORD=your-password');
    }

    private static function getConfigTemplate(): string
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

    private static function getEnvTemplate(): string
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