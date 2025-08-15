<?php

// Script to fix import issues
$files = [
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\CellMemberResource\Forms\Fields\CellMemberAdditionalFields.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\CellMemberResource\Forms\Sections\TrainingDevelopmentSection.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\CellMemberResource\Forms\Sections\AdditionalInformationSection.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\LeaderResource\Forms\Fields\AdditionalFields.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\LeaderResource\Forms\Fields\TrainingFields.php',
    'c:\laragon\www\FilamentCMS\app\Filament\Resources\LeaderResource\Forms\Fields\LeadershipFields.php',
];

foreach ($files as $file) {
    if (file_exists($file)) {
        $content = file_get_contents($file);
        
        // Fix incorrect import placement
        if (strpos($content, "<?php\n\nuse App\\Filament\\Shared\\Helpers\\FormHelpers;\n\nnamespace") !== false) {
            // Remove the incorrectly placed import
            $content = str_replace(
                "<?php\n\nuse App\\Filament\\Shared\\Helpers\\FormHelpers;\n\nnamespace",
                "<?php\n\nnamespace",
                $content
            );
            
            // Find the namespace line and add imports after it
            $lines = explode("\n", $content);
            $newLines = [];
            $namespaceFound = false;
            
            foreach ($lines as $line) {
                $newLines[] = $line;
                
                if (strpos($line, 'namespace ') === 0 && !$namespaceFound) {
                    $newLines[] = '';
                    $newLines[] = 'use App\Filament\Shared\Helpers\FormHelpers;';
                    $namespaceFound = true;
                }
            }
            
            $content = implode("\n", $newLines);
        }
        
        file_put_contents($file, $content);
        echo "Fixed imports: $file\n";
    } else {
        echo "File not found: $file\n";
    }
}

echo "Import fix completed!\n";
