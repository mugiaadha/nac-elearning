<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DatabaseOptimizeCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:optimize 
                            {--check : Check current database optimization status}
                            {--analyze : Analyze query performance}
                            {--indexes : Show all indexes}
                            {--foreign-keys : Show all foreign keys}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Database optimization tools and analysis';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🗄️  Database Optimization Tool');
        $this->newLine();

        if ($this->option('check')) {
            $this->checkOptimizationStatus();
        } elseif ($this->option('analyze')) {
            $this->analyzePerformance();
        } elseif ($this->option('indexes')) {
            $this->showIndexes();
        } elseif ($this->option('foreign-keys')) {
            $this->showForeignKeys();
        } else {
            $this->showMenu();
        }

        return 0;
    }

    /**
     * Show main menu
     */
    private function showMenu()
    {
        $choice = $this->choice('What would you like to do?', [
            'Check optimization status',
            'Analyze query performance',
            'Show all indexes',
            'Show foreign keys',
            'Run optimization migrations',
            'Exit'
        ]);

        switch ($choice) {
            case 'Check optimization status':
                $this->checkOptimizationStatus();
                break;
            case 'Analyze query performance':
                $this->analyzePerformance();
                break;
            case 'Show all indexes':
                $this->showIndexes();
                break;
            case 'Show foreign keys':
                $this->showForeignKeys();
                break;
            case 'Run optimization migrations':
                $this->runOptimizationMigrations();
                break;
            case 'Exit':
                $this->info('👋 Goodbye!');
                break;
        }
    }

    /**
     * Check current optimization status
     */
    private function checkOptimizationStatus()
    {
        $this->info('📊 Checking Database Optimization Status...');
        $this->newLine();

        $tables = [
            'users', 'courses', 'orders', 'payments', 'reviews', 
            'questions', 'coupons', 'wishlists', 'course_lectures',
            'course_sections', 'chat_messages', 'site_settings',
            'categories', 'sub_categories', 'blog_posts', 'blog_categories'
        ];

        $headers = ['Table', 'Indexes', 'Foreign Keys', 'Status'];
        $rows = [];

        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $indexes = $this->getTableIndexes($table);
                $foreignKeys = $this->getTableForeignKeys($table);
                
                $status = '✅ Optimized';
                if (count($indexes) < 2) {
                    $status = '⚠️ Needs Indexes';
                }

                $rows[] = [
                    $table,
                    count($indexes),
                    count($foreignKeys),
                    $status
                ];
            }
        }

        $this->table($headers, $rows);

        // Check if optimization migrations have been run
        $this->checkOptimizationMigrations();
    }

    /**
     * Analyze query performance
     */
    private function analyzePerformance()
    {
        $this->info('🔍 Analyzing Query Performance...');
        $this->newLine();

        // Test some common queries
        $queries = [
            'Active Courses' => "SELECT COUNT(*) FROM courses WHERE status = 1",
            'Featured Courses' => "SELECT COUNT(*) FROM courses WHERE status = 1 AND featured = '1'",
            'User Orders' => "SELECT COUNT(*) FROM orders WHERE user_id = 1",
            'Course Reviews' => "SELECT COUNT(*) FROM reviews WHERE course_id = 1 AND status = 'active'",
            'Site Settings' => "SELECT * FROM site_settings WHERE is_active = 1 LIMIT 1",
            'Active Users' => "SELECT COUNT(*) FROM users WHERE status = '1'",
        ];

        $headers = ['Query', 'Execution Time (ms)', 'Status'];
        $rows = [];

        foreach ($queries as $name => $sql) {
            try {
                $start = microtime(true);
                DB::select($sql);
                $end = microtime(true);
                
                $executionTime = round(($end - $start) * 1000, 2);
                $status = $executionTime < 10 ? '🟢 Fast' : ($executionTime < 50 ? '🟡 Medium' : '🔴 Slow');
                
                $rows[] = [$name, $executionTime, $status];
            } catch (\Exception $e) {
                $rows[] = [$name, 'Error', '❌ Failed'];
            }
        }

        $this->table($headers, $rows);
    }

    /**
     * Show all indexes
     */
    private function showIndexes()
    {
        $this->info('📋 Database Indexes Overview');
        $this->newLine();

        $tables = ['users', 'courses', 'orders', 'reviews', 'wishlists', 'site_settings'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("🗂️  Table: $table");
                $indexes = $this->getTableIndexes($table);
                
                if (empty($indexes)) {
                    $this->warn('   No indexes found');
                } else {
                    foreach ($indexes as $index) {
                        $this->line("   • {$index['Key_name']} ({$index['Column_name']}) - {$index['Index_type']}");
                    }
                }
                $this->newLine();
            }
        }
    }

    /**
     * Show foreign keys
     */
    private function showForeignKeys()
    {
        $this->info('🔗 Foreign Key Constraints');
        $this->newLine();

        $tables = ['courses', 'orders', 'reviews', 'wishlists', 'course_lectures'];
        
        foreach ($tables as $table) {
            if (Schema::hasTable($table)) {
                $this->info("🗂️  Table: $table");
                $foreignKeys = $this->getTableForeignKeys($table);
                
                if (empty($foreignKeys)) {
                    $this->warn('   No foreign keys found');
                } else {
                    foreach ($foreignKeys as $fk) {
                        $this->line("   • {$fk['COLUMN_NAME']} → {$fk['REFERENCED_TABLE_NAME']}.{$fk['REFERENCED_COLUMN_NAME']}");
                    }
                }
                $this->newLine();
            }
        }
    }

    /**
     * Run optimization migrations
     */
    private function runOptimizationMigrations()
    {
        $this->warn('⚠️  This will run database optimization migrations.');
        $this->warn('Make sure you have backed up your database first!');
        $this->newLine();

        if ($this->confirm('Do you want to continue?')) {
            $this->info('🚀 Running optimization migrations...');
            
            try {
                $this->call('migrate', [
                    '--path' => 'database/migrations/2025_01_22_000000_optimize_database_indexes_and_foreign_keys.php'
                ]);
                
                $this->call('migrate', [
                    '--path' => 'database/migrations/2025_01_22_000001_optimize_database_structure.php'
                ]);
                
                $this->call('migrate', [
                    '--path' => 'database/migrations/2025_01_22_000002_optimize_site_settings_table.php'
                ]);
                
                $this->info('✅ Optimization migrations completed successfully!');
                
            } catch (\Exception $e) {
                $this->error('❌ Migration failed: ' . $e->getMessage());
                $this->warn('Please check the error and try again.');
            }
        } else {
            $this->info('Operation cancelled.');
        }
    }

    /**
     * Get table indexes
     */
    private function getTableIndexes($table)
    {
        try {
            return DB::select("SHOW INDEX FROM `{$table}`");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Get table foreign keys
     */
    private function getTableForeignKeys($table)
    {
        try {
            return DB::select("
                SELECT 
                    COLUMN_NAME,
                    REFERENCED_TABLE_NAME,
                    REFERENCED_COLUMN_NAME
                FROM information_schema.KEY_COLUMN_USAGE 
                WHERE TABLE_SCHEMA = DATABASE() 
                AND TABLE_NAME = '{$table}' 
                AND REFERENCED_TABLE_NAME IS NOT NULL
            ");
        } catch (\Exception $e) {
            return [];
        }
    }

    /**
     * Check if optimization migrations have been run
     */
    private function checkOptimizationMigrations()
    {
        $this->newLine();
        $this->info('🔍 Checking Optimization Migrations Status...');

        $migrations = [
            '2025_01_22_000000_optimize_database_indexes_and_foreign_keys',
            '2025_01_22_000001_optimize_database_structure',
            '2025_01_22_000002_optimize_site_settings_table'
        ];

        foreach ($migrations as $migration) {
            $exists = DB::table('migrations')
                ->where('migration', $migration)
                ->exists();

            $status = $exists ? '✅ Applied' : '❌ Pending';
            $this->line("• $migration: $status");
        }

        if (!DB::table('migrations')->whereIn('migration', $migrations)->exists()) {
            $this->newLine();
            $this->warn('⚠️  Optimization migrations have not been run yet.');
            $this->info('Run: php artisan db:optimize and select "Run optimization migrations"');
        }
    }
}
