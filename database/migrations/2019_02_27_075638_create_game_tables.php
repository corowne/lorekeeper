<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Tables for actual game use.
        // Most of these have a description field - these are intended for short descriptions, like
        // you would find in a tooltip, and should contain a link out to more detailed explanations
        // if necessary.

        // Create rarity tables ///////////////////////////////////////////////////////////////////

        // Some games will have different systems for rarity, but here's an all-purpose setup
        Schema::create('rarities', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('name');
            $table->string('description', 512);
            $table->integer('sort')->unsigned()->default(0);
            $table->string('color', 6)->nullable();
            $table->boolean('has_image')->default(0);
        });

        // Create item tables /////////////////////////////////////////////////////////////////////

        // Item categories are for sorting types of items in the inventory
        Schema::create('item_categories', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            
            $table->string('name');
            $table->string('description', 512);
            $table->integer('sort')->unsigned()->default(0);
        });

        Schema::create('items', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('item_category_id')->unsigned()->nullable()->default(null);
            // For the sake of saving some headaches, item category can be left empty (which will put them in a generic "miscellaneous" category)
            
            $table->string('name');
            $table->string('description', 512);
            $table->text('specifications')->nullable()->default(null);
            $table->boolean('has_image')->default(0);

            $table->foreign('item_category_id')->references('id')->on('item_categories');
        });

        Schema::create('inventory', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->integer('user_id')->unsigned();

            $table->integer('count')->unsigned()->default(1);

            $table->string('data', 1024)->nullable(); // includes information like staff notes, etc.

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('user_id')->references('id')->on('users');
        });

        Schema::create('inventory_log', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->integer('count')->unsigned()->default(1);
            $table->integer('stack_id')->unsigned();

            $table->integer('sender_id')->unsigned()->nullable();
            $table->integer('recipient_id')->unsigned()->nullable();
            $table->string('log'); // Actual log text
            $table->string('log_type'); // Indicates what type of transaction the item was used in
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('stack_id')->references('id')->on('inventory');

            $table->foreign('sender_id')->references('id')->on('users');
            $table->foreign('recipient_id')->references('id')->on('users');
        });

        // Create species tables //////////////////////////////////////////////////////////////////

        // These are named "species", but can also be used for making subspecies 
        // (just change the wording on the views)
        // I'll add a recipe to add a category above species so that multiple species
        // with subspecies can be done, it just appears to be uncommon in ARPGs to
        // have so many levels of options at once.
        Schema::create('specieses', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->string('name');
            $table->string('description', 512);
            $table->text('specifications')->nullable()->default(null);
            $table->boolean('has_image')->default(0);
            $table->integer('sort')->unsigned()->default(0);
        });
        // "Trait" is a reserved keyword, so traits are going to be called "features" instead
        Schema::create('feature_categories', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('name');
            $table->string('description', 512);
            $table->boolean('has_image')->default(0);
            $table->integer('sort')->unsigned()->default(0);
        });
        Schema::create('features', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            // Once again, this can be left blank to put it in a miscellaneous category
            $table->integer('feature_category_id')->unsigned()->nullable()->default(null);

            // Species ID can be left as null for a trait that can be used by
            // any species in the database.
            $table->integer('species_id')->unsigned()->nullable()->default(null);
            
            $table->integer('rarity_id')->unsigned();
            
            $table->string('name');
            $table->string('description', 512);
            $table->text('specifications')->nullable()->default(null);
            $table->boolean('has_image')->default(0);

            $table->foreign('feature_category_id')->references('id')->on('feature_categories');
            $table->foreign('species_id')->references('id')->on('specieses');
            $table->foreign('rarity_id')->references('id')->on('rarities');
        });

        // Create character tables ////////////////////////////////////////////////////////////////

        Schema::create('character_categories', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->string('code'); // A short code used to identify the category, e.g. MYO, GEN2, etc.
            
            $table->string('name');
            $table->string('description', 512);
            $table->boolean('has_image')->default(0);
            $table->integer('sort')->unsigned()->default(0);
        });

        Schema::create('character_images', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('character_id')->unsigned();
            $table->integer('user_id')->unsigned();

            // Credits
            $table->string('designer_alias')->nullable();
            $table->string('designer_url')->nullable();
            $table->string('artist_alias')->nullable();
            $table->string('artist_url')->nullable();

            // This may get long, so making it a text field
            // For writing notes about the image.
            $table->text('description')->nullable();

            // An additional URL reference for this image.
            // Can be used to link to the original sale post, or a full body image
            // for a design that's partially obscured.
            $table->string('url')->nullable();

            $table->boolean('is_visible')->default(1);

            $table->timestamps();
            $table->softDeletes();

            //$table->foreign('character_id')->references('id')->on('characters');
            $table->foreign('user_id')->references('id')->on('users');

        });

        // The actual character tables
        Schema::create('characters', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');

            $table->integer('character_image_id')->unsigned(); // Default character image to display
            $table->integer('character_category_id')->unsigned();
            $table->integer('rarity_id')->unsigned();
            $table->integer('user_id')->unsigned();

            // Marks if the character is still a MYO slot.
            $table->boolean('is_myo_slot')->default(0);

            $table->integer('number')->unsigned();
            $table->string('slug'); // The identifying code for this character

            // For writing additional notes about the character (mod usage)
            $table->text('description')->nullable();

            // Transfer permissions
            $table->boolean('is_sellable')->default(1);
            $table->boolean('is_tradeable')->default(1);
            $table->boolean('is_giftable')->default(1);
            $table->integer('sale_value')->default(0);
            $table->timestamp('transferrable_at')->nullable()->default(null); // Date of transfer cooldown
            $table->timestamps();
            $table->softDeletes();

            $table->boolean('is_visible')->default(1);
        });
        Schema::create('character_features', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            // Images can have different features, so features are attached to images rather than characters
            $table->integer('character_image_id')->unsigned(); 
            $table->integer('feature_id')->unsigned(); 
            $table->string('data'); // Any special notes about the usage of this feature

            $table->unique(['character_image_id', 'feature_id']);
        });

        Schema::create('character_log', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('character_id')->unsigned();
            $table->integer('sender_id')->unsigned();
            $table->integer('recipient_id')->unsigned();
            $table->string('log'); // Actual log text
            $table->string('log_type'); // Transfer, updates etc.
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

            $table->timestamps();
        });

        // A general queue for modifying characters
        Schema::create('character_submissions', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('user_id')->unsigned();

            $table->integer('character_id')->unsigned()->nullable()->default(null);

            $table->string('data', 1024)->nullable(); // Includes submitted information about the slot, e.g. traits used
            $table->text('description')->nullable(); // Optional notes submitted by the user

            // Mod info
            $table->integer('staff_id')->unsigned();
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Cancelled'])->default('Pending');
            $table->text('reason')->nullable(); // Include a reason for rejection

            $table->timestamps();
        });
        

        // Tracks characters/MYO slots that enter or leave a user's possession.
        // Main things to track: 
        // - Bought/traded for/was gifted a character/MYO slot
        // - Sold/traded away/gifted away a character/MYO slot
        // - Used a MYO slot (was approved)
        // Depending on the game owner's preferences, FTO status may be "doesn't currently own a character"
        // or "never had a character" (sometimes with additional qualifications, but usually the latter)
        Schema::create('user_character_log', function(Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->increments('id');
            $table->integer('character_id')->unsigned()->nullable()->default(null);
            $table->integer('sender_id')->unsigned();
            $table->integer('recipient_id')->unsigned();
            $table->string('log'); // Actual log text
            $table->string('log_type'); // Transfer, updates etc.
            $table->string('data', 1024)->nullable(); // Includes information like staff notes, etc.

            $table->timestamps();
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_character_log');
        
        Schema::dropIfExists('character_submissions');
        Schema::dropIfExists('character_log');
        Schema::dropIfExists('character_features');
        Schema::dropIfExists('characters');
        Schema::dropIfExists('character_images');
        Schema::dropIfExists('character_categories');
        
        Schema::dropIfExists('features');
        Schema::dropIfExists('feature_categories');
        Schema::dropIfExists('specieses');

        Schema::dropIfExists('inventory_log');
        Schema::dropIfExists('inventory');
        Schema::dropIfExists('items');
        Schema::dropIfExists('item_categories');
        
        Schema::dropIfExists('rarities');
    }
}
