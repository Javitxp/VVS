<?php

use App\Infrastructure\Clients\DBClient;
use App\Models\RegistredUser;
use App\Services\UserDataManager;
use App\Utilities\ErrorCodes;
use Tests\TestCase;
use Exception;
use Mockery;

class CreateUserTest extends TestCase
{
    protected DBClient $dbClientMock;

    protected function setUp(): void
    {
        parent::setUp();
        $this->dbClientMock = Mockery::mock(DBClient::class);
        $this->app->instance(DBClient::class, $this->dbClientMock);
    }

    /**
     * @test
     */
    public function CreatesUserSuccessfully()
    {
        $user = new RegistredUser();
        $user->username = 'nuevo_usuario';
        $this->dbClientMock->expects('checkUsername')
            ->with('nuevo_usuario')
            ->andReturn(false);
        $this->dbClientMock->expects('insertUser')
            ->with('nuevo_usuario', 'nueva_contraseña')
            ->andReturn($user);
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
        $this->dbClientMock->expects('checkUsername')
            ->with('nuevo_usuario')
            ->andReturn(true);
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
        $user = new RegistredUser();
        $user->username = 'nuevo_usuario';
        $this->dbClientMock->expects('checkUsername')
            ->with('nuevo_usuario')
            ->andReturn(false);
        $this->dbClientMock->expects('insertUser')
            ->with('nuevo_usuario', 'nueva_contraseña')
            ->andThrows(new Exception("Error al crear el usuario.", ErrorCodes::USERS_500));
        $userData = [
            'username' => 'nuevo_usuario',
            'password' => 'nueva_contraseña'
        ];

        $response = $this->postJson('/analytics/users', $userData);

        $response->assertStatus(500);
        $response->assertJson(['error' => 'Error del servidor al crear el usuario.']);
    }
}
