<?php

namespace App\Console\Commands;

use DB;
use Carbon\Carbon;
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
        $updateDrops = CharacterDrop::requiresUpdate()->get();
        foreach ($updateDrops as $drop) {
            if((!isset($drop->dropData->cap) || $drop->dropData->cap == 0) || $drop->drops_available < $drop->dropData->cap)
            $drop->update([
                'drops_available' => $drop->drops_available += 1,
                'next_day' => Carbon::now()->add(
                    $drop->dropData->data['frequency']['frequency'],
                    $drop->dropData->data['frequency']['interval']
                )->startOf($drop->dropData->data['frequency']['interval'])
            ]);
        }
    }
}
