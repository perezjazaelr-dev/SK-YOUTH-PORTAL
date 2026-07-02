<?php

namespace Tests\Feature;

use App\Models\League;
use App\Models\RegistrationForm;
use App\Models\FormField;
use App\Models\RegistrationResponse;
use App\Models\KkProfile;
use App\Models\Purok;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class SportsFormBuilderTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        Purok::firstOrCreate([
            'id' => 1,
        ], [
            'purok_name' => 'J. RIZAL',
            'purok_code' => 'JPR',
            'street_name' => 'J. RIZAL'
        ]);
    }

    /**
     * Test superadmin can access create form builder page.
     */
    public function test_admin_can_access_form_builder(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->get(route('admin.sports-league.form-builder.create'));

        $response->assertStatus(200);
        $response->assertSee('Sports Registration Form Builder');
    }

    /**
     * Test admin can store sports form schema.
     */
    public function test_admin_can_store_sports_form_schema(): void
    {
        $admin = User::factory()->create(['role' => 'superadmin']);

        $response = $this->actingAs($admin)->post(route('admin.sports-league.form-builder.store'), [
            'league_name' => 'SK Namayan Cup 2026',
            'sport' => 'Basketball',
            'division_name' => 'Midget Division',
            'description' => 'Ages 12 to 17',
            'custom_fields' => [
                [
                    'label' => 'Team Jersey Name',
                    'name' => 'team_jersey_name',
                    'type' => 'text',
                    'placeholder' => 'Enter your jersey name',
                    'required' => '1',
                ],
                [
                    'label' => 'Jersey Size',
                    'name' => 'jersey_size',
                    'type' => 'select',
                    'placeholder' => 'Select size',
                    'required' => '0',
                    'options' => ['S', 'M', 'L', 'XL'],
                ]
            ]
        ]);

        $response->assertRedirect(route('admin.sports-league.index'));
        $this->assertDatabaseHas('leagues', [
            'name' => 'SK Namayan Cup 2026',
            'sport' => 'Basketball',
        ]);

        $this->assertDatabaseHas('registration_forms', [
            'type' => 'sports',
            'division_name' => 'Midget Division',
            'description' => 'Ages 12 to 17',
        ]);

        $this->assertDatabaseHas('form_fields', [
            'field_label' => 'Team Jersey Name',
            'field_name' => 'team_jersey_name',
            'field_type' => 'text',
            'is_required' => true,
        ]);
    }

    /**
     * Test public can view dynamic form if logged in with KK profile.
     */
    public function test_public_can_view_dynamic_form(): void
    {
        $league = League::create(['name' => 'Summer Volleyball 2026', 'sport' => 'Volleyball']);
        $form = RegistrationForm::create([
            'type' => 'sports',
            'league_id' => $league->id,
            'division_name' => 'Volleyball Womens',
        ]);
        FormField::create([
            'registration_form_id' => $form->id,
            'field_label' => 'Preferred Jersey Number',
            'field_name' => 'jersey_number',
            'field_type' => 'number',
            'is_required' => true,
        ]);

        $user = User::factory()->create(['role' => 'user']);
        KkProfile::create([
            'email' => $user->email,
            'status' => 'approved',
            'surname' => 'User',
            'first_name' => 'Test',
            'age' => 20,
            'sex' => 'Female',
            'gender' => 'Female',
            'dob' => '2006-07-02',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Street',
            'youth_classification' => 'ISY',
            'contact_number' => '09123456789',
            'consent_given' => true,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
        ]);

        $response = $this->actingAs($user)->get(route('forms.sports.create', ['division_id' => $form->id]));

        $response->assertStatus(200);
        $response->assertSee('Volleyball Womens');
        $response->assertSee('Preferred Jersey Number');
    }

    /**
     * Test public submission with dynamic validation and file uploads.
     */
    public function test_public_can_submit_dynamic_registration(): void
    {
        Storage::fake('public');

        $league = League::create(['name' => 'SK Namayan Cup 2026', 'sport' => 'Basketball']);
        $form = RegistrationForm::create([
            'type' => 'sports',
            'league_id' => $league->id,
            'division_name' => 'Senior Division',
        ]);
        
        $textField = FormField::create([
            'registration_form_id' => $form->id,
            'field_label' => 'Position Play',
            'field_name' => 'position_play',
            'field_type' => 'text',
            'is_required' => true,
        ]);

        $fileField = FormField::create([
            'registration_form_id' => $form->id,
            'field_label' => 'Identity Doc',
            'field_name' => 'identity_doc',
            'field_type' => 'file',
            'is_required' => true,
        ]);

        $fakeFile = UploadedFile::fake()->create('my_id.jpg', 500, 'image/jpeg');

        $user = User::factory()->create(['role' => 'user']);
        KkProfile::create([
            'email' => $user->email,
            'status' => 'approved',
            'surname' => 'User',
            'first_name' => 'Test',
            'age' => 22,
            'sex' => 'Male',
            'gender' => 'Male',
            'dob' => '2004-07-02',
            'civil_status' => 'Single',
            'purok_id' => 1,
            'street_address' => '123 Street',
            'youth_classification' => 'ISY',
            'contact_number' => '09123456789',
            'consent_given' => true,
            'registered_sk_voter' => true,
            'registered_national_voter' => false,
            'attended_kk_assembly' => true,
            'part_of_youth_org' => false,
            'interested_in_joining' => true,
            'part_of_lgbtqia' => false,
            'pwd' => false,
            'highest_educational_attainment' => 'High School Student',
        ]);

        $response = $this->actingAs($user)->post(route('forms.sports.store'), [
            'registration_form_id' => $form->id,
            'answers' => [
                'position_play' => 'Point Guard',
                'identity_doc' => $fakeFile,
            ],
        ]);

        $response->assertRedirect(route('forms.sports.create', ['division_id' => $form->id]));
        $response->assertSessionHas('success');

        $submission = RegistrationResponse::first();
        $this->assertNotNull($submission);
        $this->assertEquals('Point Guard', $submission->answers['position_play']);
        
        $filePath = $submission->answers['identity_doc'];
        $this->assertNotNull($filePath);
        Storage::disk('public')->assertExists($filePath);
    }
}
