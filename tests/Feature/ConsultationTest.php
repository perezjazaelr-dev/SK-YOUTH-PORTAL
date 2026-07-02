<?php

namespace Tests\Feature;

use App\Models\ConsultationRequest;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ConsultationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test public views.
     */
    public function test_public_can_view_consultation_form(): void
    {
        $response = $this->get(route('skonsulta.index'));
        $response->assertStatus(200);
        $response->assertSee('SKONSULTA Anonymous Platform');
    }

    public function test_public_can_view_tracking_page(): void
    {
        $response = $this->get(route('skonsulta.track'));
        $response->assertStatus(200);
        $response->assertSee('Track Inquiry Status');
    }

    /**
     * Test public can submit an anonymous consultation request.
     */
    public function test_public_can_submit_anonymous_consultation(): void
    {
        $response = $this->postJson(route('consultations.store'), [
            'category' => 'General Concern',
            'subject' => 'Mental Health Support Inquiry',
            'message' => 'Hello, I would like to ask about anonymous support groups in Barangay Namayan.',
        ]);

        $response->assertStatus(201); // 201 Created
        $response->assertJsonStructure([
            'success',
            'message',
            'tracking_id',
        ]);

        $data = $response->json();
        $this->assertStringStartsWith('SKO-', $data['tracking_id']);
        $this->assertDatabaseHas('consultation_requests', [
            'tracking_id' => $data['tracking_id'],
            'category' => 'General Concern',
            'subject' => 'Mental Health Support Inquiry',
            'status' => 'Pending',
        ]);
    }

    /**
     * Test public can track their consultation request status and replies using tracking ID.
     */
    public function test_public_can_track_consultation(): void
    {
        $consultation = ConsultationRequest::create([
            'category' => 'Suggestion',
            'subject' => 'Scholarship Application Help',
            'message' => 'What are the requirements for the SK Namayan scholarship?',
            'status' => 'Pending',
            'replies' => [
                [
                    'message' => 'Please submit a copy of your school card.',
                    'sender' => 'SK Admin',
                    'timestamp' => '2026-07-02 12:00:00',
                ]
            ],
        ]);

        $response = $this->getJson(route('consultations.track', ['tracking_id' => $consultation->tracking_id]));

        $response->assertStatus(200);
        $response->assertJson([
            'tracking_id' => $consultation->tracking_id,
            'category' => 'Suggestion',
            'subject' => 'Scholarship Application Help',
            'status' => 'Pending',
            'replies' => [
                [
                    'message' => 'Please submit a copy of your school card.',
                    'sender' => 'SK Admin',
                ]
            ],
        ]);
    }

    /**
     * Test guest cannot access consultations index.
     */
    public function test_guest_cannot_view_consultations_index(): void
    {
        $response = $this->get(route('admin.consultations.index'));
        $response->assertRedirect('/login');
    }

    /**
     * Test regular user cannot access consultations index.
     */
    public function test_user_cannot_view_consultations_index(): void
    {
        $user = User::factory()->create(['role' => 'user']);

        $response = $this->actingAs($user)->get(route('admin.consultations.index'));
        $response->assertRedirect('/');
        $response->assertSessionHas('error');
    }

    /**
     * Test staff/admin/superadmin can access consultations index.
     */
    public function test_staff_can_view_consultations_index(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)->get(route('admin.consultations.index'));
        $response->assertStatus(200);
        $response->assertSee('Consultation Inbox');
    }

    /**
     * Test staff can update consultation status.
     */
    public function test_staff_can_update_consultation_status(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);
        $consultation = ConsultationRequest::create([
            'category' => 'Report',
            'subject' => 'Trash Collection Complaint',
            'message' => 'No collection has happened this week.',
        ]);

        $response = $this->actingAs($staff)->patch(route('admin.consultations.update-status', $consultation), [
            'status' => 'In Review',
        ]);

        $response->assertRedirect(route('admin.consultations.index'));
        $this->assertDatabaseHas('consultation_requests', [
            'id' => $consultation->id,
            'status' => 'In Review',
        ]);
    }

    /**
     * Test staff can reply to consultations.
     */
    public function test_staff_can_reply_to_consultations(): void
    {
        $staff = User::factory()->create([
            'name' => 'Kagawad Dela Cruz',
            'role' => 'staff'
        ]);
        $consultation = ConsultationRequest::create([
            'category' => 'General Concern',
            'subject' => 'Basketball Court Booking',
            'message' => 'How can we book the court for a tournament?',
        ]);

        $response = $this->actingAs($staff)->post(route('admin.consultations.reply', $consultation), [
            'message' => 'You can book it at the SK office during office hours.',
        ]);

        $response->assertRedirect(route('admin.consultations.index'));
        
        $consultation->refresh();
        $this->assertCount(1, $consultation->replies);
        $this->assertEquals('You can book it at the SK office during office hours.', $consultation->replies[0]['message']);
        $this->assertEquals('Kagawad Dela Cruz', $consultation->replies[0]['sender']);
        $this->assertEquals('In Review', $consultation->status);
    }
}
