<?php

namespace Tests\Feature;

use App\Models\KkProfile;
use App\Models\Purok;
use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class KkProfilingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Seed default Purok
        Purok::create([
            'id' => 1,
            'purok_name' => 'J. RIZAL',
            'purok_code' => 'JPR',
            'street_name' => 'J. RIZAL'
        ]);
    }

    public function test_guest_cannot_access_profiling_dashboard(): void
    {
        $response = $this->get('/dashboard/profiling');
        $response->assertRedirect('/login');
    }

    public function test_staff_and_admin_can_access_profiling_dashboard(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);
        $staff = User::factory()->create(['role' => 'staff', 'is_approved' => true]);

        $response1 = $this->actingAs($admin)->get('/dashboard/profiling');
        $response1->assertOk();
        $response1->assertSee('Youth Profiling Registry');

        $response2 = $this->actingAs($staff)->get('/dashboard/profiling');
        $response2->assertOk();
    }

    public function test_profiling_filters_and_search_work_correctly(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        // Create secondary purok
        $purok2 = Purok::create([
            'purok_name' => 'Sunny Ridge Residences',
            'purok_code' => 'SRR',
            'street_name' => 'J. RIZAL'
        ]);

        // Create profile 1 (ISY, J. Rizal)
        KkProfile::create([
            'surname' => 'DelaCruzUnique',
            'first_name' => 'Juan',
            'middle_name' => 'Santiago',
            'ext' => null,
            'age' => 18,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2008-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '594 J.P Rizal Street',
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student'
        ]);

        // Create profile 2 (OSY, Sunny Ridge)
        KkProfile::create([
            'surname' => 'Santos',
            'first_name' => 'Maria',
            'middle_name' => null,
            'ext' => null,
            'age' => 22,
            'sex' => 'Female',
            'gender' => 'Female',
            'dob' => '2004-05-15',
            'civil_status' => 'Single',
            'purok_id' => $purok2->id,
            'street_address' => 'Sunny Ridge Unit 10A',
            'youth_classification' => 'OSY',
            'contact_number' => '09187654321',
            'email' => 'maria@example.com',
            'registered_sk_voter' => false,
            'registered_national_voter' => true,
            'attended_kk_assembly' => false,
            'part_of_youth_org' => true,
            'youth_org_name' => 'Local Club',
            'interested_in_joining' => false,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Graduate'
        ]);

        // Search Test
        $responseSearch = $this->actingAs($admin)->get('/dashboard/profiling?search=Juan');
        $responseSearch->assertSee('DelaCruzUnique');
        $responseSearch->assertDontSee('Santos');

        // Purok Filter Test
        $responsePurok = $this->actingAs($admin)->get('/dashboard/profiling?purok=' . $purok2->id);
        $responsePurok->assertSee('Santos');
        $responsePurok->assertDontSee('DelaCruzUnique');

        // Classification Filter Test
        $responseClass = $this->actingAs($admin)->get('/dashboard/profiling?classification=ISY');
        $responseClass->assertSee('DelaCruzUnique');
        $responseClass->assertDontSee('Santos');

        // Year Filter Test
        $currentYear = date('Y');
        $responseYear = $this->actingAs($admin)->get("/dashboard/profiling?year={$currentYear}");
        $responseYear->assertSee('DelaCruzUnique');
        $responseYear->assertSee('Santos');

        // Sex Filter Test
        $responseSex = $this->actingAs($admin)->get('/dashboard/profiling?sex=Female');
        $responseSex->assertSee('Santos');
        $responseSex->assertDontSee('DelaCruzUnique');

        // Page Size Limit Test
        $responseLimit = $this->actingAs($admin)->get('/dashboard/profiling?limit=10');
        $responseLimit->assertOk();
    }

    public function test_storing_profile_validates_required_fields(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $response = $this->actingAs($admin)->post('/dashboard/profiling', []);
        $response->assertSessionHasErrors([
            'surname', 'first_name', 'age', 'sex', 'dob', 'civil_status', 
            'purok_id', 'youth_classification', 'contact_number', 'email',
            'registered_sk_voter', 'registered_national_voter', 'attended_kk_assembly',
            'part_of_youth_org', 'interested_in_joining', 'part_of_lgbtqia', 
            'pwd', 'highest_educational_attainment', 'consent_given'
        ]);
    }

    public function test_storing_profile_succeeds_and_creates_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $payload = [
            'surname' => 'Reyes',
            'first_name' => 'Jose',
            'middle_name' => 'Cruz',
            'ext' => 'Jr.',
            'age' => 19,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2007-11-12',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Rizal St',
            'youth_classification' => 'WY',
            'contact_number' => '09191112222',
            'email' => 'jose@example.com',
            
            'registered_sk_voter' => 1,
            'registered_national_voter' => 1,
            'attended_kk_assembly' => 0,
            'part_of_youth_org' => 1,
            'youth_org_name' => 'Namayan Basketball Association',
            'interested_in_joining' => 0,
            
            'part_of_lgbtqia' => 0,
            'pwd' => 0,
            'highest_educational_attainment' => '1st year College',
            'consent_given' => 1,
        ];

        $response = $this->actingAs($admin)->post('/dashboard/profiling', $payload);
        $response->assertRedirect('/dashboard/profiling');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kk_profiles', [
            'surname' => 'Reyes',
            'first_name' => 'Jose',
            'email' => 'jose@example.com',
            'purok_id' => 1,
            'processed_by' => $admin->id,
        ]);

        // Assert Activity Log was recorded
        $this->assertDatabaseHas('activity_logs', [
            'action' => 'kk_profile_created',
            'subject_type' => KkProfile::class,
        ]);
    }

    public function test_dashboard_shows_kk_profiling_charts_data(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        // Create a few profiles with different classifications
        KkProfile::create([
            'surname' => 'A', 'first_name' => 'B', 'age' => 18, 'sex' => 'Male',
            'dob' => '2008-01-20', 'civil_status' => 'Single', 'purok_id' => 1,
            'youth_classification' => 'ISY', 'contact_number' => '09171234567', 'email' => 'a@example.com',
            'registered_sk_voter' => true, 'registered_national_voter' => false, 'attended_kk_assembly' => true,
            'part_of_youth_org' => false, 'interested_in_joining' => true, 'part_of_lgbtqia' => false, 'pwd' => false,
            'highest_educational_attainment' => 'High School'
        ]);

        KkProfile::create([
            'surname' => 'C', 'first_name' => 'D', 'age' => 22, 'sex' => 'Female',
            'dob' => '2004-05-15', 'civil_status' => 'Single', 'purok_id' => 1,
            'youth_classification' => 'OSY', 'contact_number' => '09187654321', 'email' => 'c@example.com',
            'registered_sk_voter' => false, 'registered_national_voter' => true, 'attended_kk_assembly' => false,
            'part_of_youth_org' => true, 'interested_in_joining' => false, 'part_of_lgbtqia' => false, 'pwd' => false,
            'highest_educational_attainment' => 'College'
        ]);

        $response = $this->actingAs($admin)->get('/dashboard');
        $response->assertOk();
        $response->assertViewHas('totalYouth', 2);
        $response->assertViewHas('totalIsy', 1);
        $response->assertViewHas('totalOsy', 1);
        $response->assertViewHas('totalWy', 0);
        $response->assertViewHas('totalSkVoters', 1);
        $response->assertViewHas('chartData');
        $response->assertViewHas('classificationDistribution');

        $chartData = $response->viewData('chartData');
        $classificationDistribution = $response->viewData('classificationDistribution');

        $this->assertCount(1, $chartData);
        $this->assertEquals('J. RIZAL', $chartData[0]['purok']);
        $this->assertEquals(2, $chartData[0]['count']);

        $this->assertEquals(1, $classificationDistribution['isy']);
        $this->assertEquals(1, $classificationDistribution['osy']);
        $this->assertEquals(0, $classificationDistribution['wy']);

        $response->assertViewHas('accomplishedByProgram');
        $response->assertViewHas('accomplishmentTrends');

        $accomplishedByProgram = $response->viewData('accomplishedByProgram');
        $accomplishmentTrends = $response->viewData('accomplishmentTrends');

        $this->assertEquals(0, $accomplishedByProgram['health']);
        $this->assertEquals(0, $accomplishedByProgram['medicine']);
        $this->assertEquals(0, $accomplishedByProgram['silid']);
        $this->assertEquals(0, $accomplishedByProgram['sports']);

        $this->assertCount(6, $accomplishmentTrends);
        $this->assertEquals(now()->subMonths(5)->format('M Y'), $accomplishmentTrends[0]['label']);
        $this->assertEquals(0, $accomplishmentTrends[0]['count']);
        $this->assertEquals(now()->format('M Y'), $accomplishmentTrends[5]['label']);
        $this->assertEquals(0, $accomplishmentTrends[5]['count']);
    }

    public function test_citizen_can_self_profile_and_tags_processed_by(): void
    {
        $citizen = User::factory()->create(['role' => 'user', 'is_approved' => true]);

        $payload = [
            'surname' => 'Reyes',
            'first_name' => 'Jose',
            'middle_name' => 'Cruz',
            'ext' => 'Jr.',
            'age' => 19,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2007-11-12',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Rizal St',
            'youth_classification' => 'WY',
            'contact_number' => '09191112222',
            'email' => 'jose@example.com',

            'registered_sk_voter' => 1,
            'registered_national_voter' => 1,
            'attended_kk_assembly' => 0,
            'part_of_youth_org' => 1,
            'youth_org_name' => 'Basketball Association',
            'interested_in_joining' => 0,

            'part_of_lgbtqia' => 0,
            'pwd' => 0,
            'highest_educational_attainment' => '1st year College',
            'consent_given' => 1,
        ];

        // Citizens can access the self-profiling create screen
        $responseGet = $this->actingAs($citizen)->get('/profile/profiling');
        $responseGet->assertOk();
        $responseGet->assertSee('Katipunan ng Kabataan Self Profiling');

        // Citizens can submit their profile details
        $responsePost = $this->actingAs($citizen)->post('/profile/profiling', $payload);
        $responsePost->assertRedirect('/profile/my-requests');
        $responsePost->assertSessionHas('success');

        // Verify database entry has citizen's own email and is processed_by the citizen
        $this->assertDatabaseHas('kk_profiles', [
            'surname' => 'Reyes',
            'first_name' => 'Jose',
            'email' => $citizen->email,
            'processed_by' => $citizen->id,
        ]);
    }

    public function test_admin_can_export_profiling_data_to_csv(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        KkProfile::create([
            'surname' => 'Garcia',
            'first_name' => 'Maria',
            'middle_name' => 'Santos',
            'ext' => null,
            'age' => 20,
            'sex' => 'Female',
            'gender' => 'Female',
            'dob' => '2006-03-15',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '456 Main St',
            'youth_classification' => 'ISY',
            'contact_number' => '09177654321',
            'email' => 'maria.garcia@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => true,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => false,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'College Student',
            'consent_given' => true,
            'processed_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->get('/dashboard/export/profiling');

        $response->assertOk();
        $this->assertStringContainsString('attachment; filename="export_profiling_', $response->headers->get('Content-Disposition'));

        $content = $response->streamedContent();
        $this->assertStringContainsString('Surname', $content);
        $this->assertStringContainsString('First Name', $content);
        $this->assertStringContainsString('Garcia', $content);
        $this->assertStringContainsString('maria.garcia@example.com', $content);
    }

    public function test_admin_or_staff_can_update_profile_and_creates_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $profile = KkProfile::create([
            'surname' => 'Dela Cruz',
            'first_name' => 'Juan',
            'age' => 18,
            'sex' => 'Male',
            'dob' => '2008-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
            'processed_by' => $admin->id,
        ]);

        $payload = [
            'surname' => 'Dela Cruz Updated',
            'first_name' => 'Juan',
            'age' => 19,
            'sex' => 'Male',
            'dob' => '2007-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => 1,
            'registered_national_voter' => 0,
            'attended_kk_assembly' => 1,
            'part_of_youth_org' => 0,
            'interested_in_joining' => 1,
            'part_of_lgbtqia' => 0,
            'pwd' => 0,
            'highest_educational_attainment' => 'College Student',
            'consent_given' => 1,
        ];

        $response = $this->actingAs($admin)->put("/dashboard/profiling/{$profile->id}", $payload);
        $response->assertRedirect('/dashboard/profiling');
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('kk_profiles', [
            'id' => $profile->id,
            'surname' => 'Dela Cruz Updated',
            'age' => 19,
            'highest_educational_attainment' => 'College Student',
            'processed_by' => $admin->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'kk_profile_updated',
            'subject_type' => KkProfile::class,
            'subject_id' => $profile->id,
        ]);
    }

    public function test_admin_or_staff_can_delete_profile_and_creates_audit_log(): void
    {
        $admin = User::factory()->create(['role' => 'admin', 'is_approved' => true]);

        $profile = KkProfile::create([
            'surname' => 'Dela Cruz',
            'first_name' => 'Juan',
            'age' => 18,
            'sex' => 'Male',
            'dob' => '2008-01-20',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'youth_classification' => 'ISY',
            'contact_number' => '09171234567',
            'email' => 'juan@example.com',
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
            'consent_given' => true,
            'processed_by' => $admin->id,
        ]);

        $response = $this->actingAs($admin)->delete("/dashboard/profiling/{$profile->id}");
        $response->assertRedirect('/dashboard/profiling');
        $response->assertSessionHas('success');

        $this->assertDatabaseMissing('kk_profiles', [
            'id' => $profile->id,
        ]);

        $this->assertDatabaseHas('activity_logs', [
            'action' => 'kk_profile_deleted',
            'subject_type' => KkProfile::class,
            'subject_id' => $profile->id,
        ]);
    }
}

