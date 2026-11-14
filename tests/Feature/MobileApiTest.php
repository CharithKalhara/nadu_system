<?php

namespace Tests\Feature;

use App\Models\User;
use App\Support\CaseTableSchema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class MobileApiTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'database.default' => 'sqlite',
            'database.connections.sqlite' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'database.connections.companies' => [
                'driver' => 'sqlite',
                'database' => ':memory:',
                'prefix' => '',
                'foreign_key_constraints' => true,
            ],
            'cache.default' => 'array',
        ]);

        DB::purge('sqlite');
        DB::purge('companies');

        $this->createCentralSchema();
        Cache::store('array')->flush();
    }

    public function test_typing_staff_can_login_fetch_identity_and_revoke_their_token(): void
    {
        $user = $this->createUser('typing');

        $login = $this->postJson('/api/v1/auth/login', [
            'email' => $user->email,
            'password' => 'secret-password',
            'device_name' => 'Test phone',
        ]);

        $login->assertOk()->assertJsonPath('data.user.id', $user->getKey());
        $this->assertNotEmpty($login->json('data.token'));
        $this->assertNotNull(DB::table('personal_access_tokens')->value('expires_at'));
        $token = $login->json('data.token');

        $this->withToken($token)
            ->getJson('/api/v1/me')
            ->assertOk()
            ->assertJsonPath('data.email', $user->email);

        $this->withToken($token)
            ->postJson('/api/v1/auth/logout')
            ->assertOk();

        $this->withToken($token)->getJson('/api/v1/me')->assertUnauthorized();
    }

    public function test_admin_accounts_cannot_obtain_mobile_tokens_and_protected_routes_require_authentication(): void
    {
        $admin = $this->createUser('admin');

        $this->postJson('/api/v1/auth/login', [
            'email' => $admin->email,
            'password' => 'secret-password',
        ])->assertForbidden();

        $this->getJson('/api/v1/companies')->assertUnauthorized();
    }

    public function test_company_list_contains_only_valid_companies_in_the_typing_queue(): void
    {
        $this->createCompany('Typing Company', 'typing', 'company_0001_cases');
        $this->createCompany('Completed Company', 'completed', 'company_0002_cases');
        $this->createCompany('Invalid Table', 'typing', 'users');

        $response = $this->asTypingUser()->getJson('/api/v1/companies');

        $response->assertOk()->assertJson(fn (AssertableJson $json) => $json
            ->has('data', 1)
            ->where('data.0.company_name', 'Typing Company')
            ->missing('data.0.table_name')
            ->etc());
    }

    public function test_reference_data_matches_the_typing_case_form(): void
    {
        $this->asTypingUser()->getJson('/api/v1/reference-data')
            ->assertOk()
            ->assertJsonPath('data.sections.0.fields.0.key', 'nadu_ankaya')
            ->assertJsonPath('data.sections.0.fields.0.required', true)
            ->assertJsonPath('data.sections.5.fields.10.key', 'total');
    }

    public function test_case_creation_uses_the_company_table_and_rejects_invalid_fields(): void
    {
        $companyId = $this->createCompany('Typing Company', 'typing', 'company_0001_cases');
        $payload = $this->casePayload($companyId);

        $created = $this->asTypingUser()->withHeader('Idempotency-Key', 'case-entry-001')
            ->postJson('/api/v1/cases', $payload);

        $created->assertCreated()
            ->assertJsonPath('data.company_id', $companyId)
            ->assertJsonPath('data.nadu_ankaya', '2026/42');

        $this->assertDatabaseCount('company_0001_cases', 1, 'companies');
        $this->assertDatabaseHas('company_0001_cases', [
            'company_id' => $companyId,
            'nadu_ankaya' => '2026/42',
            'nayakaru1_nama' => 'Test debtor',
        ], 'companies');

        $invalid = $payload;
        unset($invalid['nadu_ankaya']);

        $this->asTypingUser()->withHeader('Idempotency-Key', 'case-entry-002')
            ->postJson('/api/v1/cases', $invalid)
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['nadu_ankaya']);
    }

    public function test_cases_are_isolated_to_the_selected_typing_company(): void
    {
        $firstCompany = $this->createCompany('First', 'typing', 'company_0001_cases');
        $secondCompany = $this->createCompany('Second', 'typing', 'company_0002_cases');

        $created = $this->asTypingUser()->withHeader('Idempotency-Key', 'case-entry-003')
            ->postJson('/api/v1/cases', $this->casePayload($secondCompany));
        $created->assertCreated();

        $this->asTypingUser()->getJson('/api/v1/cases/'.$created->json('data.id').'?company_id='.$firstCompany)
            ->assertNotFound();

        $this->asTypingUser()->getJson('/api/v1/cases?company_id='.$firstCompany)
            ->assertOk()
            ->assertJsonCount(0, 'data');

        $this->asTypingUser()->getJson('/api/v1/cases?company_id='.$secondCompany)
            ->assertOk()
            ->assertJsonCount(1, 'data');
    }

    public function test_case_creation_is_idempotent_and_rejects_key_reuse_with_different_data(): void
    {
        $companyId = $this->createCompany('Typing Company', 'typing', 'company_0001_cases');
        $payload = $this->casePayload($companyId);
        $key = 'case-entry-idempotent-1';
        $user = $this->createUser();
        $token = $user->createToken('test-device', ['mobile:use'])->plainTextToken;

        $first = $this->withToken($token)->withHeader('Idempotency-Key', $key)
            ->postJson('/api/v1/cases', $payload);
        $first->assertCreated();

        $replayed = $this->withToken($token)->withHeader('Idempotency-Key', $key)
            ->postJson('/api/v1/cases', $payload);
        $replayed->assertCreated()
            ->assertHeader('Idempotent-Replay', 'true')
            ->assertJsonPath('data.id', $first->json('data.id'));

        $changed = $payload;
        $changed['kalaya'] = 'changed';

        $this->withToken($token)->withHeader('Idempotency-Key', $key)
            ->postJson('/api/v1/cases', $changed)
            ->assertStatus(409);

        $this->assertDatabaseCount('company_0001_cases', 1, 'companies');
    }

    public function test_attachment_upload_is_private_and_linked_to_a_case_in_the_selected_company(): void
    {
        Storage::fake('local');
        $companyId = $this->createCompany('Typing Company', 'typing', 'company_0001_cases');
        $case = $this->asTypingUser()->withHeader('Idempotency-Key', 'case-entry-004')
            ->postJson('/api/v1/cases', $this->casePayload($companyId))
            ->assertCreated();

        $upload = $this->asTypingUser()->post('/api/v1/attachments', [
            'company_id' => $companyId,
            'case_id' => $case->json('data.id'),
            'file' => UploadedFile::fake()->create('proof.pdf', 20, 'application/pdf'),
        ], ['Accept' => 'application/json']);

        $upload->assertCreated()->assertJsonPath('data.case_id', $case->json('data.id'));
        $document = DB::table('documents')->first();
        $this->assertNotNull($document);
        $this->assertSame('mobile_attachment', $document->document_type);
        Storage::disk('local')->assertExists($document->file_path);

        $this->asTypingUser()->post('/api/v1/attachments', [
            'company_id' => $companyId,
            'case_id' => $case->json('data.id'),
            'file' => UploadedFile::fake()->create('proof.txt', 1, 'text/plain'),
        ], ['Accept' => 'application/json'])->assertUnprocessable();

        $wrongCompany = $this->createCompany('Other', 'typing', 'company_0002_cases');
        $this->asTypingUser()->post('/api/v1/attachments', [
            'company_id' => $wrongCompany,
            'case_id' => $case->json('data.id'),
            'file' => UploadedFile::fake()->create('proof.pdf', 20, 'application/pdf'),
        ], ['Accept' => 'application/json'])->assertNotFound();
    }

    private function createCentralSchema(): void
    {
        Schema::connection('sqlite')->create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('role')->default('typing');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('companies', function (Blueprint $table): void {
            $table->id();
            $table->string('company_name');
            $table->string('table_name')->unique();
            $table->string('status')->default('typing');
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('personal_access_tokens', function (Blueprint $table): void {
            $table->id();
            $table->morphs('tokenable');
            $table->text('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable()->index();
            $table->timestamps();
        });

        Schema::connection('sqlite')->create('documents', function (Blueprint $table): void {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('nadu_id');
            $table->string('document_type');
            $table->string('file_name');
            $table->string('file_path');
            $table->unsignedBigInteger('generated_by')->nullable();
            $table->timestamps();
        });
    }

    private function createCompany(string $name, string $status, string $tableName): int
    {
        $companyId = DB::table('companies')->insertGetId([
            'company_name' => $name,
            'table_name' => $tableName,
            'status' => $status,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if (! Schema::connection('companies')->hasTable($tableName)) {
            Schema::connection('companies')->create($tableName, function (Blueprint $table): void {
                CaseTableSchema::define($table);
            });
        }

        return $companyId;
    }

    private function createUser(string $role = 'typing'): User
    {
        return User::query()->create([
            'name' => 'Nadu Typist',
            'email' => strtolower($role).'-'.fake()->unique()->safeEmail(),
            'password' => Hash::make('secret-password'),
            'role' => $role,
        ]);
    }

    private function asTypingUser(): static
    {
        $user = $this->createUser();
        $token = $user->createToken('test-device', ['mobile:use'])->plainTextToken;

        return $this->withToken($token);
    }

    /** @return array<string, mixed> */
    private function casePayload(int $companyId): array
    {
        return [
            'company_id' => $companyId,
            'nadu_ankaya' => '2026/42',
            'dun_dinaya' => '2026-11-14',
            'nayakaru1_nama' => 'Test debtor',
            'dun_naya_mudala' => '1200.00',
        ];
    }
}
