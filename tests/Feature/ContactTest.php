<?php

namespace Tests\Feature;

use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();
    }

    public function test_contact_form_is_available(): void
    {
        $this->get('/contact')
            ->assertOk()
            ->assertSee('Hubungi Saya')
            ->assertSee('Kirim Pesan');
    }

    public function test_valid_contact_message_is_saved(): void
    {
        $this->from('/contact')->post('/contact', [
            'name' => 'Ayu',
            'email' => 'ayu@example.com',
            'subject' => 'Pertanyaan',
            'message' => 'Saya ingin mengetahui informasi lebih lanjut.',
        ])->assertRedirect('/contact')
            ->assertSessionHas('success');

        $this->assertDatabaseHas('contacts', [
            'name' => 'Ayu',
            'email' => 'ayu@example.com',
            'subject' => 'Pertanyaan',
            'message' => 'Saya ingin mengetahui informasi lebih lanjut.',
            'is_read' => false,
        ]);
    }

    public function test_contact_message_is_validated(): void
    {
        $this->from('/contact')->post('/contact', [
            'name' => 'Ayu',
            'email' => 'invalid-email',
            'message' => 'short',
        ])->assertRedirect('/contact')
            ->assertSessionHasErrors(['email', 'message']);

        $this->assertSame(0, Contact::query()->count());
    }

    public function test_contact_form_is_limited_to_three_posts_per_minute(): void
    {
        $payload = [
            'name' => 'Ayu',
            'email' => 'ayu@example.com',
            'message' => 'Saya ingin mengetahui informasi lebih lanjut.',
        ];

        for ($attempt = 0; $attempt < 3; $attempt++) {
            $this->from('/contact')->post('/contact', $payload)->assertRedirect('/contact');
        }

        $this->from('/contact')->post('/contact', $payload)->assertTooManyRequests();
        $this->assertSame(3, Contact::query()->count());
    }
}
