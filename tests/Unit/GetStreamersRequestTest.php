<?php

use App\Infrastructure\Requests\GetStreamersRequest;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Validator;
use Tests\TestCase;

class GetStreamersRequestTest extends TestCase
{
    use WithFaker;
    /**
     * @test
     */
    public function RequestFailsWithoutNumericId()
    {
        $request = new GetStreamersRequest();
        $validator = Validator::make([
            'id' => $this->faker->word,
        ], $request->rules());

        $response = $validator->fails();

        $this->assertTrue($response);
    }
    /**
     * @test
     */
    public function RequestPassesWithNumericId()
    {
        $request = new GetStreamersRequest();
        $validator = Validator::make([
            'id' => $this->faker->randomNumber(),
        ], $request->rules());

        $response = $validator->fails();

        $this->assertFalse($response);
    }
    /**
     * @test
     */
    public function RequestFailsWithoutId()
    {
        $request = new GetStreamersRequest();
        $validator = Validator::make([
            'id' => null,
        ], $request->rules());

        $response = $validator->fails();

        $this->assertTrue($response);
    }
}
