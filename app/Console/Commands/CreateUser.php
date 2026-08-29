<?php

namespace App\Console\Commands;

use App\Models\User;
use Database\Seeders\DefaultMilestoneSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create
                            {--name= : Nama mahasiswa}
                            {--email= : Alamat email mahasiswa}
                            {--password= : Password untuk akun}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Membuat satu akun mahasiswa baru dan menginisialisasi milestone default';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $name = $this->option('name');
        while (empty($name)) {
            $name = $this->ask('Masukkan nama mahasiswa');
            if (empty($name)) {
                $this->error('Nama tidak boleh kosong!');
            }
        }

        $email = $this->option('email');
        while (true) {
            if (empty($email)) {
                $email = $this->ask('Masukkan email mahasiswa');
            }

            $validator = Validator::make(
                ['email' => $email],
                ['email' => 'required|email|unique:users,email']
            );

            if ($validator->fails()) {
                $this->error($validator->errors()->first());
                $email = null; // Reset and ask again
                continue;
            }
            break;
        }

        $password = $this->option('password');
        while (true) {
            if (empty($password)) {
                $password = $this->secret('Masukkan password (minimal 8 karakter)');
            }

            $validator = Validator::make(
                ['password' => $password],
                ['password' => 'required|min:8']
            );

            if ($validator->fails()) {
                $this->error($validator->errors()->first());
                $password = null; // Reset and ask again
                continue;
            }
            break;
        }

        $this->info('Sedang membuat akun mahasiswa...');

        // 1. Buat User
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        // 2. Buat ThesisProfile (profil skripsi default/kosong)
        $user->thesisProfile()->create();

        // 3. Jalankan DefaultMilestoneSeeder untuk menginisialisasi milestone
        $this->info('Menginisialisasi milestone default...');
        $seeder = new DefaultMilestoneSeeder();
        $seeder->run();

        $this->info('=========================================');
        $this->info("Akun mahasiswa berhasil dibuat!");
        $this->info("Nama     : {$user->name}");
        $this->info("Email    : {$user->email}");
        $this->info("Password : [DISEMBUNYIKAN]");
        $this->info('=========================================');

        return Command::SUCCESS;
    }
}
