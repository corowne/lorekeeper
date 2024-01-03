<?php

namespace App\Console\Commands;

use App\Models\Character\CharacterImage;
use Illuminate\Console\Command;

class FillExistingFullsizeExtensions extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fill-character-fullsize-extensions';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Supplies file extension information for any stored masterlist full-size images from the existing stored extension.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle() {
        $images = CharacterImage::whereNotNull('fullsize_hash')->whereNull('fullsize_extension')->get();

        if ($images->count()) {
            $this->info('Processing '.$images->count().' images...');
            foreach ($images as $image) {
                $image->update(['fullsize_extension' => $image->extension]);
            }

            $this->info('Done!');
        } else {
            $this->line('No images need processing!');
        }

        return Command::SUCCESS;
    }
}
