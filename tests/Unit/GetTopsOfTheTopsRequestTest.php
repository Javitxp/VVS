<?php

use App\Infrastructure\Requests\GetTopsOfTheTopsRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class GetTopsOfTheTopsRequestTest extends TestCase
{
    use WithFaker;

    /**
     * @test
     */
    public function RequestPassesWithNumericId(): void
    {
        $request = new GetTopsOfTheTopsRequest();
        $validator = Validator::make([
            'since' => $this->faker->randomNumber(),
        ], $request->rules());

        $result = $validator->fails();

        $this->assertFalse($result);
    }
    /**
     * @test
     */
    public function RequestPassesWithNull(): void
    {
        $request = new GetTopsOfTheTopsRequest();
        $validator = Validator::make([
            'since' => null,
        ], $request->rules());

        $result = $validator->fails();

        $this->assertFalse($result);
    }
    /**
     * @test
     */
    public function RequestFailsWithNonNumericId(): void
    {
        $request = new GetTopsOfTheTopsRequest();
        $validator = Validator::make([
            'since' => $this->faker->word,
        ], $request->rules());

        $result = $validator->fails();

        $this->assertTrue($result);
    }
}
