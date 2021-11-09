<?php

namespace Tests;

use App\Sanitize;
use PHPUnit\Framework\TestCase;

/**
 * SanitizeTest
 *
 * For more test strings, see:
 *
 * @link https://github.com/rwbaugh/big_list_of_nasty_strings/blob/master/blns.py
 */
class SanitizeTest extends TestCase
{
    /** @test */
    public function itStripsOutTagsAndEncodesHtmlCharacters()
    {
        // Arrange
        $string = "<this/>X<tag?>'\"";

        // Act
        $sanitizedString = Sanitize::string($string);

        // Assert
        $this->assertEquals("X&#39;&#34;", $sanitizedString);
    }

    /** @test */
    public function itStripsOutInvalidCharactersFromAnUrl()
    {
        // Arrange
        $url = "ﾟ･✿ヾ╲(｡◕‿◕｡)╱✿･ﾟ";

        // Act
        $sanitizedUrl = Sanitize::url($url);

        // Assert
        $this->assertEquals('()', $sanitizedUrl);
    }
}
