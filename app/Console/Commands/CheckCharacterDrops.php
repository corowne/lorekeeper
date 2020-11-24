<?php

namespace App\Console\Commands;

use Carbon;
use App\Models\Character\CharacterDrop;

use Illuminate\Console\Command;
use App\Services\NewsService;

class CheckCharacterDrops extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check-character-drops';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks if there are any character drops to update.';

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
     * @return mixed
     */
    public function handle()
    {
        //
        $updateDrops = CharacterDrop::requiresUpdate();
        foreach ($updateDrops as $drop) {
            $frequency = $drop->dropData->data['frequency']['frequency'];
            $interval = $drop->dropData->data['frequency']['interval'];
            $drop->update([
                'available_drops' => $drop->available_drops + 1,
                'next_day' => Carbon::now()->add(
                    $frequency,
                    $drop->dropData->data['frequency']['interval']
                )
            ]);
        }
    }
}
