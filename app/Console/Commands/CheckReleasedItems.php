<?php

namespace App\Console\Commands;

use App\Models\Item\Item;
use App\Models\User\UserItem;
use Illuminate\Console\Command;

class CheckReleasedItems extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update-released-items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Checks unreleased items to see if they are or have been owned by at least one user, and updates them if so.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $this->info('*************************');
        $this->info('* UPDATE RELEASED ITEMS *');
        $this->info('*************************'."\n");

        $this->line('Searching for released items...');

        $userItems = UserItem::pluck('item_id')->unique()->toArray();
        $releasedItems = Item::where('is_released', 0)->whereIn('id', $userItems);

        $this->info('Updating items...');
        $releasedItems->update(['is_released' => 1]);
        $this->info('Updated items.');

        return 0;
    }
}
