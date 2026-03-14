<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class PruneDbSessions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:prune-db-sessions {--dry : Show count only} {--chunk=1000 : Delete in batches}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete expired database sessions based on SESSION_LIFETIME (minutes)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $table = config('session.table', 'sessions');

        if (!Schema::hasTable($table)) {
            $this->error("Table [{$table}] does not exist.");
            return self::FAILURE;
        }

        // SESSION_LIFETIME is in minutes; sessions.last_activity stores a UNIX timestamp (seconds)
        $lifetimeMinutes = (int) config('session.lifetime', 120);
        $threshold = now()->subMinutes($lifetimeMinutes)->getTimestamp();

        if ($this->option('dry')) {
            $count = DB::table($table)->where('last_activity', '<', $threshold)->count();
            $this->line("Dry run: {$count} expired session(s) found in [{$table}].");
            return self::SUCCESS;
        }

        $chunk = max(1, (int) $this->option('chunk'));
        $totalDeleted = 0;

        // Delete in batches to avoid long-running deletes/locks
        do {
            $deleted = DB::table($table)
                ->where('last_activity', '<', $threshold)
                ->limit($chunk)
                ->delete();

            $totalDeleted += $deleted;
        } while ($deleted > 0);

        $this->info("Deleted {$totalDeleted} expired session(s) from [{$table}].");

        return self::SUCCESS;
    }
}
