<?php

namespace Tests\Drivers\ImageProcessor;

use App\Drivers\ImageProcessor\Gd2;
use App\Exceptions\ProcessingException;
use App\Exceptions\UnsupportedFormatException;
use PHPUnit\Framework\TestCase;

/**
 * Gd2Test
 */
class Gd2Test extends TestCase
{
    /** @test */
    public function itResizesAnJpegTo300x200Jpeg()
    {
        // Arrange
        $fileName = 'balloons.jpg';

        // Act
        $image = (new Gd2())
            ->load(file_get_contents(ROOT . '/tests/' . $fileName))
            ->resize(300, 200)
            ->render('jpeg');

        // Assert
        $size = getimagesizefromstring($image);
        $this->assertNotEmpty($size);
        $this->assertEquals(300, $size[0]);
        $this->assertEquals(200, $size[1]);
        $this->assertEquals('image/jpeg', $size['mime']);
    }

    /** @test */
    public function itResizesAnJpegTo300x200Png()
    {
        // Arrange
        $fileName = 'balloons.jpg';

        // Act
        $image = (new Gd2())
            ->load(file_get_contents(ROOT . '/tests/' . $fileName))
            ->resize(300, 200)
            ->render('png');

        // Assert
        $size = getimagesizefromstring($image);
        $this->assertNotEmpty($size);
        $this->assertEquals(300, $size[0]);
        $this->assertEquals(200, $size[1]);
        $this->assertEquals('image/png', $size['mime']);
    }

    /** @test */
    public function itResizesAnJpegTo300x200Webp()
    {
        // Arrange
        $fileName = 'balloons.jpg';

        // Act
        $image = (new Gd2())
            ->load(file_get_contents(ROOT . '/tests/' . $fileName))
            ->resize(300, 200)
            ->render('webp');

        // Assert
        $size = getimagesizefromstring($image);
        $this->assertNotEmpty($size);
        $this->assertEquals(300, $size[0]);
        $this->assertEquals(200, $size[1]);
        $this->assertEquals('image/webp', $size['mime']);
    }

    /** @test */
    public function itThrowsAnExceptionIfTryingToResizeWithoutLoading()
    {
        // Arrange
        $gd2 = new Gd2();
        $this->expectException(ProcessingException::class);

        // Act / Assert
        $gd2->resize(300, 200);
    }

    /** @test */
    public function itThrowsAnExceptionIfTryingToRenderWithoutLoading()
    {
        // Arrange
        $gd2 = new Gd2();
        $this->expectException(ProcessingException::class);

        // Act / Assert
        $gd2->render('jpeg');
    }

    /** @test */
    public function itThrowsAnExceptionIfTryingToRenderInvalidFormat()
    {
        // Arrange
        $fileName = 'balloons.jpg';

        $this->expectException(UnsupportedFormatException::class);

        // Act / Assert
        (new Gd2())
            ->load(file_get_contents(ROOT . '/tests/' . $fileName))
            ->resize(300, 200)
            ->render('gif');
    }
}
