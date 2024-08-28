<?php

namespace App\Console\Commands;

use App\Models\Character\CharacterImage;
use App\Models\Character\CharacterImageSubtype;
use Illuminate\Console\Command;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class ConvertCharacterSubtype extends Command {
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'convert-character-subtype';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Converts the subtype_id columns in the character_images table to a new row in character_image_subtype table.';

    /**
     * Create a new command instance.
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle() {
        if (Schema::hasColumn('character_images', 'subtype_id')) {
            $check = $this->confirm('Do you have the second subtype extension installed?', true);
            if ($check) {
                $this->info('This command will need minor modifications to run correctly with this extension. Please see the comments in the file.');

                return;
            }

            // DESIGN UPDATES
            $updates = DB::table('design_updates')->where('subtype_id', '!=', null)->get();
            // make the string into an array
            foreach ($updates as $update) {
                $update->update([
                    'subtype_ids' => json_encode([$update->subtype_ids]),
                ]);
            }
            Schema::table('design_updates', function (Blueprint $table) {
                $table->dropColumn('subtype_id');
            });

            $characterImages = CharacterImage::whereNotNull('subtype_id')->get();

            $this->info('Converting '.count($characterImages).' character images to subtypes...');
            $bar = $this->output->createProgressBar(count($characterImages));
            foreach ($characterImages as $characterImage) {
                /*
                 * FOR THE SECOND SUBTYPE EXTENSION,
                 *
                 * You will need to create two characterImageSubtype records, one for each subtype.
                 * ex.
                 *
                 *  CharacterImageSubtype::create([
                 *    'character_image_id' => $characterImage->id,
                 *    'subtype_id' => $characterImage->subtype_one_id // or subtype_two_id
                 *  ]);
                 */
                CharacterImageSubtype::create([
                    'character_image_id' => $characterImage->id,
                    'subtype_id'         => $characterImage->subtype_id,
                ]);
                $bar->advance();
            }

            $bar->finish();
            $this->info('');

            $this->info('Dropping subtype_id column from character_images table...');

            /*
             * FOR THE SECOND SUBTYPE EXTENSION,
             *
             * You will need to drop both subtype columns from the character_images table.
             */
            Schema::table('character_images', function (Blueprint $table) {
                $table->dropColumn('subtype_id');
            });

            $this->info('Done!');
        } else {
            $this->info('This command will not execute, as it has already been run.');
        }
    }
}
