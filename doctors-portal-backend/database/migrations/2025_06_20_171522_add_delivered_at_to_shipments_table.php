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
            $table->timestamp('delivered_at')->nullable()->after('date_shipped');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_updates', function (Blueprint $table) {
            $table->dropColumn('delivered_at');
        });
    }
};
