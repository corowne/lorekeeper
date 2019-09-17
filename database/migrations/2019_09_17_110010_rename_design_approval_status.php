<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class RenameDesignApprovalStatus extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        DB::statement("ALTER TABLE design_updates MODIFY COLUMN status ENUM('Draft', 'Pending', 'Approved', 'Rejected')");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
        DB::statement("ALTER TABLE design_updates MODIFY COLUMN status ENUM('Draft', 'Pending', 'Accepted', 'Rejected')");
    }
}
