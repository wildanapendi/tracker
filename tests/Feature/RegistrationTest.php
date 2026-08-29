<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    // ─── Skenario 1: Halaman registrasi Filament dapat diakses ───────────────

    public function test_registration_page_is_accessible(): void
    {
        $response = $this->get('/register');

        $response->assertStatus(200);
    }

    // ─── Skenario 2: Registrasi web berhasil membuat user baru ───────────────

    public function test_new_user_can_register_via_fortify(): void
    {
        // Fortify menangani POST /fortify/register secara HTTP tradisional
        $this->post('/fortify/register', [
            'name'                  => 'Mahasiswa Baru',
            'email'                 => 'mahasiswabaru@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $this->assertDatabaseHas('users', ['email' => 'mahasiswabaru@example.com']);
    }

    // ─── Skenario 3: User baru otomatis dapat 4 milestone & ThesisProfile ────

    public function test_new_user_gets_default_milestones_and_thesis_profile(): void
    {
        $user = User::factory()->create();
        event(new Registered($user));

        $this->assertDatabaseHas('thesis_profiles', ['user_id' => $user->id]);

        $milestones = $user->milestones()->get();
        $this->assertCount(4, $milestones);

        $first = $milestones->firstWhere('order', 1);
        $this->assertNotNull($first);
        $this->assertSame('Pengajuan Proposal', $first->title);
        $this->assertCount(4, $first->documents);
    }

    // ─── Skenario 3b: Listener idempotent (tidak double-seed) ────────────────

    public function test_listener_does_not_duplicate_milestones_if_called_twice(): void
    {
        $user = User::factory()->create();
        event(new Registered($user));
        event(new Registered($user));

        $this->assertCount(4, $user->milestones()->get());
    }

    // ─── Skenario 4: API POST /api/register berhasil 201 ────────────────────

    public function test_api_register_returns_201_with_user_data(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'API User',
            'email'                 => 'apiuser@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(201)
            ->assertJsonStructure([
                'message',
                'user' => ['id', 'name', 'email'],
                'note',
            ])
            ->assertJsonPath('user.email', 'apiuser@example.com');

        $this->assertDatabaseHas('users', ['email' => 'apiuser@example.com']);
    }

    // ─── Skenario 5: Validasi gagal mengembalikan 422 ────────────────────────

    public function test_api_register_rejects_duplicate_email(): void
    {
        User::factory()->create(['email' => 'duplikat@example.com']);

        $response = $this->postJson('/api/register', [
            'name'                  => 'User Duplikat',
            'email'                 => 'duplikat@example.com',
            'password'              => 'password',
            'password_confirmation' => 'password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }

    public function test_api_register_rejects_mismatched_password(): void
    {
        $response = $this->postJson('/api/register', [
            'name'                  => 'User Salah',
            'email'                 => 'salah@example.com',
            'password'              => 'password',
            'password_confirmation' => 'salah-password',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['password']);
    }
}
