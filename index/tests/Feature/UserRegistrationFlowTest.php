<?php

namespace Tests\Feature;

use App\Livewire\Auth\LoginPage;
use App\Livewire\Auth\RegisterPage;
use App\Livewire\Auth\SetPasswordPage;
use App\Models\User;
use App\Notifications\SetPasswordNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Livewire\Livewire;
use Tests\TestCase;

class UserRegistrationFlowTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_page_is_accessible(): void
    {
        $response = $this->get('/auth/register');
        $response->assertOk();
        $response->assertSee('Pendaftaran Akun Pengurus');
        $response->assertSee('@pajak.go.id');
    }

    public function test_registration_rejects_non_pajak_email_domains(): void
    {
        Livewire::test(RegisterPage::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi.santoso@gmail.com')
            ->call('register')
            ->assertHasErrors(['email' => 'ends_with']);

        Livewire::test(RegisterPage::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi.santoso@yahoo.com')
            ->call('register')
            ->assertHasErrors(['email' => 'ends_with']);

        Livewire::test(RegisterPage::class)
            ->set('name', 'Budi Santoso')
            ->set('email', 'budi.santoso@kemenkeu.go.id')
            ->call('register')
            ->assertHasErrors(['email' => 'ends_with']);

        $this->assertDatabaseMissing('users', [
            'email' => 'budi.santoso@gmail.com',
        ]);
    }

    public function test_registration_accepts_valid_pajak_email_and_sends_notification(): void
    {
        Notification::fake();

        Livewire::test(RegisterPage::class)
            ->set('name', 'Ahmad Dahlan, S.E.')
            ->set('email', 'ahmad.dahlan@pajak.go.id')
            ->set('password', 'SandiRahasia123!')
            ->set('password_confirmation', 'SandiRahasia123!')
            ->call('register')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true)
            ->assertSet('registeredEmail', 'ahmad.dahlan@pajak.go.id')
            ->assertSee('Pendaftaran Berhasil! Verifikasi Terkirim');

        $user = User::where('email', 'ahmad.dahlan@pajak.go.id')->first();
        $this->assertNotNull($user);
        $this->assertEquals('Ahmad Dahlan, S.E.', $user->name);
        $this->assertEquals('Jamaah', $user->role);
        $this->assertEquals('AKTIF', $user->status);
        $this->assertNull($user->email_verified_at);
        $this->assertTrue(Hash::check('SandiRahasia123!', $user->password));

        Notification::assertSentTo($user, \App\Notifications\QueuedVerifyEmail::class);
    }

    public function test_registration_requires_name_email_and_password(): void
    {
        Livewire::test(RegisterPage::class)
            ->set('name', '')
            ->set('email', '')
            ->set('password', '')
            ->set('password_confirmation', '')
            ->call('register')
            ->assertHasErrors(['name' => 'required', 'email' => 'required', 'password' => 'required']);
    }

    public function test_registration_rejects_duplicate_email(): void
    {
        User::create([
            'name' => 'Existing User',
            'email' => 'existing.pegawai@pajak.go.id',
            'password' => Hash::make('password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        Livewire::test(RegisterPage::class)
            ->set('name', 'New Attempt User')
            ->set('email', 'existing.pegawai@pajak.go.id')
            ->set('password', 'Password123!')
            ->set('password_confirmation', 'Password123!')
            ->call('register')
            ->assertHasErrors(['email' => 'unique']);
    }

    public function test_import_firebase_users_command(): void
    {
        $dummyJsonPath = tempnam(sys_get_temp_dir(), 'firebase_users_') . '.json';
        $dummyData = [
            'users' => [
                [
                    'email' => 'migrasi1@pajak.go.id',
                    'name' => 'Pegawai Migrasi 1',
                    'phone' => '0811111111',
                ],
                [
                    'email' => 'migrasi2@pajak.go.id',
                    'displayName' => 'Pegawai Migrasi 2',
                ],
            ],
        ];
        file_put_contents($dummyJsonPath, json_encode($dummyData));

        $this->artisan('app:import-firebase-users', ['filepath' => $dummyJsonPath])
            ->assertExitCode(0);

        $user1 = User::where('email', 'migrasi1@pajak.go.id')->first();
        $this->assertNotNull($user1);
        $this->assertEquals('Pegawai Migrasi 1', $user1->name);
        $this->assertNull($user1->password);
        $this->assertNull($user1->email_verified_at);

        $user2 = User::where('email', 'migrasi2@pajak.go.id')->first();
        $this->assertNotNull($user2);
        $this->assertEquals('Pegawai Migrasi 2', $user2->name);
        $this->assertNull($user2->password);
        $this->assertNull($user2->email_verified_at);

        @unlink($dummyJsonPath);
    }

    public function test_login_with_unregistered_email_shows_error(): void
    {
        Livewire::test(LoginPage::class)
            ->set('email', 'tidak.terdaftar@pajak.go.id')
            ->set('password', 'password123')
            ->call('login')
            ->assertSet('errorMessage', 'Email belum terdaftar. Silakan lakukan pendaftaran akun.')
            ->assertSet('isMigrationAccount', false);

        $this->assertFalse(Auth::check());
    }

    public function test_login_with_migrated_null_password_shows_migration_notice(): void
    {
        $user = User::create([
            'name' => 'Akun Migrasi Lama',
            'email' => 'akun.lama@pajak.go.id',
            'password' => null,
            'role' => 'Jamaah',
            'status' => 'AKTIF',
            'email_verified_at' => null,
        ]);

        Livewire::test(LoginPage::class)
            ->set('email', 'akun.lama@pajak.go.id')
            ->set('password', 'sembarangPassword')
            ->call('login')
            ->assertSet('isMigrationAccount', true)
            ->assertSet('migrationEmail', 'akun.lama@pajak.go.id')
            ->assertSee('Akun Anda terdaftar dari sistem lama namun belum memiliki kata sandi')
            ->assertSee(route('password.request', ['email' => 'akun.lama@pajak.go.id']));

        $this->assertFalse(Auth::check());
    }

    public function test_forgot_and_reset_password_auto_activates_account(): void
    {
        Notification::fake();

        // Akun migrasi belum ada password dan belum diverifikasi
        $user = User::create([
            'name' => 'Akun Butuh Reset',
            'email' => 'butuh.reset@pajak.go.id',
            'password' => null,
            'role' => 'Jamaah',
            'status' => 'AKTIF',
            'email_verified_at' => null,
        ]);

        // Step 1: Minta link reset sandi
        Livewire::test(\App\Livewire\Auth\ForgotPasswordPage::class)
            ->set('email', 'butuh.reset@pajak.go.id')
            ->call('sendResetLink')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        Notification::assertSentTo($user, \App\Notifications\QueuedResetPassword::class);

        // Step 2: Buat token dan eksekusi ResetPasswordPage
        $token = Password::broker()->createToken($user);

        Livewire::withQueryParams(['token' => $token, 'email' => $user->email])
            ->test(\App\Livewire\Auth\ResetPasswordPage::class)
            ->assertSet('isValidToken', true)
            ->set('password', 'SandiBaru2026!')
            ->set('password_confirmation', 'SandiBaru2026!')
            ->call('resetPassword')
            ->assertHasNoErrors()
            ->assertSet('isSuccess', true);

        $user->refresh();
        $this->assertNotNull($user->password);
        $this->assertTrue(Hash::check('SandiBaru2026!', $user->password));
        $this->assertNotNull($user->email_verified_at); // Otomatis aktif!

        // Step 3: Pengguna kini bisa login
        Livewire::test(LoginPage::class)
            ->set('email', 'butuh.reset@pajak.go.id')
            ->set('password', 'SandiBaru2026!')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(Auth::check());
    }

    public function test_forgot_password_rejects_non_pajak_email_domains(): void
    {
        Livewire::test(\App\Livewire\Auth\ForgotPasswordPage::class)
            ->set('email', 'budi.santoso@gmail.com')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'ends_with']);

        Livewire::test(\App\Livewire\Auth\ForgotPasswordPage::class)
            ->set('email', 'budi.santoso@yahoo.com')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'ends_with']);

        Livewire::test(\App\Livewire\Auth\ForgotPasswordPage::class)
            ->set('email', 'budi.santoso@kemenkeu.go.id')
            ->call('sendResetLink')
            ->assertHasErrors(['email' => 'ends_with']);
    }

    public function test_email_verification_route_activates_user(): void
    {
        $user = User::create([
            'name' => 'Verifikasi User',
            'email' => 'verifikasi.user@pajak.go.id',
            'password' => Hash::make('password123'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
            'email_verified_at' => null,
        ]);

        $url = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            now()->addMinutes(60),
            ['id' => $user->getKey(), 'hash' => sha1($user->getEmailForVerification())]
        );

        $response = $this->get($url);
        $response->assertRedirect(route('login'));
        $response->assertSessionHas('status');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
    }

    public function test_set_password_page_validates_token_and_sets_new_password(): void
    {
        $user = User::create([
            'name' => 'Sri Mulyani Pegawai',
            'email' => 'sri.pegawai@pajak.go.id',
            'password' => Hash::make('temporary-password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
            'email_verified_at' => null,
        ]);

        $token = Password::broker()->createToken($user);

        Livewire::withQueryParams(['token' => $token, 'email' => $user->email])
            ->test(SetPasswordPage::class)
            ->assertSet('isValidToken', true)
            ->assertSet('email', 'sri.pegawai@pajak.go.id')
            ->assertSet('userName', 'Sri Mulyani Pegawai')
            ->assertSee('Buat Kata Sandi Baru')
            ->set('password', 'DJP-Sandi2026!')
            ->set('password_confirmation', 'DJP-Sandi2026!')
            ->call('setPassword')
            ->assertHasNoErrors()
            ->assertSet('isSuccess', true)
            ->assertSee('Kata Sandi Berhasil Dibuat!');

        $user->refresh();
        $this->assertNotNull($user->email_verified_at);
        $this->assertTrue(Hash::check('DJP-Sandi2026!', $user->password));

        // Token should be consumed and deleted from password_reset_tokens
        $this->assertFalse(Password::broker()->tokenExists($user, $token));
    }

    public function test_set_password_rejects_invalid_or_expired_token(): void
    {
        $user = User::create([
            'name' => 'Bambang Irawan',
            'email' => 'bambang.irawan@pajak.go.id',
            'password' => Hash::make('temporary-password'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
        ]);

        Livewire::withQueryParams(['token' => 'invalid-tampered-token', 'email' => $user->email])
            ->test(SetPasswordPage::class)
            ->assertSet('isValidToken', false)
            ->assertSee('Tautan Tidak Valid atau Kedaluwarsa');
    }

    public function test_user_can_login_after_setting_password(): void
    {
        $user = User::create([
            'name' => 'Faisal Rahman',
            'email' => 'faisal.rahman@pajak.go.id',
            'password' => Hash::make('temporary'),
            'role' => 'Jamaah',
            'status' => 'AKTIF',
            'email_verified_at' => null,
        ]);

        $token = Password::broker()->createToken($user);

        // Step 1: Set new password (must include uppercase, number, symbol, min 8)
        Livewire::withQueryParams(['token' => $token, 'email' => $user->email])
            ->test(SetPasswordPage::class)
            ->set('password', 'PajakBebasRiba123!')
            ->set('password_confirmation', 'PajakBebasRiba123!')
            ->call('setPassword')
            ->assertHasNoErrors();

        // Step 2: Login with new password
        Livewire::test(LoginPage::class)
            ->set('email', 'faisal.rahman@pajak.go.id')
            ->set('password', 'PajakBebasRiba123!')
            ->call('login')
            ->assertHasNoErrors()
            ->assertRedirect(route('admin.dashboard'));

        $this->assertTrue(Auth::check());
        $this->assertEquals('faisal.rahman@pajak.go.id', Auth::user()->email);
    }

    public function test_registration_enforces_strong_password_rules(): void
    {
        // Missing uppercase letter
        Livewire::test(RegisterPage::class)
            ->set('password', 'lowercase123!')
            ->set('password_confirmation', 'lowercase123!')
            ->call('register')
            ->assertHasErrors(['password']);

        // Missing symbol
        Livewire::test(RegisterPage::class)
            ->set('password', 'NoSymbolHere123')
            ->set('password_confirmation', 'NoSymbolHere123')
            ->call('register')
            ->assertHasErrors(['password']);

        // Missing number
        Livewire::test(RegisterPage::class)
            ->set('password', 'NoNumberHere!@#')
            ->set('password_confirmation', 'NoNumberHere!@#')
            ->call('register')
            ->assertHasErrors(['password']);

        // Under 8 characters
        Livewire::test(RegisterPage::class)
            ->set('password', 'Ab1!')
            ->set('password_confirmation', 'Ab1!')
            ->call('register')
            ->assertHasErrors(['password']);
    }
}
