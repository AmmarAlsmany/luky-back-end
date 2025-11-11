<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->string('booking_number')->unique();
            $table->foreignId('client_id')->constrained('users')->onDelete('restrict');
            $table->foreignId('provider_id')->constrained('service_providers')->onDelete('restrict');
            $table->date('booking_date');
            $table->datetime('start_time');
            $table->datetime('end_time');
            $table->enum('status', ['pending', 'confirmed', 'completed', 'cancelled', 'rejected'])->default('pending');
            $table->decimal('subtotal', 10, 2);
            $table->decimal('tax_amount', 10, 2)->default(0);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2);
            $table->decimal('commission_amount', 10, 2);
            $table->enum('payment_status', ['pending', 'paid', 'refunded', 'failed'])->default('pending');
            $table->string('payment_method')->nullable();
            $table->string('payment_reference')->nullable();
            $table->text('notes')->nullable();
            $table->string('client_address')->nullable();
            $table->decimal('client_latitude', 10, 8)->nullable();
            $table->decimal('client_longitude', 11, 8)->nullable();
            $table->text('cancellation_reason')->nullable();
            $table->enum('cancelled_by', ['client', 'provider', 'admin'])->nullable();
            $table->timestamp('confirmed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->softDeletes();
            $table->timestamps();
            
            $table->index(['client_id', 'status']);
            $table->index(['provider_id', 'status']);
            $table->index(['booking_date', 'status']);
            $table->index('payment_status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
};