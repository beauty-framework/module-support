<?php
declare(strict_types=1);

namespace Beauty\Module\Console\Commands\Generate;

use Beauty\Cli\CLI;
use Beauty\Cli\CliOutput;
use Beauty\Cli\Console\AbstractCommand;

class ModuleCommand extends AbstractCommand
{

    /**
     * @return string
     */
    public function name(): string
    {
        return 'generate:module';
    }

    /**
     * @return string|null
     */
    public function description(): string|null
    {
        return 'Create a new module';
    }

    /**
     * @param array $args
     * @return int
     */
    public function handle(array $args): int
    {
        $name = $args[0] ?? null;

        if (!$name) {
            CliOutput::error('Module name is required');
            return CLI::FAILURE;
        }

        $moduleDirName = mb_strtolower($name);
        $moduleNamespaceName = $this->studly($moduleDirName);

        $moduleDir = "modules/{$moduleDirName}";
        $srcDir = "{$moduleDir}/src";
        $composerFile = "{$moduleDir}/composer.json";

        $namespace = "Module\\{$moduleNamespaceName}\\";

        $moduleComposer = [
            'name' => "module/$moduleDirName",
            'type' => 'library',
            'version' => '1.0.0',
            'autoload' => [
                'psr-4' => [
                    $namespace => 'src/'
                ]
            ]
        ];

        mkdir($srcDir, 0777, true);

        $defaultFolders = [
            'Controllers',
            'Services',
            'Repositories',
            'Middlewares',
            'Entities',
            'DTO',
            'Events',
            'Listeners',
            'Jobs',
            'Container',
        ];

        foreach ($defaultFolders as $folder) {
            $structureDir = "$srcDir/$folder";
            mkdir($structureDir, 0777, true);

            if ($folder == 'Container') {
                continue;
            }

            file_put_contents($structureDir.'/.gitkeep', '');
        }

        $stubDiPath = dirname(__DIR__, 4) . '/stubs/di.stub';

        $stubDi = file_get_contents($stubDiPath);
        $stubDi = str_replace(
            '{{ namespace }}',
            $namespace,
            $stubDi
        );

        file_put_contents("$srcDir/Container/DI.php", $stubDi);

        $this->saveComposerJson($composerFile, $moduleComposer);

        $rootComposerPath = base_path() . '/composer.json';
        $composer = json_decode(file_get_contents($rootComposerPath), true);

        $composer['require']["module/$moduleDirName"] = "*";

        $hasPathRepo = false;
        if (isset($composer['repositories'])) {
            foreach ($composer['repositories'] as $repo) {
                if ($repo['type'] === 'path' && $repo['url'] === 'modules/*') {
                    $hasPathRepo = true;
                    break;
                }
            }
        } else {
            $composer['repositories'] = [];
        }

        if (!$hasPathRepo) {
            $composer['repositories'][] = [
                'type' => 'path',
                'url' => 'modules/*'
            ];
        }

        $this->saveComposerJson($rootComposerPath, $composer);

        CliOutput::success('Module created successfully');
        CliOutput::line('Run this command for implementation module:');
        CliOutput::info('composer update');

        return CLI::SUCCESS;
    }

    /**
     * @param string $str
     * @return string
     */
    protected function studly(string $str): string
    {
        return str_replace(' ', '', ucwords(str_replace(['-', '_'], ' ', $str)));
    }

    /**
     * @param string $path
     * @param array|object $composer
     * @return void
     */
    protected function saveComposerJson(string $path, array|object $composer): void
    {
        $json = json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        $json = preg_replace_callback('/^( +)/m', function ($m) {
            return str_repeat(' ', intdiv(strlen($m[1]), 2));
        }, $json);

        file_put_contents($path, $json);
    }
}