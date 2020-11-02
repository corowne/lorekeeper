<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDelayedCommentsToSales extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sales', function (Blueprint $table) {
            // Time at which comments should open to members
            $table->timestamp('comments_open_at')->nullable()->default(null);
            
            // Bools
            $table->boolean('is_open')->default(1);
            $table->boolean('hide_comments_before_start')->default(0);
            $table->boolean('disable_commenting_after_close')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sales', function (Blueprint $table) {
            //
            $table->dropColumn('is_open');
            $table->dropColumn('comments_open_at');
            $table->dropColumn('hide_comments_before_start');
            $table->dropColumn('disable_commenting_after_close');
        });
    }
}
