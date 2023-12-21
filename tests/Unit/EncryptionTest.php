<?php

namespace Tests\Unit;

use stdClass;
use Tests\TestCase;
use App\Models\User;
use App\Models\LogEntry;
use App\Utilities\Encryption;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EncryptionTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_encryption_key(): void
    {
        $key = Encryption::createPersonalKey();
        $this->assertTrue((bool) preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $key));
    }

    public function test_encrypt_using_key(): void
    {
        $key = Encryption::createPersonalKey();

        $test = "Test String!";
        $testEncrypted = Encryption::encryptUsingKey($key, $test);
        $this->assertEquals($test, Encryption::decryptUsingKey($key, $testEncrypted));

        $test = new stdClass;
        $test->name = "Test Name";
        $test->email = "test@email.com";
        $test->username = "Tester";
        $test = serialize($test);
        $testEncrypted = Encryption::encryptUsingKey($key, $test);
        $this->assertEquals(unserialize($test), unserialize(Encryption::decryptUsingKey($key, $testEncrypted)));

        $test = ["test", 1, "tester"];
        $test = serialize($test);
        $testEncrypted = Encryption::encryptUsingKey($key, $test);
        $this->assertEquals(unserialize($test), unserialize(Encryption::decryptUsingKey($key, $testEncrypted)));
    }

    public function test_encrypt_personal_object(): void
    {
        $user = User::factory()->create();
        $user->id = 1;
        $user->encryption_key = Encryption::createPersonalKey();
        $user->save();

        $test = LogEntry::factory()->create();
        $encryptedTest = $test;

        Encryption::encryptPersonalObject($encryptedTest, $user->id);
        Encryption::decryptPersonalObject($encryptedTest, $user->id);
        $this->assertEquals($test, $encryptedTest);
    }
}
