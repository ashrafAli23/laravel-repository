<?php

namespace Laravel\Repository\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateRepositoryCommand extends Command
{
    protected $signature = 'make:repository {name : The name of the repository to create}';

    protected $description = 'Create a new repository and interface with the specified name';

    public function handle()
    {
        $name = $this->argument('name');

        $repoName = $name . 'Repository';
        $interfaceName = "I" . $repoName;

        $repoPath = app_path('Repositories/' . $repoName . '.php');
        $interfacePath = app_path('Repositories/' . 'Interfaces/' . $interfaceName . '.php');

        if (!file_exists("app/Repositories/Interfaces")) {
            mkdir("app/Repositories/Interfaces", 0777, true);
        }

        $repoContent = file_get_contents(__DIR__ . '/../Stubs/Repository.stub');
        $interfaceContent = file_get_contents(__DIR__ . '/../Stubs/IRepository.stub');

        $repoContent = str_replace(['{{repoName}}', '{{interfaceName}}'], [$repoName, $interfaceName], $repoContent);
        $interfaceContent = str_replace('{{interfaceName}}', $interfaceName, $interfaceContent);

        file_put_contents($repoPath, $repoContent);
        file_put_contents($interfacePath, $interfaceContent);

        $this->info("Repository $repoName and interface $interfaceName created successfully!");
    }
}
