<?php
/**
 * This file is part of CircleRestClientBundle.
 *
 * CircleRestClientBundle is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * CircleRestClientBundle is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with CircleRestClientBundle.  If not, see <http://www.gnu.org/licenses/>.
 */

namespace Circle\RestClientBundle\Tests\Functional\Services;

use Circle\RestClientBundle\Services\ResponseHeaders;

/**
 * Tests the response headers class
 *
 * @author    Timo Linde <info@timo-linde.de>
 * @copyright 2016 TeeAge-Beatz UG
 *
 * @coversDefaultClass Circle\RestClientBundle\Services\ResponseHeaders
 *
 * @SuppressWarnings("PHPMD.StaticAccess")
 */
class ResponseHeadersTest extends \PHPUnit_Framework_TestCase {    
    
    /**
     * @test
     * @covers ::httpParseHeadersPeclReplacement
     * @covers ::<private>
     */
    public function testHttpParseHeadersPeclReplacement() {
        $headers = "HTTP/1.1 302 Found\r\n".
            "Date: Tue, 21 Apr 2015 13:57:48 GMT\r\n".
            "Server: Apache/2.2.22 (Debian)\r\n".
            "Location: https://test.de\r\n".
            "Vary: Accept-Encoding\r\n".
            "Content-Length: 284\r\n".
            "Content-Type: text/html; charset=iso-8859-1\r\n";
        
        $parsedHeader = ResponseHeaders::httpParseHeadersPeclReplacement($headers);
        $this->assertArrayHasKey('Date', $parsedHeader);
        $this->assertArrayHasKey('Server', $parsedHeader);
        $this->assertArrayHasKey('Location', $parsedHeader);
        $this->assertArrayHasKey('Vary', $parsedHeader);
        $this->assertArrayHasKey('Content-Length', $parsedHeader);
        $this->assertArrayHasKey('Content-Type', $parsedHeader);
        $this->assertEquals("Tue, 21 Apr 2015 13:57:48 GMT", $parsedHeader["Date"]);
        $this->assertEquals("Apache/2.2.22 (Debian)", $parsedHeader["Server"]);
        $this->assertEquals("https://test.de", $parsedHeader["Location"]);
        $this->assertEquals("Accept-Encoding", $parsedHeader["Vary"]);
        $this->assertEquals("284", $parsedHeader["Content-Length"]);
        $this->assertEquals("text/html; charset=iso-8859-1", $parsedHeader["Content-Type"]);
        
        $headers = "content-type: text/html; charset=UTF-8\r\n".
            "Server: Funky/1.0\r\n".
            "Set-Cookie: foo=bar\r\n".
            "Set-Cookie: baz=quux\r\n".
            "Folded: works\r\n\ttoo\r\n";
        $returnValue = ResponseHeaders::httpParseHeadersPeclReplacement($headers);
        $this->assertTrue(is_array($returnValue));
        
        $this->assertTrue(isset($returnValue['Content-Type']));
        $this->assertEquals($returnValue['Content-Type'], "text/html; charset=UTF-8");
        
        $this->assertTrue(isset($returnValue['Server']));
        $this->assertEquals($returnValue['Server'], "Funky/1.0");
        
        $this->assertTrue(isset($returnValue['Set-Cookie']));
        $this->assertTrue(is_array($returnValue['Set-Cookie']));
        $this->assertEquals(count($returnValue['Set-Cookie']), 2);
        
        $this->assertTrue(isset($returnValue['Folded']));
        $this->assertEquals($returnValue['Folded'], "works too");
    }
}