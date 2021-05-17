<?php

namespace Tests\Feature;

use App\Jobs\ImportYoutubeChannelStreamsJob;
use App\Models\Stream;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ImportYoutubeChannelStreamsTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_imports_youtube_channel_streams(): void
    {
        Http::fake([
            '*search*' => Http::response($this->upcomingStreamsResponse()),
            '*video*' => Http::response($this->videoResponse()),
        ]);

        // Assert
        $this->assertDatabaseCount((new Stream())->getTable(), 0);

    	// Act
        (new ImportYoutubeChannelStreamsJob('UCdtd5QYBx9MUVXHm7qgEpxA'))->handle();

    	// Assert
        $this->assertDatabaseCount((new Stream())->getTable(), 3);

    }

    /** @test */
    public function it_updates_already_given_streams(): void
    {
        // Arrange
        Http::fake([
            '*search*' => Http::response($this->upcomingStreamsResponse()),
            '*video*' => Http::response($this->videoResponse()),
        ]);
        Stream::factory()->create(['youtube_id' => 'gzqJZQyfkaI']);

        // Act
        (new ImportYoutubeChannelStreamsJob('UCdtd5QYBx9MUVXHm7qgEpxA'))->handle();

        // Assert
        $this->assertDatabaseCount((new Stream())->getTable(), 3);

    }
}
