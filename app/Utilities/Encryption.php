<?php

namespace App\Utilities;

use App\Models\User;

class Encryption
{
    const METHOD = 'aes-256-ctr';

    /**
     * Encrypts a resource.
     *
     * @param Int $key
     * @param String $resource
     * @return void
     */
    public static function encrypt_using_key($key, $resource)
    {
        if ($resource == null || $resource == '') {
            return $resource;
        }
        $code = unpack("H*", env("ENC_KEY"));
        $code = array_pop($code);
        $code_key = hex2bin($code);

        $nonce = base64_decode($key);
        $ciphertext = openssl_encrypt(
            $resource,
            self::METHOD,
            $code_key,
            OPENSSL_RAW_DATA,
            $nonce
        );
        return base64_encode($ciphertext);
    }

    /**
     * Decrypts the value of a resource based on the user's
     * decrypt key.
     *
     * @param Int $user_id
     * @param String $resource
     * @return String $plaintext
     */
    public static function decryptPersonal($user_id, $resource)
    {
        $user = User::findOrFail($user_id);
        $code = unpack("H*", env("ENC_KEY"));
        $code = array_pop($code);
        $code_key = hex2bin($code);

        if ($user->enc_key == null) {
            return $resource;
        }

        $nonce = base64_decode($user->enc_key);
        $data = base64_decode($resource, true);


        $plaintext = openssl_decrypt(
            $data,
            self::METHOD,
            $code_key,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }

    /**
     * Decrypts the value of a resource based on the user's
     * decrypt key.
     *
     * @param Object& $obj
     * @param Int $user_id
     * @return void
     */
    public static function decryptPersonalObject(&$obj, $user_id)
    {
        if (empty($obj)) {
            return;
        }

        $reflectionClass = new \ReflectionClass(get_class($obj));
        $fillables = $reflectionClass->getProperty("decryptable");
        $fillables->setAccessible(true);
        $attributes = $fillables->getValue($obj);
        foreach ($attributes as $attribute) {
            if ($obj->{$attribute} != null) {
                $val = $obj->{$attribute};
                $obj->{$attribute} =  Encryption::decryptPersonal($user_id, $val);
            }
        }
    }

    /**
     * Decrypts the value of a resource based on a given key.
     *
     * @param String $key
     * @param String $resource
     * @return String $plaintext
     */
    public static function decrypt_using_key($key, $resource)
    {
        if ($resource == null || $resource == '') {
            return $resource;
        }
        $code = unpack("H*", env("ENC_KEY"));
        $code = array_pop($code);
        $code_key = hex2bin($code);

        $nonce = base64_decode($key);
        $data = base64_decode($resource, true);

        $plaintext = openssl_decrypt(
            $data,
            self::METHOD,
            $code_key,
            OPENSSL_RAW_DATA,
            $nonce
        );

        return $plaintext;
    }

    /**
     * Creates a personal key to be stored in the DB
     * per user.
     *
     * @return String
     */
    public static function createPersonalKey()
    {
        $nonceSize = openssl_cipher_iv_length(self::METHOD);
        $nonce = openssl_random_pseudo_bytes($nonceSize);
        return base64_encode($nonce);
    }
}
