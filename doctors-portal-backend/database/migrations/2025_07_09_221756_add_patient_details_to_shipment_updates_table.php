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
            $table->date('date_of_birth')->nullable()->after('patient_name');
            $table->string('insurance')->nullable()->after('date_of_birth');
            $table->string('city')->nullable()->after('insurance');
            $table->date('arrived_to_office_date')->nullable()->after('delivered_at');
            $table->string('source')->nullable()->after('arrived_to_office_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('shipment_updates', function (Blueprint $table) {
             $table->dropColumn([
                'date_of_birth',
                'insurance',
                'city',
                'arrived_to_office_date',
                'source'
            ]);
        });
    }
};
