<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDisabledToUsersTable extends Migration {
    /**
     * Run the migrations.
     */
    public function up() {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_deactivated')->default(0);
            $table->integer('deactivater_id')->unsigned()->index()->nullable()->default(null);
        });
        Schema::table('user_settings', function (Blueprint $table) {
            $table->timestamp('deactivated_at')->nullable()->default(null);
            $table->text('deactivate_reason')->nullable()->default(null);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down() {
        Schema::table('users', function (Blueprint $table) {
            $table->dropcolumn('is_deactivated');
            $table->dropcolumn('deactivater_id');
        });
        Schema::table('user_settings', function (Blueprint $table) {
            $table->dropColumn('deactivate_reason');
            $table->dropColumn('deactivated_at');
        });
    }
}
