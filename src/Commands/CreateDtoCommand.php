<?php

namespace Laravel\Repository\Commands;

use Illuminate\Console\Command;

class CreateDtoCommand extends Command
{
    protected $signature = 'make:dto {name : The name of the dto to create}';

    protected $description = 'Create a new dto with the specified name';

    public function handle()
    {
        $name = $this->argument('name');

        $repoName = $name . 'Dto';

        $repoPath = app_path('Dto/' . $repoName . '.php');

        if (!file_exists("app/Dto")) {
            mkdir("app/Dto", 0777, true);
        }

        $repoContent = file_get_contents(__DIR__ . '/../Stubs/Dto.stub');
        $repoContent = str_replace(['{{name}}'], [$repoName], $repoContent);

        file_put_contents($repoPath, $repoContent);

        $this->info("Dto $repoName created successfully!");
    }
}
