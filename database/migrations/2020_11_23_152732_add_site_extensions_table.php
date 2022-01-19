<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSiteExtensionsTable extends Migration
{
    /**
     * Run the migrations.
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
     */
    public function down()
    {
        Schema::dropIfExists('site_extensions');
    }
}
