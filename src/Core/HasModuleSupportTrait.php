<?php
declare(strict_types=1);

namespace Beauty\Module\Core;

trait HasModuleSupportTrait
{
    /**
     * @param string $modulesDir
     * @return array
     */
    function findModuleContainerClasses(string $modulesDir = 'modules'): array
    {
        $result = [];
        foreach (glob($modulesDir . '/*/src/Container/*.php') as $file) {
            $parts = explode(DIRECTORY_SEPARATOR, $file);

            $module = ucfirst($parts[1]);

            $class = basename($file, '.php');
            $fqcn = "Module\\$module\\Container\\$class";

            if (class_exists($fqcn)) {
                $result[] = $fqcn;
            }
        }

        return $result;
    }
}