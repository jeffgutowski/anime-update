<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderNumberToCustomLists extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('custom_lists', function (Blueprint $table) {
            $table->integer('order_number');
        });
        $users = \App\Models\User::all();
        foreach ($users as $user) {
            $lists = \App\Models\CustomList::where('user_id', $user->id)->get();
            $order_number = 1;
            foreach ($lists as $list) {
                $list->order_number = $order_number;
                $list->save();
                $order_number++;
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('custom_lists', function (Blueprint $table) {
            $table->dropColumn("order_number");
        });
    }
}
