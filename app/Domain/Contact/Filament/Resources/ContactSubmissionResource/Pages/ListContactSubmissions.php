<?php

namespace App\Domain\Contact\Filament\Resources\ContactSubmissionResource\Pages;

use App\Domain\Contact\Filament\Resources\ContactSubmissionResource;
use Filament\Resources\Pages\ListRecords;

class ListContactSubmissions extends ListRecords
{
    protected static string $resource = ContactSubmissionResource::class;
}
