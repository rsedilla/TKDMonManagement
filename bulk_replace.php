<?php

// Script to replace disable logic across all files
$files = [
    // CellMember files
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\CellMemberResource\Forms\Fields\CellMemberAdditionalFields.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\CellMemberResource\Forms\Sections\PersonalInformationSection.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\CellMemberResource\Forms\Sections\TrainingDevelopmentSection.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\CellMemberResource\Forms\Sections\AdditionalInformationSection.php',
    
    // Leader files
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\LeaderResource\Forms\Fields\AdditionalFields.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\LeaderResource\Forms\Fields\TrainingFields.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\LeaderResource\Forms\Fields\LeadershipFields.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Add FormHelpers import if not present
        if (strpos($content, 'use App\Filament\Shared\Helpers\FormHelpers;') === false) {
            $content = str_replace(
                '<?php',
                "<?php\n\nuse App\\Filament\\Shared\\Helpers\\FormHelpers;",
                $content
            );
        }
        
        // Replace disable logic
        $content = str_replace(
            '->disabled(fn (string $operation) => $operation === \'view\')',
            '->disabled(FormHelpers::getDisabledClosure())',
            $content
        );
        
        file_put_contents($file, $content);
        echo "Updated: $file\n";
    } else {
        echo "File not found: $file\n";
    }
}

echo "Bulk replacement completed!\n";
