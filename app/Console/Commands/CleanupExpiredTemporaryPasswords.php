<?php

namespace App\Console\Commands;

use App\Models\TemporaryPassword;
use Illuminate\Console\Command;

class CleanupExpiredTemporaryPasswords extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passwords:cleanup {--days=7 : Number of days to keep expired passwords}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Remove expired temporary passwords older than specified days';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $days = (int) $this->option('days');
        
        if ($days < 1) {
            $this->error('Days must be at least 1');
            return self::FAILURE;
        }

        $this->info("Cleaning up expired temporary passwords older than {$days} days...");

        $deletedCount = TemporaryPassword::where('expires_at', '<', now()->subDays($days))
                                        ->orWhere(function($query) use ($days) {
                                            $query->where('used', true)
                                                  ->where('used_at', '<', now()->subDays($days));
                                        })
                                        ->delete();

        if ($deletedCount > 0) {
            $this->info("Successfully deleted {$deletedCount} expired temporary password(s)");
        } else {
            $this->info('No expired temporary passwords found to delete');
        }

        return self::SUCCESS;
    }
}