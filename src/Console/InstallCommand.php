<?php

declare(strict_types=1);

namespace Accelade\Console;

use Illuminate\Console\Command;

class InstallCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'accelade:install
                            {--framework=vue : The frontend framework to use (vue or react)}';

    /**
     * The console command description.
     */
    protected $description = 'Install Accelade and configure your application';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $framework = $this->option('framework');

        if (! in_array($framework, ['vue', 'react'], true)) {
            $this->error('Invalid framework. Choose "vue" or "react".');

            return self::FAILURE;
        }

        $this->info('Installing Accelade...');

        // Publish config
        $this->call('vendor:publish', [
            '--tag' => 'accelade-config',
            '--force' => true,
        ]);

        // Update the config with chosen framework
        $this->updateConfig($framework);

        $this->newLine();
        $this->info('Accelade installed successfully!');
        $this->newLine();

        $this->line('Add the following to your Blade layout:');
        $this->newLine();
        $this->line('  <head>');
        $this->line('      @acceladeStyles');
        $this->line('  </head>');
        $this->line('  <body>');
        $this->line('      <!-- Your content -->');
        $this->line('      @acceladeScripts');
        $this->line('  </body>');
        $this->newLine();

        $this->line('Example component usage:');
        $this->line('  <x-accelade:counter :initial-count="0" />');
        $this->newLine();

        return self::SUCCESS;
    }

    /**
     * Update the config file with the chosen framework.
     */
    protected function updateConfig(string $framework): void
    {
        $configPath = config_path('accelade.php');

        if (file_exists($configPath)) {
            $content = file_get_contents($configPath);
            $content = preg_replace(
                "/'framework' => env\('ACCELADE_FRAMEWORK', '.*?'\)/",
                "'framework' => env('ACCELADE_FRAMEWORK', '{$framework}')",
                $content
            );
            file_put_contents($configPath, $content);
        }

        $this->info("Configured to use {$framework} framework.");
    }
}
