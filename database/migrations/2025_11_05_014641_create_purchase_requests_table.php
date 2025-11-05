<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('purchase_requests', function (Blueprint $table): void {
            $table->id();

            $table->string('description');
            $table->text('notes')->nullable();
            $table->string('item_code')->nullable();
            $table->unsignedInteger('quantity')->default(1);
            $table->decimal('unit_price', 12, 2)->nullable();
            $table->date('eta_date')->nullable();
            $table->string('carrier')->nullable()->nullable();
            $table->enum('status', ['PENDING', 'IN_PROGRESS', 'COMPLETED', 'CANCELED'])->default('PENDING');

            $table->string('customer');
            $table->string('reference_os')->nullable();

            $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('shop_id')->constrained('shops')->cascadeOnDelete();

            $table->text('cancellation_reason')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_requests');
    }
};
