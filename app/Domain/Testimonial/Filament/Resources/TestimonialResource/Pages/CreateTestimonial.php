<?php

namespace App\Domain\Testimonial\Filament\Resources\TestimonialResource\Pages;

use App\Domain\Testimonial\Filament\Resources\TestimonialResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTestimonial extends CreateRecord
{
    protected static string $resource = TestimonialResource::class;
}
