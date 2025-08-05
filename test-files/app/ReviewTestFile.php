<?php

namespace App\Test;

use App\Models\Conversion;
use App\Http\Resources\ConversionResource;
use Carbon\Carbon;

class ReviewTestFile
{
    public function __construct()
    {
        // Violation: Using the env() helper outside of a config file
        $apiKey = env('API_KEY');
    }

    public function someMethod($value)
    {
        // Violation: Unnecessary resource response chain
        return (new ConversionResource(Conversion::first()))
            ->response()
            ->setStatusCode(200);

        // Violation: Duplicated logic
        Conversion::updateOrCreate(
            ['integer_value' => $value],
            [
                'roman_numeral' => 'I',
                'last_converted_at' => Carbon::now(),
            ]
        );
    }
}
