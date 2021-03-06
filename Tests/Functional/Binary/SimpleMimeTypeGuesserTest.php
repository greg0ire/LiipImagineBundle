<?php

/*
 * This file is part of the `liip/LiipImagineBundle` project.
 *
 * (c) https://github.com/liip/LiipImagineBundle/graphs/contributors
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Liip\ImagineBundle\Tests\Functional\Binary;

use Liip\ImagineBundle\Tests\Functional\WebTestCase;

class SimpleMimeTypeGuesserTest extends WebTestCase
{
    public function testCouldBeGetFromContainerAsService()
    {
        $this->createClient();

        $service = self::$kernel->getContainer()->get('liip_imagine.binary.mime_type_guesser');

        $this->assertInstanceOf('Liip\ImagineBundle\Binary\SimpleMimeTypeGuesser', $service);
    }
}
