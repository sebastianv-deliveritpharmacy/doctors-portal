<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('shipment_updates', function (Blueprint $table) {
            $table->string('rx_number')->nullable()->change();
            $table->string('shipment_id')->nullable()->after('rx_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_updates', function (Blueprint $table) {
            //
        });
    }
};
