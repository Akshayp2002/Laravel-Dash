<?php

namespace Database\Seeders;

use App\Models\Transaction;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class TransactionSeeder extends Seeder
{
      /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // $transactions = [
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 5000.00,
        //         'transaction_type' => 'credit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Salary credited',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 2000.00,
        //         'transaction_type' => 'credit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Freelance project payment',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 1500.00,
        //         'transaction_type' => 'credit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Refund from e-commerce order',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 7500.00,
        //         'transaction_type' => 'credit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Bonus received',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 3000.00,
        //         'transaction_type' => 'credit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Bank interest credited',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 1000.00,
        //         'transaction_type' => 'debit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Grocery shopping',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 500.00,
        //         'transaction_type' => 'debit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Electricity bill payment',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 1200.00,
        //         'transaction_type' => 'debit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Mobile recharge',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 2500.00,
        //         'transaction_type' => 'debit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Dining out with friends',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        //     [
        //         'id'               => (string) Str::ulid(),
        //         'user_id'          => (string) Str::ulid(),
        //         'account_id'       => (string) Str::ulid(),
        //         'amount'           => 8000.00,
        //         'transaction_type' => 'debit',
        //         'reference_number' => Str::uuid(),
        //         'description'      => 'Laptop installment payment',
        //         'created_at'       => now(),
        //         'updated_at'       => now(),
        //     ],
        // ];


        $faker = Faker::create(); // ✅ Use Faker factory correctly
        $transactions = [];

        for ($i = 0; $i < 10; $i++) {
            $transactions[] = [
                'id'               => (string) Str::ulid(),
                'user_id'          => (string) Str::ulid(),
                'account_id'       => (string) Str::ulid(),
                'amount'           => $faker->randomFloat(2, 500, 10000), // Random amount (500-10000)
                'transaction_type' => $faker->randomElement(['credit', 'debit']),
                'reference_number' => (string) Str::uuid(),
                'description'      => $faker->sentence(4), // Random short description
                'created_at'       => now(),
                'updated_at'       => now(),
            ];
        }

        Transaction::insert($transactions);
    }
}
