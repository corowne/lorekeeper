<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Carbon\Carbon;
use App\Models\Encounter\EncounterArea;

class UpdateTimedAreas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-timed-areas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Hides timed encounter areas, or sets active if ready.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //activate or deactivate the areas
        $hidearea = EncounterArea::where('is_active', 1)
            ->where('start_at', '<=', Carbon::now())
            ->where('end_at', '<=', Carbon::now())
            ->orWhere('is_active', 0)
            ->whereNull('start_at')
            ->where('end_at', '>=', Carbon::now())
            ->get();
        $showarea = EncounterArea::where('is_active', 0)
            ->where('start_at', '<=', Carbon::now())
            ->where('end_at', '>=', Carbon::now())
            ->orWhere('is_active', 0)
            ->where('start_at', '<=', Carbon::now())
            ->whereNull('end_at')
            ->get();
        //set area that should be active to active
        foreach ($showarea as $showarea) {
            $showarea->is_active = 1;
            $showarea->save();
        }
        //hide area that should be hidden now
        foreach ($hidearea as $hidearea) {
            $hidearea->is_active = 0;
            $hidearea->save();
        }
    }
}
