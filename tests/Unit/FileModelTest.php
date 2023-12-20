<?php

namespace Tests\Unit;

use App\Models\User;
use App\Utilities\MessageFileModel;
use Illuminate\Foundation\Testing\RefreshDatabase;
use stdClass;
use Tests\TestCase;

class FileModelTest extends TestCase
{
    use RefreshDatabase;

    public function test_file_model_records_count(): void
    {
        $user = User::factory()->create();
        $user->id = 4;

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $this->assertTrue($messageFileModel->all()->numberOfRecords() == 6);

        $user->id = 2;
        $this->assertTrue($messageFileModel->all()->numberOfRecords() == 2);

        $user->id = 1;
        $this->assertTrue($messageFileModel->all()->numberOfRecords() == 0);
    }

    public function test_file_model_records_get(): void
    {
        $user = User::factory()->create();
        $user->id = 4;

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));

        $messageEvent = $messageFileModel->all()->get('1');
        $this->assertFalse(property_exists($messageEvent, 'id'));
        $this->assertTrue($messageEvent == new stdClass());

        $messageEvent = $messageFileModel->all()->get('9ac8b91c-9747-435e-9384-50ba010f3bb8');
        $this->assertTrue(property_exists($messageEvent, 'id'));
        $this->assertTrue($messageEvent->id == '9ac8b91c-9747-435e-9384-50ba010f3bb8');
    }

    public function test_file_model_records_get_correct_values(): void
    {
        $user = User::factory()->create();
        $user->id = 4;

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $messageEvent = $messageFileModel->all()->get('9ac8b91c-9747-435e-9384-50ba010f3bb8');

        $this->assertTrue($messageEvent->route == "client.profile.show");
        $this->assertTrue($messageEvent->user_id == "4");
    }

    public function test_file_model_records_access_levels(): void
    {
        $user = User::factory()->create();
        $user->id = 4;
        $user->role->name = "Admin";

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $messageEvent = $messageFileModel->all()->get('9ac8b91f-ca30-4789-b196-aa838e3a68dd');
        $this->assertTrue(property_exists($messageEvent, 'id'));
        $this->assertTrue($messageEvent->id == '9ac8b91f-ca30-4789-b196-aa838e3a68dd');

        $user->role->name = "Member";
        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $messageEvent = $messageFileModel->all()->get('9ac8b91f-ca30-4789-b196-aa838e3a68dd');
        $this->assertFalse(property_exists($messageEvent, 'id'));

        $user->role->name = "Super Admin";
        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $messageEvent = $messageFileModel->all()->get('9ac8b91f-ca30-4789-b196-aa838e3a68dd');
        $this->assertTrue(property_exists($messageEvent, 'id'));
        $this->assertTrue($messageEvent->id == '9ac8b91f-ca30-4789-b196-aa838e3a68dd');
    }
}
