<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDesignUpdates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('design_updates', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('character_id')->unsigned()->index();
            $table->enum('status', ['Draft', 'Pending', 'Accepted', 'Rejected'])->default('Draft');

            $table->integer('user_id')->unsigned()->index();
            $table->integer('staff_id')->unsigned()->nullable()->default(null);
            
            $table->text('comments')->nullable()->default(null);
            $table->text('staff_comments')->nullable()->default(null);

            // Items are attached by stack - currencies are listed in data
            // This will be non-standard in terms of formatting, since we have to note down
            // whether the currencies came from the user or character
            $table->string('data', 512)->nullable()->default(null);
            
            $table->string('extension', 5)->nullable()->default(null);
            $table->boolean('use_cropper')->default(0);
            $table->integer('x0')->nullable()->default(null);
            $table->integer('x1')->nullable()->default(null);
            $table->integer('y0')->nullable()->default(null);
            $table->integer('y1')->nullable()->default(null);
            $table->string('hash', 10);

            // These are required in the final submission, 
            // but for the sake of incremental editing they'll be nullable in the meantime
            $table->integer('species_id')->unsigned()->nullable();
            $table->integer('rarity_id')->unsigned()->nullable();

            // We're going to use this for the UI - since we're breaking down the editing by parts, 
            // we want to make it easy for users to know if they've missed a section.
            // Additionally, validation can be done per section, so submitting the final update is simple.
            $table->boolean('has_comments')->default(0);
            $table->boolean('has_image')->default(0);
            $table->boolean('has_addons')->default(0);
            $table->boolean('has_features')->default(0);

            $table->timestamps();
            $table->softDeletes();
        });
        
        // Add a type to the features, so features can be attached to design updates
        // We'll still use the character image column for the design updates -
        // it should probably be renamed to be more appropriate though
        Schema::table('character_features', function(Blueprint $table) {
            $table->enum('character_type', ['Character', 'Update'])->default('Character');
        });
        
        // Same here, the advantage of doing this is that after everything is good
        // we only have to swap the type over to "Character" and update the ID
        // and it's good to go
        Schema::table('character_image_creators', function(Blueprint $table) {
            $table->enum('character_type', ['Character', 'Update'])->default('Character');
        });

        // Add a holding ID and holding type - in the future when we attach
        // item stacks to secure trade lots, this will also be used
        // When the item is released from a design update or trade lot that has been
        // deleted, it'll be returned to the user ID on the stack
        Schema::table('user_items', function(Blueprint $table) {
            $table->enum('holding_type', ['Update', 'Trade'])->nullable()->default(null);
            $table->integer('holding_id')->unsigned()->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        Schema::table('user_items', function(Blueprint $table) {
            $table->dropColumn('holding_type');
            $table->dropColumn('holding_id');
        });
        
        Schema::table('character_image_creators', function(Blueprint $table) {
            $table->dropColumn('character_type');
        });
        
        Schema::table('character_features', function(Blueprint $table) {
            $table->dropColumn('character_type');
        });

        Schema::dropIfExists('design_update_features');
        Schema::dropIfExists('design_updates');
    }
}
