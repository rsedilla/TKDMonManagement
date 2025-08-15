<?php

namespace App\Filament\Resources\ConsolidationResource\Forms\Configuration;

class ConsolidationFormConfiguration
{
    /**
     * Get form validation rules
     */
    public static function validationRules(): array
    {
        return [
            'vip_name' => 'required|string|max:255',
            'vip_contact_details' => 'required|string',
            'vip_address' => 'required|string',
            'vip_status' => 'required|string',
            'consolidation_date' => 'required|date',
            'consolidation_place' => 'required|string',
            'consolidator_type' => 'required|string',
            'consolidator_id' => 'required|integer',
        ];
    }

    /**
     * Get form field dependencies
     */
    public static function fieldDependencies(): array
    {
        return [
            'consolidator_selection' => ['consolidator_type', 'consolidator_id'],
            'change_consolidator' => ['current_consolidator', 'consolidator_selection'],
        ];
    }

    /**
     * Get progress tracking field patterns
     */
    public static function progressFieldPatterns(): array
    {
        return [
            'suynl_lessons' => 'suynl_lesson_{number}_date',
            'sunday_services' => 'sunday_service_{number}_date',
            'cell_group_sessions' => 'cell_group_{number}_date',
        ];
    }

    /**
     * Get progress tracking limits
     */
    public static function progressLimits(): array
    {
        return [
            'suynl_lessons' => 10,
            'sunday_services' => 4,
            'cell_group_sessions' => 4,
        ];
    }

    /**
     * Get form layout configuration
     */
    public static function layoutConfiguration(): array
    {
        return [
            'columns' => 1,
            'collapsible_sections' => true,
            'sticky_submit_button' => true,
            'auto_save' => false,
        ];
    }
}
