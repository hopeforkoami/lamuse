<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Config\Database;
use Illuminate\Database\Schema\Blueprint;

class MigrationRunner
{
    private $capsule;
    private $schema;
    private $migrationsPath;

    public function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->safeLoad();

        $this->capsule = Database::init();
        $this->schema = $this->capsule->schema();
        $this->migrationsPath = __DIR__ . '/list';

        $this->ensureMigrationsTableExists();
    }

    private function ensureMigrationsTableExists()
    {
        if (!$this->schema->hasTable('migrations')) {
            $this->schema->create('migrations', function (Blueprint $table) {
                $table->id();
                $table->string('migration');
                $table->integer('batch');
                $table->timestamp('executed_at')->useCurrent();
            });
            echo "Created 'migrations' table.\n";
        }
    }

    public function runAll()
    {
        $executed = $this->getExecutedMigrations();
        $files = $this->getMigrationFiles();
        $batch = $this->getNextBatchNumber();

        $count = 0;
        foreach ($files as $file) {
            $migrationName = basename($file, '.php');
            if (!in_array($migrationName, $executed)) {
                $this->runMigration($file, $migrationName, $batch);
                $count++;
            }
        }

        if ($count === 0) {
            echo "No pending migrations to run.\n";
        } else {
            echo "Successfully ran $count migrations.\n";
        }
    }

    public function runFile($fileName)
    {
        $filePath = $this->migrationsPath . '/' . $fileName;
        if (!file_exists($filePath)) {
            $filePath .= '.php';
        }

        if (!file_exists($filePath)) {
            echo "Error: Migration file not found: $fileName\n";
            return;
        }

        $migrationName = basename($filePath, '.php');
        $executed = $this->getExecutedMigrations();

        if (in_array($migrationName, $executed)) {
            echo "Migration '$migrationName' has already been executed.\n";
            return;
        }

        $batch = $this->getNextBatchNumber();
        $this->runMigration($filePath, $migrationName, $batch);
        echo "Successfully ran migration: $migrationName\n";
    }

    public function status()
    {
        $executed = $this->getExecutedMigrations();
        $files = $this->getMigrationFiles();

        echo str_pad("Migration", 60) . "Status\n";
        echo str_repeat("-", 70) . "\n";

        foreach ($files as $file) {
            $name = basename($file, '.php');
            $status = in_array($name, $executed) ? "Executed" : "Pending";
            echo str_pad($name, 60) . $status . "\n";
        }
    }

    private function runMigration($filePath, $migrationName, $batch)
    {
        echo "Running migration: $migrationName... ";
        $migration = require $filePath;

        try {
            $this->capsule->getConnection()->statement('SET FOREIGN_KEY_CHECKS = 0');
            $migration->up($this->schema);
            $this->capsule->getConnection()->statement('SET FOREIGN_KEY_CHECKS = 1');

            $this->capsule->table('migrations')->insert([
                'migration' => $migrationName,
                'batch' => $batch
            ]);
            echo "DONE\n";
        } catch (\Exception $e) {
            echo "FAILED\n";
            echo "Error: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    private function getExecutedMigrations()
    {
        return $this->capsule->table('migrations')->pluck('migration')->toArray();
    }

    private function getMigrationFiles()
    {
        $files = glob($this->migrationsPath . '/*.php');
        sort($files);
        return $files;
    }

    private function getNextBatchNumber()
    {
        return ($this->capsule->table('migrations')->max('batch') ?? 0) + 1;
    }
}

// CLI Argument Handling
if (php_sapi_name() === 'cli') {
    $runner = new MigrationRunner();
    $args = $argv;
    array_shift($args); // remove script name

    if (empty($args)) {
        echo "Usage: php runner.php [command] [options]\n";
        echo "Commands:\n";
        echo "  --all              Run all pending migrations\n";
        echo "  --file=<name>      Run a specific migration file\n";
        echo "  --status           Show the status of all migrations\n";
        exit;
    }

    foreach ($args as $arg) {
        if ($arg === '--all') {
            $runner->runAll();
        } elseif (strpos($arg, '--file=') === 0) {
            $fileName = substr($arg, 7);
            $runner->runFile($fileName);
        } elseif ($arg === '--status') {
            $runner->status();
        } else {
            echo "Unknown argument: $arg\n";
        }
    }
}
