<?php

namespace App\Domain\Comment\Filament\Resources\CommentResource\Pages;

use App\Domain\Comment\Filament\Resources\CommentResource;
use Filament\Resources\Pages\ListRecords;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;
}
