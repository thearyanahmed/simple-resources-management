<?php

namespace Database\Factories;

use App\Models\HtmlSnippet;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class HtmlSnippetFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = HtmlSnippet::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'description' => $this->faker->realTextBetween(1,255),
            'markup' => str_replace("\n","",Str::of($this->faker->randomHtml())->limit(10000)),
        ];
    }
}
