<?php

namespace App\Http\Controllers;

use App\Models\HealthRequest;
use App\Models\MedicineRequest;
use App\Models\SilidKarununganRequest;
use App\Models\SportsRegistration;
use App\Models\KkProfile;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ExportController extends Controller
{
    /**
     * Stream CSV export for a specific request type.
     */
    public function export($type): StreamedResponse
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export_' . $type . '_' . date('Ymd_His') . '.csv"',
            'Cache-Control' => 'no-cache, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0',
        ];

        return new StreamedResponse(function () use ($type) {
            $handle = fopen('php://output', 'w');

            // Add UTF-8 BOM for Excel compatibility
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            switch ($type) {
                case 'health':
                    fputcsv($handle, ['ID', 'First Name', 'Last Name', 'Middle Name', 'Age', 'Gender', 'Email', 'Contact Number', 'Concerns', 'Preferred Date', 'Preferred Time', 'Status', 'Date Submitted']);
                    HealthRequest::chunk(100, function ($records) use ($handle) {
                        foreach ($records as $record) {
                            fputcsv($handle, [
                                $record->id,
                                $record->first_name,
                                $record->last_name,
                                $record->middle_name,
                                $record->age,
                                $record->gender,
                                $record->email,
                                $record->contact_number,
                                $record->concerns,
                                $record->preferred_date->format('Y-m-d'),
                                $record->preferred_time,
                                $record->status,
                                $record->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;

                case 'medicine':
                    fputcsv($handle, ['ID', 'Requestor First Name', 'Requestor Last Name', 'Age', 'Gender', 'Email', 'Contact Number', 'Complete Address', 'Status', 'Date Submitted']);
                    MedicineRequest::chunk(100, function ($records) use ($handle) {
                        foreach ($records as $record) {
                            fputcsv($handle, [
                                $record->id,
                                $record->requestor_first_name,
                                $record->requestor_last_name,
                                $record->requestor_age,
                                $record->requestor_gender,
                                $record->email,
                                $record->contact_number,
                                $record->complete_address,
                                $record->status,
                                $record->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;

                case 'silid':
                    fputcsv($handle, ['ID', 'Requestor First Name', 'Requestor Last Name', 'Requestor Middle Name', 'Age', 'Email', 'Contact Number', 'Preferred Date', 'Preferred Time', 'Status', 'Date Submitted']);
                    SilidKarununganRequest::chunk(100, function ($records) use ($handle) {
                        foreach ($records as $record) {
                            fputcsv($handle, [
                                $record->id,
                                $record->requestor_first_name,
                                $record->requestor_last_name,
                                $record->requestor_middle_name,
                                $record->requestor_age,
                                $record->email,
                                $record->contact_number,
                                $record->preferred_date->format('Y-m-d'),
                                $record->preferred_time,
                                $record->status,
                                $record->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;

                case 'sports':
                    fputcsv($handle, ['ID', 'First Name', 'Last Name', 'Middle Name', 'Age', 'Gender', 'Email', 'Contact Number', 'Sport', 'Team Name', 'Event Date', 'Remarks', 'Status', 'Date Submitted']);
                    SportsRegistration::chunk(100, function ($records) use ($handle) {
                        foreach ($records as $record) {
                            fputcsv($handle, [
                                $record->id,
                                $record->first_name,
                                $record->last_name,
                                $record->middle_name,
                                $record->age,
                                $record->gender,
                                $record->email,
                                $record->contact_number,
                                $record->sport,
                                $record->team_name,
                                $record->event_date->format('Y-m-d'),
                                $record->remarks,
                                $record->status,
                                $record->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;

                case 'profiling':
                    fputcsv($handle, [
                        'ID', 'Surname', 'First Name', 'Middle Name', 'Suffix (Ext)', 
                        'Age', 'Sex', 'Gender Identity', 'Date of Birth', 'Civil Status', 
                        'Purok', 'Street Address', 'Youth Classification', 'Contact Number', 
                        'Email Address', 'Registered SK Voter', 'Registered National Voter', 
                        'Attended KK Assembly', 'Part of Youth Org', 'Youth Org Name', 
                        'Interested in Joining', 'LGBTQIA+', 'PWD', 'Registered Disability', 
                        'Highest Educational Attainment', 'Processed By', 'Date Registered'
                    ]);
                    KkProfile::with(['purok', 'processedBy'])->chunk(100, function ($records) use ($handle) {
                        foreach ($records as $record) {
                            fputcsv($handle, [
                                $record->id,
                                $record->surname,
                                $record->first_name,
                                $record->middle_name,
                                $record->ext,
                                $record->age,
                                $record->sex,
                                $record->gender,
                                $record->dob ? $record->dob->format('Y-m-d') : '',
                                $record->civil_status,
                                $record->purok ? $record->purok->purok_name : '',
                                $record->street_address,
                                $record->youth_classification,
                                $record->contact_number,
                                $record->email,
                                $record->registered_sk_voter ? 'Yes' : 'No',
                                $record->registered_national_voter ? 'Yes' : 'No',
                                $record->attended_kk_assembly ? 'Yes' : 'No',
                                $record->part_of_youth_org ? 'Yes' : 'No',
                                $record->youth_org_name,
                                $record->interested_in_joining ? 'Yes' : 'No',
                                $record->part_of_lgbtqia ? 'Yes' : 'No',
                                $record->pwd ? 'Yes' : 'No',
                                $record->registered_disability,
                                $record->highest_educational_attainment,
                                $record->processedBy ? ($record->processedBy->role === 'user' ? 'Self Profiling' : $record->processedBy->name) : 'Self Profiling',
                                $record->created_at->format('Y-m-d H:i:s'),
                            ]);
                        }
                    });
                    break;
            }

            fclose($handle);
        }, 200, $headers);
    }
}
