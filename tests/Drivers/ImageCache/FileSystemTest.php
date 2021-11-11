<?php

namespace Tests\Drivers\ImageCache;

use App\Drivers\ImageCache\FileSystem;
use PHPUnit\Framework\TestCase;

/**
 * FileSystemTest
 */
class FileSystemTest extends TestCase
{
    public function tearDown(): void
    {
        (new FileSystem())->clear();
    }

    /** @test */
    public function itSetsAndGetsACacheItem()
    {
        // Arrange
        $fs = new FileSystem();
        $key = 'foo$%^&*/bar' . uniqid(md5((string)time()), true);

        // Act
        $fs->set($key, 'cachedValue');
        $item = $fs->get($key);

        // Assert
        $this->assertEquals('cachedValue', $item);
    }

    /** @test */
    public function itChecksIfTheCacheHasAKey()
    {
        // Arrange
        $fs = new FileSystem();
        $key = 'foo$%^&*/bar' . uniqid(md5((string)time()), true);

        // Act / Assert
        $this->assertFalse($fs->has($key));
        $fs->set($key, 'cachedValue');
        $this->assertTrue($fs->has($key));
    }

    /** @test */
    public function itDeletesACacheEntry()
    {
        // Arrange
        $fs = new FileSystem();
        $key = 'foo$%^&*/bar' . uniqid(md5((string)time()), true);

        // Act / Assert
        $fs->set($key, 'cachedValue');
        $this->assertTrue($fs->has($key));
        $fs->delete($key);
        $this->assertFalse($fs->has($key));
    }

    /** @test */
    public function itClearsTheWholeCache()
    {
        // Arrange
        $fs = new FileSystem();
        $key1 = 'foo$%^&*/one' . uniqid(md5((string)time()), true);
        $key2 = 'foo$%^&*/two' . uniqid(md5((string)time()), true);
        $key3 = 'foo$%^&*/three' . uniqid(md5((string)time()), true);
        $fs->set($key1, 'cachedValue1');
        $fs->set($key2, 'cachedValue2');
        $fs->set($key3, 'cachedValue3');

        // Act
        $fs->clear();

        // Assert
        $this->assertFalse($fs->has($key1));
        $this->assertFalse($fs->has($key2));
        $this->assertFalse($fs->has($key3));
    }

    /** @test */
    public function itSetsAndGetsAnItemWithATag()
    {
        // Arrange
        $fs = new FileSystem();
        $key = 'foo$%^&*/bar' . uniqid(md5((string)time()), true);
        $tag = uniqid('key', true);

        // Act
        $fs->set($key, 'cachedValue', $tag);
        $item = $fs->get($key, $tag);

        // Assert
        $this->assertEquals('cachedValue', $item);
        $this->assertTrue($fs->has($key, $tag));
    }

    /** @test */
    public function itClearsMultipleKeysByTag()
    {
        // Arrange
        $fs = new FileSystem();
        $key1 = 'foo$%^&*/bar' . uniqid(md5((string)time()), true);
        $key2 = 'some>thing' . uniqid(md5((string)time()), true);
        $tag = uniqid('tag-', true);
        $fs->set($key1, 'cachedValue1', $tag);
        $fs->set($key2, 'cachedValue2', $tag);

        // Act
        $item = $fs->deleteTag($tag);

        // Assert
        $this->assertTrue($item);
        $this->assertFalse($fs->has($key1, $tag));
        $this->assertFalse($fs->has($key2, $tag));
    }
}
