<?php

namespace Database\Seeders;

use App\Models\Documents\AgencyDocumentRequired;
use App\Models\Documents\Document;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Document::insert([
            [
                'name' => 'Passport',
                'description' => 'Image of the data page of your passport, showing your name and details.', 'internal_notes' => 'Passport data page'
            ],
            [
                'name' => 'Visa',
                'description' => 'Image of the visa or the residence permit in the foreign country you are currently in.',
                'internal_notes' => 'Visa'
            ],
            [
                'name' => 'Curriculum Vitae',
                'description' => '',
                'internal_notes' => 'Curriculum Vitae'
            ],
            [
                'name' => 'Official Transcript of Records',
                'description' => '',
                'internal_notes' => 'Official Transcript of Records'
            ],
            [
                'name' => 'Diploma',
                'description' => 'For Bachelor of Science in Nursing only',
                'internal_notes' => 'Diploma'
            ],
            [
                'name' => 'Certificate of Completion',
                'description' => 'For Caregivers only',
                'internal_notes' => 'Certificate of Completion'
            ],
            [
                'name' => 'Certificate of Employment',
                'description' => '',
                'internal_notes' => 'Certificate of Employment'
            ],
            [
                'name' => 'Board Certificate',
                'description' => 'Lupon',
                'internal_notes' => 'Board Certificate'
            ],
            [
                'name' => 'License Identification',
                'description' => 'For Registered Nurses only - board passers',
                'internal_notes' => 'License Identification'
            ],
            [
                'name' => 'FIN - NBI Clearance SUO',
                'description' => 'Police Clearance issued from current country',
                'internal_notes' => 'FIN - NBI Clearance SUO'
            ],
            [
                'name' => 'Marriage Certificate',
                'description' => '',
                'internal_notes' => 'Marriage Certificate'
            ],
            [
                'name' => 'Medical Certificate',
                'description' => 'Negative TB and Salmonella',
                'internal_notes' => 'Medical Certificate'
            ],
            [
                'name' => 'COVID Vaccine with 3 doses',
                'description' => '2 for J&J',
                'internal_notes' => 'COVID Vaccine with 3 doses'
            ],
            [
                'name' => 'Complete Adult Immunization',
                'description' => 'TDAP, MMR, Polio, Influenza, and Varicella',
                'internal_notes' => 'Complete Adult Immunization'
            ],
            [
                'name' => 'Pre-Medical Fit to Work',
                'description' => '',
                'internal_notes' => 'Pre-Medical Fit to Work'
            ],
            [
                'name' => 'NC II',
                'description' => 'For caregivers only',
                'internal_notes' => 'NC II'
            ]
        ]);

        AgencyDocumentRequired::insert([
            ['agency_id' => 1, 'document_id' => 1, 'is_required' => true],
            ['agency_id' => 1, 'document_id' => 2, 'is_required' => true],
            ['agency_id' => 2, 'document_id' => 1, 'is_required' => true],
            ['agency_id' => 3, 'document_id' => 1, 'is_required' => true],
            ['agency_id' => 4, 'document_id' => 1, 'is_required' => true],
            ['agency_id' => 5, 'document_id' => 1, 'is_required' => true],
        ]);
    }
}
