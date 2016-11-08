<?php

/*
 * This file is part of the WebPush library.
 *
 * (c) Louis Lagrange <lagrange.louis@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Minishlink\WebPush\Encryption;
use Minishlink\WebPush\Utils;

class EncryptionTest extends PHPUnit_Framework_TestCase
{
    protected function checkRequirements()
    {
        parent::checkRequirements();

        if (!array_key_exists('skipIfTravis', $this->getAnnotations()['method'])) {
            return;
        }

        if (getenv('TRAVIS') || getenv('CI')) {
            $this->markTestSkipped('This test does not run on Travis.');
        }
    }

    /**
     * @dataProvider payloadProvider
     *
     * @param string $payload
     */
    public function testPadPayload($payload)
    {
        $res = Encryption::padPayload($payload, true);

        $this->assertContains('test', $res);
        $this->assertEquals(4080, Utils::safeStrlen($res));
    }

    public function payloadProvider()
    {
        return array(
            array('testé'),
            array(str_repeat('test', 1019)),
            array(str_repeat('test', 1019).'te'),
        );
    }

    /**
     * @skipIfTravis
     */
    public function testEncrypt()
    {
        $publicKey = getenv('USER_PUBLIC_KEY');
        $authToken = getenv('USER_AUTH_TOKEN');
        $n = 50;

        $start = microtime(true);

        for ($i = 0; $i < $n; $i++) {
            Encryption::encrypt("plop", $publicKey, $authToken, false);
        }

        $total = microtime(true) - $start;

        echo "[testEncrypt] Encryption duration: ".round($total/$n*1000, 1)." ms for a total (".$n.") of ".round($total, 2)."s.";
    }
}
