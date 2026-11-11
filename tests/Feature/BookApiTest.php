<?php

namespace Tests\Feature;

use App\Models\Book;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BookApiTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_lists_books_newest_first(): void
    {
        $older = Book::factory()->create(['title' => 'Older book']);
        $newer = Book::factory()->create(['title' => 'Newer book']);

        $this->getJson('/api/books')
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonPath('data.0.id', $newer->id)
            ->assertJsonPath('data.1.id', $older->id)
            ->assertJsonStructure([
                'data' => [['id', 'title', 'created_at']],
                'links',
                'meta' => ['current_page', 'per_page', 'total'],
            ]);
    }

    #[Test]
    public function it_paginates_the_book_list(): void
    {
        Book::factory()->count(20)->create();

        $this->getJson('/api/books?page=2')
            ->assertOk()
            ->assertJsonCount(5, 'data')
            ->assertJsonPath('meta.total', 20);
    }

    #[Test]
    public function it_creates_a_book(): void
    {
        $this->postJson('/api/books', ['title' => 'Clean Code'])
            ->assertCreated()
            ->assertJsonPath('data.title', 'Clean Code');

        $this->assertDatabaseHas('books', ['title' => 'Clean Code']);
    }

    #[Test]
    public function it_requires_a_title(): void
    {
        $this->postJson('/api/books', [])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);

        $this->assertDatabaseCount('books', 0);
    }

    #[Test]
    public function it_rejects_a_title_longer_than_255_characters(): void
    {
        $this->postJson('/api/books', ['title' => str_repeat('a', 256)])
            ->assertUnprocessable()
            ->assertJsonValidationErrors(['title']);
    }

    #[Test]
    public function it_ignores_fields_that_are_not_allowed(): void
    {
        $this->postJson('/api/books', ['title' => 'Refactoring', 'id' => 999])
            ->assertCreated();

        $this->assertDatabaseMissing('books', ['id' => 999]);
    }

    #[Test]
    public function it_deletes_a_book(): void
    {
        $book = Book::factory()->create();

        $this->deleteJson("/api/books/{$book->id}")
            ->assertNoContent();

        $this->assertModelMissing($book);
    }

    #[Test]
    public function it_returns_404_when_deleting_a_missing_book(): void
    {
        $this->deleteJson('/api/books/12345')
            ->assertNotFound();
    }
}
