# Beauty Framework: Module Support

## Installation

Add the package to your project via Composer:

```bash
composer require beauty-framework/module-support
```

## Quick Start: Module Generation

### 1. Generate a new module

Use the built-in command to generate a new module:

```bash
./beauty generate:module <module-name>
```

* `<module-name>` — your module name (in lowercase; dashes and underscores will automatically be converted to StudlyCase for the namespace).
* The module will appear in `modules/<module-name>`, with a ready-to-use `src/` structure and standard subfolders.

#### Example:

```bash
./beauty generate:module hello-world
```

This will create:

```
modules/hello-world/
 ├─ src/
 │   ├─ Controllers/
 │   ├─ Services/
 │   ├─ Repositories/
 │   ├─ Middlewares/
 │   ├─ Entities/
 │   ├─ DTO/
 │   ├─ Events/
 │   └─ Listeners/
 │   └─ Container/DI.php
 └─ composer.json
```

* The module's composer.json will contain a ready psr-4 autoloading config with the namespace `Module\HelloWorld\`.
* The root composer.json will be automatically patched with a path repository and dependency on your new module.

---

### 2. Register the module generation command

To make the `generate:module` command available for all CLI Beauty runs, register it in your config:

Open `config/commands.php` and add:

```php
return [
    ...
    \Beauty\Module\Console\RegisterCommands::commands(),
    ...
];
```

---

## Done!

Now you can create new modules with a single command. Beauty will handle the rest.

For custom templates or advanced file generation, see the "Advanced Usage" section in the upcoming documentation.
