<?php


use App\Models\User;

class AuthenticateControllerTest extends TestCase
{

    public function createApplication()
    {
        return require './bootstrap/app.php';
    }

    public function testUserShouldNotAuthenticateWithWrongProvider()
    {
        $payload = [
            'email' => 'cesar@email.com',
            'password' => 'secret123'
        ];

        $request = $this->post(route('authenticate', ['provider' => 'wrongProvider']), $payload);

        $request->assertResponseStatus(422);
        $request->seeJson(['error' => 'Wrong provider.']);
    }

    public function testUserShouldBeDeniedIfNotRegistered()
    {
        $payload = [
            'email' => 'test123@gmail.com',
            'password' => 'secret'
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request->assertResponseStatus(401);
        $request->seeJson(['error' => 'Wrong credentials.']);

    }

    public function testUserShouldSendWrongPassword()
    {
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'pass123'
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request->assertResponseStatus(401);
        $request->seeJson(['error' => 'Wrong credentials.']);

    }

    public function testUserCanAuthenticate()
    {
        $this->artisan('passport:install');
        $user = User::factory()->create();
        $payload = [
            'email' => $user->email,
            'password' => 'secret123'
        ];

        $request = $this->post(route('authenticate', ['provider' => 'user']), $payload);
        $request->assertResponseStatus(200);
        $request->seeJsonStructure(['access_token', 'expires_at', 'provider']);
    }
}


