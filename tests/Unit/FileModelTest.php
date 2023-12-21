<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\User;
use App\Utilities\MessageFileModel;
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

    public function test_file_model_records_pagination(): void
    {
        $user = User::factory()->create();
        $user->id = 4;

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $messageEvents = $messageFileModel->all()->paginate(5);
        $this->assertCount(5, $messageEvents["messages"]);
        $this->assertCount(4, $messageEvents["links"]);

        $messageEvents = $messageFileModel->all()->paginate(3);
        $this->assertCount(3, $messageEvents["messages"]);
        $this->assertCount(4, $messageEvents["links"]);

        $messageEvents = $messageFileModel->all()->paginate();
        $this->assertCount(6, $messageEvents["messages"]);
        $this->assertCount(3, $messageEvents["links"]);
    }

    public function test_file_model_records_get_number(): void
    {
        $user = User::factory()->create();
        $user->id = 4;

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $this->assertCount(5, $messageFileModel->all()->getRecords(5));
        $this->assertCount(6, $messageFileModel->all()->getRecords(0));
        $this->assertCount(6, $messageFileModel->all()->getRecords());
    }

    public function test_file_model_records_order_by(): void
    {
        $user = User::factory()->create();
        $user->id = 4;

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));

        $messageEvents = $messageFileModel->all()->orderBy('route', 'asc')->getRecords();
        $this->assertTrue($messageEvents[0]->id == '9ac8b91f-2061-4b4a-a351-d43e1de4ff18');

        $messageEvents = $messageFileModel->all()->orderBy('route', 'desc')->getRecords();
        $this->assertTrue($messageEvents[0]->id == '9ac8b91e-68c4-4a6f-82c2-23384ff2b66e');
    }

    public function test_file_model_records_filter_by(): void
    {
        $user = User::factory()->create();
        $user->id = 4;

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $messageEvents = $messageFileModel->all()->filterBy('route', 'client.profile.show')->getRecords();
        $this->assertCount(1, $messageEvents);
        $this->assertTrue(reset($messageEvents)->id == '9ac8b91c-9747-435e-9384-50ba010f3bb8');

        $messageEvents = $messageFileModel->all()->filterBy('route', 'client.notifications.get')->getRecords();
        $this->assertCount(1, $messageEvents);
        $this->assertTrue(reset($messageEvents)->id == '9ac8b91f-ca30-4789-b196-aa838e3a68dc');
    }

    public function test_file_model_records_get_unique_values(): void
    {
        $user = User::factory()->create();
        $user->id = 4;

        request()->setUserResolver(function () use ($user) {
            return $user;
        });

        $messageFileModel = new MessageFileModel(storage_path('files/example.log'));
        $this->assertTrue(count($messageFileModel->all()->getUniqueValues()["route"]) == 6);
        $this->assertContains('client.notifications.get', $messageFileModel->all()->getUniqueValues()["route"]);
        $this->assertTrue(count($messageFileModel->all()->getUniqueValues()["app_id"]) == 1);
        $this->assertTrue(count($messageFileModel->all()->getUniqueValues()["ip_address"]) == 1);
        $this->assertTrue(count($messageFileModel->all()->getUniqueValues()["id"]) == 6);
        $this->assertContains('9ac8b91f-ca30-4789-b196-aa838e3a68dc', $messageFileModel->all()->getUniqueValues()["id"]);
    }
}
