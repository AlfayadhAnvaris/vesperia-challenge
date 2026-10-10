<?php

namespace Tests\Feature;

use App\Models\Form;
use App\Models\Submission;
use App\Models\User;
use Database\Seeders\DemoReportsSeeder;
use Database\Seeders\DemoUsersSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminDashboardTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->forceFill(['role' => 'admin'])->save();

        return $user;
    }

    private function form(): Form
    {
        return Form::create([
            'name' => 'Operational risk',
            'slug' => 'operational-risk-test',
            'schema' => [
                [
                    'name' => 'Details',
                    'payloads' => [
                        [
                            'id' => 'description',
                            'label' => 'Description',
                            'type' => 'long_text',
                            'options' => [],
                        ],
                        [
                            'id' => 'impact',
                            'label' => 'Impact',
                            'type' => 'radio_button',
                            'options' => [
                                ['id' => 'high', 'label' => 'High'],
                                ['id' => 'low', 'label' => 'Low'],
                            ],
                        ],
                        [
                            'id' => 'departments',
                            'label' => 'Departments',
                            'type' => 'checkbox',
                            'options' => [
                                ['id' => 'finance', 'label' => 'Finance'],
                                ['id' => 'operations', 'label' => 'Operations'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);
    }

    public function test_demo_users_seeder_creates_an_admin_and_a_regular_user(): void
    {
        $this->seed(DemoUsersSeeder::class);

        $this->assertDatabaseHas('users', [
            'email' => 'admin@vesperia.test',
            'role' => 'admin',
        ]);
        $this->assertDatabaseHas('users', [
            'email' => 'user@vesperia.test',
            'role' => 'user',
        ]);
    }

    public function test_demo_reports_seeder_creates_valid_idempotent_reports_for_the_demo_user(): void
    {
        $this->seed(DemoReportsSeeder::class);

        $demoUser = User::where('email', 'user@vesperia.test')->firstOrFail();
        $form = Form::where('slug', 'operational-risk')->firstOrFail();
        $reports = Submission::where('form_id', $form->id)
            ->where('user_id', $demoUser->id)
            ->get();

        $this->assertSame('user', $demoUser->role);
        $this->assertCount(3, $reports);
        $this->assertNotEmpty($reports[0]->answers);

        $this->seed(DemoReportsSeeder::class);

        $this->assertSame(3, Submission::where('form_id', $form->id)
            ->where('user_id', $demoUser->id)
            ->count());
    }

    public function test_admin_pages_require_login_and_dashboard_shows_summary(): void
    {
        $this->get('/dashboard')->assertRedirect(route('login'));
        $this->get('/admin/reports')->assertRedirect(route('login'));
        $this->get('/admin/forms')->assertNotFound();
        $this->get('/settings')->assertRedirect(route('login'));

        $form = $this->form();
        Submission::create([
            'form_id' => $form->id,
            'answers' => ['description' => 'Payment mismatch'],
        ]);

        $this->actingAs($this->admin())
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard Admin')
            ->assertSee('Tren laporan')
            ->assertSee('Monitoring laporan')
            ->assertSee('Operational risk');
    }

    public function test_user_dashboard_only_counts_the_signed_in_users_reports_and_offers_report_creation(): void
    {
        $form = $this->form();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownReport = Submission::create([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'answers' => ['description' => 'My report'],
        ]);
        Submission::create([
            'form_id' => $form->id,
            'user_id' => $otherUser->id,
            'answers' => ['description' => 'Other user report'],
        ]);

        $this->actingAs($user)
            ->get('/dashboard')
            ->assertOk()
            ->assertSee('Dashboard User')
            ->assertSee('Aktivitas laporan saya')
            ->assertSee('Buat laporan')
            ->assertSee('#'.$ownReport->id)
            ->assertDontSee('Other user report');
    }

    public function test_authenticated_user_can_open_settings_and_profile_management(): void
    {
        $this->actingAs(User::factory()->create())
            ->get('/settings')
            ->assertOk()
            ->assertSee('theme-toggle')
            ->assertSee('Mode terang aktif')
            ->assertSee(route('profile.edit'));

        $this->get(route('profile.edit'))->assertOk();
    }

    public function test_regular_users_cannot_open_admin_pages_and_only_see_their_reports(): void
    {
        $form = $this->form();
        $user = User::factory()->create();
        $otherUser = User::factory()->create();
        $ownReport = Submission::create([
            'form_id' => $form->id,
            'user_id' => $user->id,
            'answers' => ['description' => 'My private report'],
        ]);
        Submission::create([
            'form_id' => $form->id,
            'user_id' => $otherUser->id,
            'answers' => ['description' => 'Someone else report'],
        ]);

        $this->actingAs($user)
            ->get('/admin/reports')
            ->assertForbidden();

        $this->get('/admin/forms')->assertNotFound();

        $this->get(route('my-reports.index'))
            ->assertOk()
            ->assertSee('#'.$ownReport->id)
            ->assertDontSee('Someone else report');

        $this->get(route('my-reports.show', $ownReport))->assertOk();

        $otherReport = Submission::where('user_id', $otherUser->id)->firstOrFail();
        $this->get(route('my-reports.show', $otherReport))->assertNotFound();

        $admin = $this->admin();
        $this->actingAs($admin)
            ->get(route('my-reports.index'))
            ->assertForbidden();
    }

    public function test_admin_cannot_create_or_manage_form_definitions(): void
    {
        $this->actingAs($this->admin());
        $this->get('/admin/forms')->assertNotFound();
    }

    public function test_admin_can_monitor_reports_but_cannot_edit_or_delete_them(): void
    {
        $this->actingAs($this->admin());
        $this->get(route('admin.reports.index'))->assertOk();

        $form = $this->form();
        $report = Submission::create([
            'form_id' => $form->id,
            'answers' => [
                'description' => 'Needs correction',
                'impact' => 'high',
                'departments' => ['finance'],
            ],
        ]);

        $this->get(route('admin.reports.show', $report))
            ->assertOk()
            ->assertSee('Needs correction')
            ->assertSee('High')
            ->assertSee('Finance')
            ->assertDontSee('Edit laporan');

        $this->put('/admin/reports/'.$report->id, ['answers' => []])->assertMethodNotAllowed();
        $this->delete('/admin/reports/'.$report->id)->assertMethodNotAllowed();

        $this->assertDatabaseHas('submissions', ['id' => $report->id]);
    }

    public function test_admin_cannot_submit_reports_but_regular_users_can_open_the_form(): void
    {
        $form = $this->form();

        $this->actingAs($this->admin())
            ->get(route('forms.show', $form))
            ->assertRedirect(route('dashboard'));

        $this->postJson('/api/forms/'.$form->slug.'/submissions', [
            'answers' => ['description' => 'Admin attempt'],
        ])->assertForbidden();
    }
}
