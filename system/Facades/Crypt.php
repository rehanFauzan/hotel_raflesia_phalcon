<?php
namespace Core\Facades;

/**
 * Class Crypt
 * @package Core\Facades
 * Provides encryption capabilities to Phalcon applications.
 * 
 * @method static string getAuthTag()
 *
 * @method static string getAuthData()
 *
 * @method static int getAuthTagLength()
 *
 * @method static string decrypt(string $text, string $key = null)
 * Decrypts an encrypted text.
 *
 * ```php
 * $encrypted = Crypt::decrypt(
 *     $encrypted,
 *     "T4\xb1\x8d\xa9\x98\x05\\\x8c\xbe\x1d\x07&[\x99\x18\xa4~Lc1\xbeW\xb3"
 * );
 * ```
 *
 * @method static string decryptBase64(string $text, $key = null, bool $safe = false)
 * Decrypt a text that is coded as a base64 string.
 *
 * @method static string encrypt(string $text, string $key = null)
 * Encrypts a text.
 *
 * ```php
 * $encrypted = Crypt::encrypt(
 *     "Top secret",
 *     "T4\xb1\x8d\xa9\x98\x05\\\x8c\xbe\x1d\x07&[\x99\x18\xa4~Lc1\xbeW\xb3"
 * );
 * ```
 *
 * @method static string encryptBase64(string $text, $key = null, bool $safe = false)
 * Encrypts a text returning the result as a base64 string.
 *
 * @method static array getAvailableCiphers()
 * Returns a list of available ciphers.
 *
 * @method static array getAvailableHashAlgos()
 * Return a list of registered hashing algorithms suitable for hash_hmac.
 *
 * @method static string getCipher()
 * Returns the current cipher
 *
 * @method static string getHashAlgo()
 * Get the name of hashing algorithm.
 *
 * @method static string getKey()
 * Returns the encryption key
 *
 * @method static \Phalcon\Crypt\CryptInterface setAuthTag(string $tag)
 *
 * @method static \Phalcon\Crypt\CryptInterface setAuthData(string $data)
 *
 * @method static \Phalcon\Crypt\CryptInterface setAuthTagLength(int $length)
 *
 * @method static \Phalcon\Crypt\CryptInterface setCipher(string $cipher)
 * Sets the cipher algorithm for data encryption and decryption.
 *
 * The `aes-256-gcm' is the preferable cipher, but it is not usable
 * until the openssl library is upgraded, which is available in PHP 7.1.
 *
 * The `aes-256-ctr' is arguably the best choice for cipher
 * algorithm for current openssl library version.
 *
 * @method static \Phalcon\Crypt\CryptInterface setHashAlgo(string $hashAlgo)
 * Set the name of hashing algorithm.
 *
 * @method static \Phalcon\Crypt\CryptInterface setKey(string $key)
 * Sets the encryption key.
 *
 * The `$key' should have been previously generated in a cryptographically
 * safe way.
 *
 * Bad key:
 * "le password"
 *
 * Better (but still unsafe):
 * "#1dj8$=dp?.ak//j1V$~%0X"
 *
 * Good key:
 * "T4\xb1\x8d\xa9\x98\x05\\\x8c\xbe\x1d\x07&[\x99\x18\xa4~Lc1\xbeW\xb3"
 *
 * @method static \Phalcon\Crypt\CryptInterface setPadding(int $scheme)
 * Changes the padding scheme used.
 *
 * @method static \Phalcon\Crypt\CryptInterface useSigning(bool $useSigning)
 * Sets if the calculating message digest must used.
 *
 */
class Crypt extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'crypt';
    }
}