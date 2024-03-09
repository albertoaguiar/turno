<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserControllerTest extends TestCase
{
    use RefreshDatabase;

    public function testIndex()
    {
        // Criar usuários fictícios no banco de dados
        User::factory()->count(3)->create();

        // Fazer uma requisição para a rota do método index do UserController
        $response = $this->get('/api/v1/users');

        // Verificar se a resposta contém a lista de usuários
        $response->assertStatus(200)
            ->assertJsonCount(3, 'data');
    }

    public function testStore()
    {
        // Dados do usuário para criar
        $userData = [
            'name' => 'Test Name',
            'password' => 'password',
            'balance' => 100,
            'user_type' => 'A',
            'email' => 'test@example.com',
        ];

        // Fazer uma requisição para a rota do método store do UserController
        $response = $this->post('/api/v1/users', $userData);

        // Verificar se o usuário foi criado com sucesso
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'User created.']);
    }

    public function testShow()
    {
        // Criar um usuário fictício no banco de dados
        $user = User::factory()->create();

        // Fazer uma requisição para a rota do método show do UserController
        $response = $this->get("/api/v1/users/{$user->id}");

        // Verificar se a resposta contém os dados do usuário
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'data' => $user->toArray()]);
    }

    public function testUpdate()
    {
        // Criar um usuário fictício no banco de dados
        $user = User::factory()->create();

        // Dados do usuário para atualizar
        $updatedUserData = [
            'name' => 'Test Name Update'
            'password' => 'newpassword',
            'balance' => 200,
            'user_type' => 'C',
            'email' => 'updated@example.com',
        ];

        // Fazer uma requisição para a rota do método update do UserController
        $response = $this->put("/api/v1/users/{$user->id}", $updatedUserData);

        // Verificar se o usuário foi atualizado com sucesso
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'User updated.']);
    }

    public function testDestroy()
    {
        // Criar um usuário fictício no banco de dados
        $user = User::factory()->create();

        // Fazer uma requisição para a rota do método destroy do UserController
        $response = $this->delete("/api/v1/users/{$user->id}");

        // Verificar se o usuário foi deletado com sucesso
        $response->assertStatus(200)
            ->assertJson(['success' => true, 'message' => 'User deleted.']);
    }
}