<?php

namespace Tests\Unit\Support;

use App\Support\EncryptedValue;
use App\Support\SensitiveDataHasher;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class SensitiveDataEncryptionTest extends TestCase
{
    public function test_document_hash_is_stable_and_normalized(): void
    {
        $hashA = SensitiveDataHasher::hashDocument(' 12345678 ');
        $hashB = SensitiveDataHasher::hashDocument('12345678');

        $this->assertSame($hashA, $hashB);
        $this->assertNotNull($hashA);
    }

    public function test_encrypted_value_detector(): void
    {
        $encrypted = Crypt::encryptString('40123456');

        $this->assertTrue(EncryptedValue::isEncrypted($encrypted));
        $this->assertFalse(EncryptedValue::isEncrypted('40123456'));
    }
}
