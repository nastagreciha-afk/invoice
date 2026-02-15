<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Invoice;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@example.com',
        ]);

        // Seed some sample invoices for testing
        Invoice::factory()->create([
            'number' => 'INV-2025-0001',
            'supplier_name' => 'ТОВ "Альфа Консалтинг"',
            'supplier_tax_id' => '1234567890',
            'net_amount' => 10000.00,
            'vat_amount' => 2000.00,
            'gross_amount' => 12000.00,
            'currency' => 'UAH',
            'status' => 'pending',
            'issue_date' => '2025-02-10',
            'due_date' => '2025-02-20',
        ]);

        Invoice::factory()->create([
            'number' => 'INV-2025-0002',
            'supplier_name' => 'ТОВ "Бета Сервіс"',
            'supplier_tax_id' => '9876543210',
            'net_amount' => 5000.00,
            'vat_amount' => 1000.00,
            'gross_amount' => 6000.00,
            'currency' => 'UAH',
            'status' => 'approved',
            'issue_date' => '2025-02-15',
            'due_date' => '2025-03-01',
        ]);

        Invoice::factory()->create([
            'number' => 'INV-2025-0003',
            'supplier_name' => 'ТОВ "Гамма Трейд"',
            'supplier_tax_id' => '5555555555',
            'net_amount' => 20000.00,
            'vat_amount' => 4000.00,
            'gross_amount' => 24000.00,
            'currency' => 'USD',
            'status' => 'pending',
            'issue_date' => '2025-02-18',
            'due_date' => '2025-03-10',
        ]);
    }
}
