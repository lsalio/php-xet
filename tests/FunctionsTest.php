<?php
/**
 * Xet - Utility methods for php
 *
 * @package   php-xet
 * @author    Jayson Wang <jayson@laboys.org>
 * @copyright Copyright (C) 2020 Jayson Wang
 * @license   MIT License
 * @link      https://github.com/lsalio/php-xet
 */
namespace Xet\Tests;

use PHPUnit\Framework\Error\Warning;
use PHPUnit\Framework\TestCase;
use stdClass;
use function Xet\aes_decrypt;
use function Xet\aes_encrypt;
use function Xet\array_any;
use function Xet\array_at;
use function Xet\array_every;
use function Xet\base64_decode_safe;
use function Xet\base64_encode_safe;
use function Xet\str_contains;
use function Xet\str_has_prefix;
use function Xet\str_has_suffix;


/**
 * Class FunctionsTest
 *
 * @package Xet\Tests
 */
class FunctionsTest extends TestCase {

    protected $plain_text = "text中文🔥\u2333\xff";

    protected $array_data = [
        'a' => [
            'b' => [
                'c' => [
                    'd' => 'simple'
                ]
            ],
            1 => [
                /* 0 => */'has_int'
            ]
        ],
        'a.b' => [
            'c.d' => [
                /* 0 => */'delimiter'
            ]
        ],
    ];

    public function testBase64SafeEncoder() {
        $encoded = base64_encode_safe($this->plain_text);
        $this->assertEquals('dGV4dOS4reaWh_CflKVcdTIzMzP_', $encoded);
        $this->assertEquals($this->plain_text, base64_decode_safe($encoded));
    }

    public function testBase64SafeEncoderFailure() {
        $this->assertEquals('', base64_decode_safe('🔥'));
    }

    public function testAesEncrypt() {
        $encrypted = aes_encrypt($this->plain_text, 'password', '0123456789012345');
        $this->assertEquals('6OGaCQsiTGwJ_EgPqKJ9UKxMJzzYAPOwLcLWfjlJCSQ', $encrypted);
        $this->assertEquals($this->plain_text, aes_decrypt($encrypted, 'password', '0123456789012345'));
    }

    public function testAesDecryptFailure() {
        $this->assertEquals(false, aes_decrypt('invalid', 'incorrect'));
    }

    public function testAesEncryptInvalidIv() {
        $this->expectException(Warning::class);
        aes_encrypt($this->plain_text, 'password', 'invalid');
    }

    public function testArrayAt() {
        $this->assertEquals('simple', array_at($this->array_data, 'a.b.c.d'));
        $this->assertEquals('has_int', array_at($this->array_data, 'a.1.0'));
        $this->assertEquals('delimiter', array_at($this->array_data, 'a.b->c.d->0', null, '->'));
        $this->assertEquals('default', array_at($this->array_data, 'x.y.z', 'default'));
    }

    public function testStrHasPrefix() {
        $this->assertEquals(true, str_has_prefix($this->plain_text, 'text中文🔥'));
        $this->assertEquals(false, str_has_prefix($this->plain_text, "🔥\u2333\xff"));
    }

    public function testStrHasSuffix() {
        $this->assertEquals(false, str_has_suffix($this->plain_text, 'text中文🔥'));
        $this->assertEquals(true, str_has_suffix($this->plain_text, "🔥\u2333\xff"));
    }

    public function testStrContains() {
        $this->assertEquals(true, str_contains($this->plain_text, 'text中文🔥'));
        $this->assertEquals(true, str_contains($this->plain_text, '🔥'));
        $this->assertEquals(true, str_contains($this->plain_text, "\xff"));
    }

    public function testArrayAny() {
        $this->assertEquals(true, array_any([true, false]));
        $this->assertEquals(true, array_any([1, 0]));
        $this->assertEquals(true, array_any([-1, 0]));
        $this->assertEquals(false, array_any([0, false, [], '']));
    }

    public function testArrayAnyCustomHandler() {
        $this->assertEquals(true, array_any([false, 0, [], ''], function($value) {
            return $value === 0;
        }));
    }

    public function testArrayEvery() {
        $this->assertEquals(true, array_every([true, new stdClass(), 1, -1, [[]]]));
        $this->assertEquals(false, array_every([true, new stdClass(), '', -1, [[]]]));
    }

    public function testArrayEveryCustomHandler() {
        $this->assertEquals(true, array_any([false, 0, [], ''], function($value) {
            return !boolval($value);
        }));
    }

}
