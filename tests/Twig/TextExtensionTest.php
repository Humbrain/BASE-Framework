<?php

namespace Tests\Twig;

use Humbrain\Framework\extensions\TextExtensions;
use PHPUnit\Framework\TestCase;

class TextExtensionTest extends TestCase
{

    /**
     * @var TextExtensions
     */
    private $textExtension;

    public function setUp(): void
    {
        $this->textExtension = new TextExtensions();
    }

    public function testExcerptWithShortText()
    {
        $text = "Salut";
        $this->assertEquals($text, $this->textExtension->excerpt($text, 10));
    }

    public function testExcerptWithLongText()
    {
        $text = "Salut les gens";
        $this->assertEquals('Salut...', $this->textExtension->excerpt($text, 6));
        $this->assertEquals('Salut les...', $this->textExtension->excerpt($text, 12));
    }
}
