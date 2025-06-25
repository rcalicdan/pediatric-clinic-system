<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Enums\AppointmentStatuses;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('queue_number');
            $table->date('appointment_date');
            $table->text('reason')->nullable();
            $table->enum('status', AppointmentStatuses::getAllStatuses())
                ->default(AppointmentStatuses::WAITING->value);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['appointment_date', 'queue_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
