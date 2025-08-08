<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Equipping;

class CleanOrphanedEquippings extends Command
{
    protected $signature = 'equipping:clean-orphans';
    protected $description = 'Delete Equipping records where the related Leader or CellMember is missing.';

    public function handle()
    {
        $orphans = Equipping::all()->filter(function ($equipping) {
            return !$equipping->equippable;
        });
        $count = $orphans->count();
        if ($count > 0) {
            $orphans->each->delete();
            $this->info("Deleted {$count} orphaned Equipping records.");
        } else {
            $this->info('No orphaned Equipping records found.');
        }
        return 0;
    }
}
