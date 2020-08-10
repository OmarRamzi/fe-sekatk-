<?php

namespace Tests\Feature;
use App\User;
use APP\Car;
use App\Ride;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Foundation\Testing\WithFaker;


class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {
        $response = $this->get('');

        $response->assertStatus(200);
    }

    /**
     * A login test.
     *
     * @test
     */

    public function LoginTest()
    {
        $response= $this->get('/home');
        $response->assertRedirect('/login');
    }

    /**
     * Authorized test.
     * @test
     * 
     */
    public function Authenticated_users_can_enter_to_home()
    {
        $response = $this->postJson('api/user', [
            'email'=>'test@test.com',
            'nationalId'=>'123456789',
            'phoneNumber'=>'123456789',
            'name'=>'test',
            'password'=>'123456789',
               
        ]);

        $response
           ->assertStatus(200);
            
            //->assertJson([
              //  'created' => true,
       // ]);
    }

    /**
     * 'Filling car details and store it' test.
     * @test
     */
    public function filling_car_details(){
        //built-in exception handler that allows you
        // to report and render exceptions easily and in a friendly manner.
        $this->withoutExceptionHandling();
        //to run it faster than ordinary times without firing events.
        Event::fake();

        $this->actingAs(factory(User::class)->create([
            'email'=>'test@test.com',
            'nationalId'=>'123456789',
            'phoneNumber'=>'123456789',
            'name'=>'test',
            'password'=>'123456789',
        ]));

        $response = $this->postJson('/fillCarDetails/1', [
           
            'user_id'=>1,
            'userLicense'=>'123456789',
            'license'=>'123456789',
            'carModel'=>'bmw',
            'nationalId'=>'123456789',
            'color'=>'red'
               ]);

               $response
            ->assertStatus(200);
        }
     /**
     * 'upload files' test.
     * @test
     * 
     */
    public function testAvatarUpload()
    {
        Storage::fake('avatars');

        $file = UploadedFile::fake()->image('avatar.jpg');

        $response = $this->json('POST', 'api/register', [
            'avatar' => $file,

        ]);
        // Assert the file was stored...
        //Storage::disk('avatars')->assertExists($file->hashName());
        $response-> assertStatus(200);
          }

    public function test_sending_request_details()
    {


        $response = $this->actingAs($this->user)
            ->postJson(route('\request'), [

            'email'=>'test@test.com',
            'nationalId'=>'123456789',
            'phoneNumber'=>'123456789',
            'name'=>'test',
            'password'=>'123456789',
            
            ]);

        $response>assertStatus(
            Response::HTTP_UNPROCESSABLE_ENTITY
        );   
    }

    public function test_get_all_requests()
    {
        $response = $this->get('api/allRides')->assertStatus(200);

       // $response->assertStatus(200);
    }
}
