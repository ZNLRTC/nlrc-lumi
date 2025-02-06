@php
    $record = $getRecord();
    
    // Livewire snapshots need this, or stuff breaks when the Filament table refreshes
    $uniqueKey = 'exam_attempt' . '-' . $record->id . '-' . uniqid(); 
@endphp

<livewire:exams.helpers.score-column :record="$record" :key="$uniqueKey" />