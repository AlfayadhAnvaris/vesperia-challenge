<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class FormApiTest extends TestCase
{
    use RefreshDatabase;

    private function createForm(): Form
    {
        return Form::create([
            'name' => 'Test form',
            'slug' => 'test-form',
            'schema' => [
                [
                    'name' => 'Details',
                    'payloads' => [
                        [
                            'id' => 'choice-field',
                            'label' => 'Choice',
                            'type' => 'radio_button',
                            'options' => [
                                ['id' => 'option-a', 'label' => 'Option A'],
                                ['id' => 'option-b', 'label' => 'Option B'],
                            ],
                        ],
                        [
                            'id' => 'text-field',
                            'label' => 'Description',
                            'type' => 'long_text',
                            'options' => [],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_form_schema_is_available_from_the_api_and_homepage(): void
    {
        $this->createForm();

        $this->getJson('/api/forms/test-form')
            ->assertOk()
            ->assertJsonPath('data.slug', 'test-form')
            ->assertJsonPath('data.schema.0.payloads.0.id', 'choice-field');

        $this->getJson('/api/forms')
            ->assertOk()
            ->assertJsonPath('data.data.0.slug', 'test-form')
            ->assertJsonMissingPath('data.data.0.schema');

        $this->withoutVite();
        $this->get('/')
            ->assertOk()
            ->assertSee('Risiko lebih terkelola')
            ->assertSee('Mulai buat laporan');

        $this->get('/forms/test-form')
            ->assertOk()
            ->assertSee('Pusat Pelaporan Risiko')
            ->assertSee('/api/forms/test-form/submissions');
    }

    public function test_submission_is_validated_and_persisted_as_json(): void
    {
        $form = $this->createForm();

        $this->postJson('/api/forms/test-form/submissions', [
            'answers' => [
                'choice-field' => 'option-b',
                'text-field' => 'A valid description',
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('data.answers.choice-field', 'option-b')
            ->assertJsonPath('data.answers.text-field', 'A valid description');

        $this->assertDatabaseHas('submissions', [
            'form_id' => $form->id,
        ]);

        $this->getJson('/api/forms/test-form/submissions')
            ->assertUnauthorized();

        $admin = User::factory()->create();
        $admin->forceFill(['role' => 'admin'])->save();
        Sanctum::actingAs($admin);

        $this->getJson('/api/forms/test-form/submissions')
            ->assertOk()
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'form_id', 'user_id', 'answers', 'created_at', 'updated_at'],
                ],
            ])
            ->assertJsonPath('data.0.answers.choice-field', 'option-b');
    }

    public function test_guest_form_submission_is_saved_without_a_user_owner(): void
    {
        $form = $this->createForm();

        $this->postJson('/api/forms/test-form/submissions', [
            'answers' => [
                'choice-field' => 'option-a',
                'text-field' => 'Guest report',
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('message', 'Laporan berhasil dikirim.')
            ->assertJsonPath('redirect_url', null);

        $this->assertDatabaseHas('submissions', [
            'form_id' => $form->id,
            'user_id' => null,
        ]);
    }

    public function test_submission_rejects_unknown_fields_and_options(): void
    {
        $this->createForm();

        $this->postJson('/api/forms/test-form/submissions', [
            'answers' => [
                'choice-field' => 'not-a-valid-option',
                'unknown-field' => 'unexpected',
            ],
        ])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['answers.choice-field', 'answers.unknown-field']);
    }

    public function test_authenticated_api_submission_is_owned_by_the_logged_in_user(): void
    {
        $form = $this->createForm();
        $user = User::factory()->create();
        Sanctum::actingAs($user);

        $this->postJson('/api/forms/test-form/submissions', [
            'answers' => [
                'choice-field' => 'option-a',
                'text-field' => 'User report',
            ],
        ])
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('redirect_url', route('my-reports.index'));

        $this->assertDatabaseHas('submissions', [
            'form_id' => $form->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_bearer_token_api_submission_is_owned_by_the_authenticated_user(): void
    {
        $form = $this->createForm();
        $user = User::factory()->create();
        $token = $user->createToken('postman')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/forms/test-form/submissions', [
                'answers' => [
                    'choice-field' => 'option-a',
                    'text-field' => 'Submitted with a bearer token',
                ],
            ])
            ->assertCreated()
            ->assertJsonPath('data.user_id', $user->id)
            ->assertJsonPath('redirect_url', route('my-reports.index'));

        $this->assertDatabaseHas('submissions', [
            'form_id' => $form->id,
            'user_id' => $user->id,
            'answers->text-field' => 'Submitted with a bearer token',
        ]);
    }

    public function test_admin_bearer_token_cannot_create_a_submission(): void
    {
        $this->createForm();
        $admin = User::factory()->create(['role' => 'admin']);
        $token = $admin->createToken('postman')->plainTextToken;

        $this->withHeader('Authorization', 'Bearer '.$token)
            ->postJson('/api/forms/test-form/submissions', [
                'answers' => ['text-field' => 'Admin attempt'],
            ])
            ->assertForbidden();

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_invalid_bearer_token_is_not_treated_as_a_guest_submission(): void
    {
        $this->createForm();

        $this->withHeader('Authorization', 'Bearer invalid-token')
            ->postJson('/api/forms/test-form/submissions', [
                'answers' => ['text-field' => 'Should not be submitted anonymously'],
            ])
            ->assertUnauthorized()
            ->assertExactJson(['message' => 'Unauthenticated.']);

        $this->assertDatabaseCount('submissions', 0);
    }

    public function test_regular_user_api_listing_only_returns_their_own_submissions(): void
    {
        $form = $this->createForm();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownSubmission = $form->submissions()->create([
            'user_id' => $user->id,
            'answers' => ['text-field' => 'My report'],
        ]);
        $otherSubmission = $form->submissions()->create([
            'user_id' => $otherUser->id,
            'answers' => ['text-field' => 'Other report'],
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/forms/test-form/submissions')
            ->assertOk()
            ->assertExactJson([
                'data' => [[
                    'id' => $ownSubmission->id,
                    'form_id' => $form->id,
                    'user_id' => $user->id,
                    'answers' => ['text-field' => 'My report'],
                    'created_at' => $ownSubmission->created_at->toJSON(),
                    'updated_at' => $ownSubmission->updated_at->toJSON(),
                ]],
            ])
            ->assertJsonFragment(['id' => $ownSubmission->id])
            ->assertJsonMissing(['id' => $otherSubmission->id]);
    }

    public function test_api_login_returns_json_with_a_sanctum_token(): void
    {
        $user = User::factory()->create();

        $response = $this->postJson('/api/login', [
            'email' => $user->email,
            'password' => 'password',
        ])
            ->assertOk()
            ->assertJsonStructure([
                'success',
                'message',
                'user' => ['id', 'name', 'email', 'role', 'token'],
            ])
            ->assertJsonPath('success', true)
            ->assertJsonPath('message', 'Login successful')
            ->assertJsonPath('user.id', (string) $user->id)
            ->assertJsonPath('user.role', 'user');

        $token = $response->json('user.token');
        $this->assertNotEmpty($token);

        $form = $this->createForm();
        $this->withHeader('Authorization', 'Bearer '.$token)
            ->getJson('/api/forms/'.$form->slug.'/submissions')
            ->assertOk()
            ->assertJsonPath('data', []);
    }

    public function test_api_login_rejects_invalid_credentials_as_json(): void
    {
        $this->postJson('/api/login', [
            'email' => 'unknown@example.com',
            'password' => 'wrong-password',
        ])
            ->assertUnauthorized()
            ->assertExactJson([
                'success' => false,
                'message' => 'The provided credentials are incorrect.',
            ]);
    }

    public function test_database_seeder_imports_the_submission_json_definition(): void
    {
        $this->seed(DatabaseSeeder::class);

        $form = Form::where('slug', 'operational-risk')->firstOrFail();

        $this->assertSame('Detail Risiko Operasional', $form->name);
        $this->assertNotEmpty($form->schema);
        $this->assertSame('Detail Kejadian Risiko Operasional', $form->schema[0]['name']);
    }
}
