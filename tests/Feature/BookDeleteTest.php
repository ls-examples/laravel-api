<?php

namespace Tests\Feature;

use App\Book;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Routing\Middleware\ThrottleRequests;
use Tests\TestCase;

class BookDeleteTest extends TestCase
{

    use DatabaseMigrations;

    /**
     * @param int $bookId
     * @return \Illuminate\Foundation\Testing\TestResponse
     */
    private function sendRequest(int $bookId)
    {
        return $this
            ->withoutMiddleware(ThrottleRequests::class)
            ->json('POST', "/api/book/{$bookId}/delete");
    }

    public function testDelete()
    {
        $existBook = factory(Book::class)->create();
        $response = $this->sendRequest($existBook->id);

        $response->assertSuccessful();

        $this->assertEquals("", $response->content());
        $this->assertDatabaseMissing('books', [
            'id' => $existBook->id,
        ]);
    }

    public function testDeleteIfNotFound()
    {
        $response = $this->sendRequest(1);
        $response->assertNotFound();
    }

}
