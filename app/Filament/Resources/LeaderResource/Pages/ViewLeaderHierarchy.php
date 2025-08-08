<?php

namespace App\Filament\Resources\LeaderResource\Pages;

use App\Filament\Resources\LeaderResource;
use Filament\Resources\Pages\Page;

class ViewLeaderHierarchy extends Page
{
    protected static string $resource = LeaderResource::class;

    protected static string $view = 'filament.resources.leader-resource.pages.view-leader-hierarchy';
}
