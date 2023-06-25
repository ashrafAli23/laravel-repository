<?php

namespace Laravel\Repository\Commands;

use Illuminate\Console\Command;

class CreateServiceCommand extends Command
{
    protected $signature = 'make:service {name : The name of the service to create}';

    protected $description = 'Create a new service with the specified name';
    public function handle()
    {
        $name = $this->argument('name');

        $repoName = $name . 'Service';

        $repoPath = app_path('Services/' . $repoName . '.php');

        if (!file_exists("app/Services")) {
            mkdir("app/Services", 0777, true);
        }

        $repoContent = file_get_contents(__DIR__ . '/../Stubs/Service.stub');
        $repoContent = str_replace(['{{interfaceName}}'], ["I" . $name . "Repository"], $repoContent);

        file_put_contents($repoPath, $repoContent);

        $this->info("Service $repoName created successfully!");
    }
}
