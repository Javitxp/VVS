<?php

use App\Services\UserDataManager;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class CreateUserTest extends TestCase
{
    /**
     * @test
     */
    public function CreatesUserSuccessfully()
    {

        $userDataManagerMock = Mockery::mock(UserDataManager::class);
        $userDataManagerMock->expects('createUser')->andReturn((object)[
            'username' => 'nuevo_usuario',
        ]);
        $this->app->instance(UserDataManager::class, $userDataManagerMock);


        $userData = [
            'username' => 'nuevo_usuario',
            'password' => 'nueva_contraseña'
        ];


        $response = $this->postJson('/analytics/users', $userData);


        $response->assertStatus(201);
        $response->assertJson([
            'username' => 'nuevo_usuario',
            'message' => 'Usuario creado correctamente'
        ]);
    }

    /**
     * @test
     */
    public function ReturnsBadRequestWhenParametersAreMissing()
    {
        // Mock del UserDataManager
        $userDataManagerMock = Mockery::mock(UserDataManager::class);
        $this->app->instance(UserDataManager::class, $userDataManagerMock);


        $userData = [
            'password' => 'nueva_contraseña'
        ];

        $response = $this->postJson('/analytics/users', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username']);


        $userData = [
            'username' => 'nuevo_usuario'
        ];

        $response = $this->postJson('/analytics/users', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['password']);


        $userData = [];

        $response = $this->postJson('/analytics/users', $userData);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['username', 'password']);
    }

    /**
     * @test
     */
    public function ReturnsConflictWhenUsernameIsTaken()
    {

        $userDataManagerMock = Mockery::mock(UserDataManager::class);
        $userDataManagerMock->expects('createUser')
            ->andThrow(new Exception("El nombre de usuario ya está en uso.", ErrorCodes::USERS_409));
        $this->app->instance(UserDataManager::class, $userDataManagerMock);


        $userData = [
            'username' => 'nuevo_usuario',
            'password' => 'nueva_contraseña'
        ];


        $response = $this->postJson('/analytics/users', $userData);


        $response->assertStatus(409);
        $response->assertJson(['error' => 'El nombre de usuario ya está en uso.']);
    }

    /**
     * @test
     */
    public function ReturnsServerErrorWhenFailInCreateUser()
    {

        $userDataManagerMock = Mockery::mock(UserDataManager::class);
        $userDataManagerMock->expects('createUser')
            ->andThrow(new Exception("Error del servidor al crear el usuario.", ErrorCodes::USERS_500));
        $this->app->instance(UserDataManager::class, $userDataManagerMock);

        
        $userData = [
            'username' => 'nuevo_usuario',
            'password' => 'nueva_contraseña'
        ];


        $response = $this->postJson('/analytics/users', $userData);


        $response->assertStatus(500);
        $response->assertJson(['error' => 'Error del servidor al crear el usuario.']);
    }
}
