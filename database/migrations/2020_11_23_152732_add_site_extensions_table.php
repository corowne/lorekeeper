<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSiteExtensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('site_extensions', function (Blueprint $table) {
            $table->engine = 'InnoDB';

            $table->string('key', 50)->unique()->primary(); // The name of the extension, styled as "extension_name"
            $table->text('wiki_key')->nullable()->default(null); // The part after "title=Extensions:" in the wiki links.
            $table->text('creators'); // Credit extension creators and collaborators
            $table->string('version')->default('1.0.0'); // Versions - defaults to 1.0.0
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('site_extensions');
    }
}
