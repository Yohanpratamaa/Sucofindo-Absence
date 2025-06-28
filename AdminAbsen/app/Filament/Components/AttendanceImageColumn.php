<?php

namespace App\Filament\Components;

use Filament\Tables\Columns\ImageColumn;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;

class AttendanceImageColumn extends ImageColumn
{
    protected function setUp(): void
    {
        parent::setUp();
        
        // Set disk
        $this->disk('public');
        
        // Set default image
        $this->defaultImageUrl(asset('images/no-image.png'));
        
        // Custom state handling
        $this->getStateUsing(function ($record, $column) {
            $field = $column->getName();
            $imagePath = $record->$field;
            
            if (!$imagePath) {
                Log::info("No image path for field: {$field}", ['record_id' => $record->id]);
                return null;
            }
            
            // Check if file exists
            if (Storage::disk('public')->exists($imagePath)) {
                Log::info("Image found for field: {$field}", [
                    'record_id' => $record->id,
                    'path' => $imagePath,
                    'full_url' => asset('storage/' . $imagePath)
                ]);
                return $imagePath;
            }
            
            // Log missing image
            Log::warning('Attendance image not found', [
                'attendance_id' => $record->id,
                'field' => $field,
                'path' => $imagePath,
                'expected_storage_path' => Storage::disk('public')->path($imagePath),
                'expected_public_path' => public_path('storage/' . $imagePath)
            ]);
            
            return null;
        });
        
        // Add extra attributes for debugging
        $this->extraAttributes([
            'style' => 'border: 2px solid #e5e5e5; object-fit: cover;'
        ]);
    }
}
