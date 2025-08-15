<?php

namespace App\Filament\Shared\Factories;

use Filament\Forms\Components\Section;

class SectionFactory
{
    /**
     * Create a standard form section with common properties
     * 
     * @param string $title
     * @param string $description
     * @param string $icon
     * @param array $options
     * @return Section
     */
    public static function create(string $title, string $description, string $icon, array $options = []): Section
    {
        $defaults = [
            'collapsible' => true,
            'collapsed' => false,
            'columns' => 1,
        ];

        $config = array_merge($defaults, $options);

        $section = Section::make($title)
            ->description($description)
            ->icon($icon);

        if ($config['collapsible']) {
            $section->collapsible();
        }

        if ($config['collapsed']) {
            $section->collapsed();
        }

        if ($config['columns'] !== 1) {
            $section->columns($config['columns']);
        }

        return $section;
    }

    /**
     * Create a personal information section
     * 
     * @param array $options
     * @return Section
     */
    public static function personalInformation(array $options = []): Section
    {
        return self::create(
            'Personal Information',
            'Enter the basic personal details',
            'heroicon-o-user',
            $options
        );
    }

    /**
     * Create a training & development section
     * 
     * @param array $options
     * @return Section
     */
    public static function trainingDevelopment(array $options = []): Section
    {
        return self::create(
            'Training & Development',
            'Track learning and growth progress',
            'heroicon-o-academic-cap',
            array_merge(['collapsed' => true], $options)
        );
    }

    /**
     * Create an additional information section
     * 
     * @param array $options
     * @return Section
     */
    public static function additionalInformation(array $options = []): Section
    {
        return self::create(
            'Additional Information',
            'Contact details and progress tracking',
            'heroicon-o-document-text',
            array_merge(['collapsed' => true], $options)
        );
    }

    /**
     * Create a VIP information section
     * 
     * @param array $options
     * @return Section
     */
    public static function vipInformation(array $options = []): Section
    {
        return self::create(
            'VIP Information',
            'Enter the VIP (Very Important Person) details',
            'heroicon-o-user',
            $options
        );
    }

    /**
     * Create a consolidation details section
     * 
     * @param array $options
     * @return Section
     */
    public static function consolidationDetails(array $options = []): Section
    {
        return self::create(
            'Consolidation Details',
            'Consolidation process information',
            'heroicon-o-calendar',
            array_merge(['columns' => 2], $options)
        );
    }

    /**
     * Create a VIP progress tracking section
     * 
     * @param array $options
     * @return Section
     */
    public static function vipProgressTracking(array $options = []): Section
    {
        return self::create(
            'VIP Progress Tracking',
            'Track VIP progress through SUYNL lessons, Sunday services, and cell group attendance',
            'heroicon-o-chart-bar',
            $options
        );
    }
}
