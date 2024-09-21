<?php

namespace Tests\Feature;

use App\Models\Admin;
use App\Models\Comment;
use App\Models\Profile;
use App\Models\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;


    public function test_store_comment_success()
    { 
        
        $adminRole = Role::create(['name' => 'admin']);

       
        $user = User::factory()->create(['role_id' => $adminRole->id]);


        Sanctum::actingAs($user);

        $profile = Profile::factory()->create();
        
        $data = [
            'content' => 'This is a test comment',
            'profile_id' => $profile->id
        ];

        $response = $this->postJson("/api/profiles/{$profile->id}/comments", $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('comments', ['content' => 'This is a test comment']);
    }


    public function test_store_comment_already_exists()
    {
     
        $adminRole = Role::create(['name' => 'admin']);


        $user = User::factory()->create(['role_id' => $adminRole->id]);
 
         
        Sanctum::actingAs($user);

        $profile = Profile::factory()->create();
        $admin = auth()->user();

        Comment::create([
            'content' => 'Existing comment',
            'administrator_id' => $admin->id,
            'profile_id' => $profile->id
        ]);

        $data = [
            'content' => 'New comment',
            'profile_id' => $profile->id
        ];

        $response = $this->postJson("/api/profiles/{$profile->id}/comments", $data);

        $response->assertStatus(400);
        $response->assertJson(['message' => 'You have already commented on this profile']);
    }

    public function test_store_profile_success()
    {
        $adminRole = Role::create(['name' => 'admin']);

       
        $user = User::factory()->create(['role_id' => $adminRole->id]);


        Sanctum::actingAs($user);
        $data = [
            'last_name' => 'Doe',
            'first_name' => 'John',
            'status' => 'active',
            'image' => \Illuminate\Http\UploadedFile::fake()->image('profile.jpg')
        ];

        $response = $this->postJson('/api/profiles', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('profiles', ['last_name' => 'Doe']);
    }

    public function test_store_profile_image_missing()
    {
        $adminRole = Role::create(['name' => 'admin']);

       
        $user = User::factory()->create(['role_id' => $adminRole->id]);


        Sanctum::actingAs($user);

        $data = [
            'last_name' => 'Doe',
            'first_name' => 'John',
            'status' => 'active'
        ];

        $response = $this->postJson('/api/profiles', $data);

        $response->assertStatus(400);
        $response->assertJson(['error' => 'Image file is required.']);
    }
    public function test_get_active_profiles()
    {
        Profile::factory()->create(['status' => 'active']);
        Profile::factory()->create(['status' => 'inactive']);
    
        $response = $this->getJson('/api/getprofiles');
    
        $response->assertStatus(200);
        $response->assertJsonCount(1);  // Only one active profile
    }
    
    public function test_get_all_profiles()
    {
        $adminRole = Role::create(['name' => 'admin']);

       
        $user = User::factory()->create(['role_id' => $adminRole->id]);


        Sanctum::actingAs($user);
    
        Profile::factory()->count(3)->create();
    
        $response = $this->getJson('/api/profiles');
    
        $response->assertStatus(200);
        $response->assertJsonCount(3);  // All profiles
    }

    public function test_update_profile_success()
    {
        $adminRole = Role::create(['name' => 'admin']);

       
        $user = User::factory()->create(['role_id' => $adminRole->id]);


        Sanctum::actingAs($user);

        $profile = Profile::factory()->create();

        $data = [
            'last_name' => 'Updated',
            'first_name' => 'Name',
            'status' => 'inactive',
            'image' => \Illuminate\Http\UploadedFile::fake()->image('newprofile.jpg')
        ];

        $response = $this->postJson("/api/updateProfiles/{$profile->id}", $data);

        $response->assertStatus(200);
        $this->assertDatabaseHas('profiles', ['last_name' => 'Updated']);
}

    public function test_update_profile_validation_error()
    {
        $adminRole = Role::create(['name' => 'admin']);

       
        $user = User::factory()->create(['role_id' => $adminRole->id]);


        Sanctum::actingAs($user);

        $profile = Profile::factory()->create();

        $data = [
            'last_name' => '',  // Missing field
            'first_name' => 'Name',
        ];

        $response = $this->postJson("/api/updateProfiles/{$profile->id}", $data);

        $response->assertStatus(400);
    }

    public function test_delete_profile_success()
    {
        $adminRole = Role::create(['name' => 'admin']);

       
        $user = User::factory()->create(['role_id' => $adminRole->id]);


        Sanctum::actingAs($user);

        $profile = Profile::factory()->create();

        $response = $this->deleteJson("/api/deleteProfiles/{$profile->id}");

        $response->assertStatus(200);
        $this->assertDatabaseMissing('profiles', ['id' => $profile->id]);
    }

}
