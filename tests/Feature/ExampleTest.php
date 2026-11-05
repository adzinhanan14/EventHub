<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Event;
use App\Models\Organization;
use App\Models\TicketTier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_homepage_shows_event_discovery_content(): void
    {
        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Temukan acara kampus yang ingin kamu datangi.')
            ->assertSee('Belum ada event mendatang');
    }

    public function test_the_homepage_filters_events_and_shows_the_active_ticket_price(): void
    {
        $category = Category::create(['name' => 'Workshop', 'slug' => 'workshop']);
        $organization = Organization::create([
            'name' => 'Panitia Kampus',
            'slug' => 'panitia-kampus',
            'status' => 'approved',
        ]);
        $event = Event::create([
            'organization_id' => $organization->id,
            'category_id' => $category->id,
            'title' => 'Lokakarya Desain',
            'date' => now()->addDay(),
            'location' => 'Yogyakarta',
            'price' => 100000,
            'stock' => 10,
        ]);

        TicketTier::create([
            'event_id' => $event->id,
            'name' => 'Early Bird',
            'price' => 75000,
            'quota' => 10,
            'sold' => 0,
            'starts_at' => now()->subHour(),
            'ends_at' => now()->addWeek(),
        ]);

        Event::create([
            'organization_id' => $organization->id,
            'category_id' => $category->id,
            'title' => 'Acara yang sudah lewat',
            'date' => now()->subDay(),
            'location' => 'Yogyakarta',
            'price' => 50000,
            'stock' => 10,
        ]);

        $this->get(route('home', ['category' => $category->slug]))
            ->assertOk()
            ->assertSee('Lokakarya Desain')
            ->assertSee('Panitia Kampus')
            ->assertSee('Rp75.000')
            ->assertSee(route('events.show', $event->id))
            ->assertDontSee('Acara yang sudah lewat');
    }

    public function test_uploaded_event_poster_replaces_temporary_homepage_photos(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('posters/workshop.png', 'poster');

        $category = Category::create(['name' => 'Workshop', 'slug' => 'workshop']);
        $organization = Organization::create([
            'name' => 'Panitia Kampus',
            'slug' => 'panitia-kampus',
            'status' => 'approved',
        ]);
        Event::create([
            'organization_id' => $organization->id,
            'category_id' => $category->id,
            'title' => 'Workshop Kampus',
            'date' => now()->addDay(),
            'location' => 'Yogyakarta',
            'price' => 0,
            'stock' => 10,
            'poster_path' => 'posters/workshop.png',
        ]);

        $this->get('/')
            ->assertOk()
            ->assertSee(asset('storage/posters/workshop.png'))
            ->assertDontSee(asset('assets/landing/event-audience.jpg'));
    }

    public function test_the_homepage_limits_events_to_five(): void
    {
        $category = Category::create(['name' => 'Workshop', 'slug' => 'workshop']);
        $organization = Organization::create([
            'name' => 'Panitia Kampus',
            'slug' => 'panitia-kampus',
            'status' => 'approved',
        ]);

        foreach (range(1, 6) as $number) {
            Event::create([
                'organization_id' => $organization->id,
                'category_id' => $category->id,
                'title' => 'Event mendatang ' . $number,
                'date' => now()->addDays($number),
                'location' => 'Yogyakarta',
                'price' => 0,
                'stock' => 10,
            ]);
        }

        $response = $this->get('/');

        $response->assertOk()
            ->assertSee('Event mendatang 1')
            ->assertSee('Event mendatang 5')
            ->assertDontSee('Event mendatang 6');
    }

    public function test_the_homepage_shows_up_to_five_past_events_as_an_archive(): void
    {
        $category = Category::create(['name' => 'Workshop', 'slug' => 'workshop']);
        $organization = Organization::create([
            'name' => 'Panitia Kampus',
            'slug' => 'panitia-kampus',
            'status' => 'approved',
        ]);

        foreach (range(1, 6) as $number) {
            Event::create([
                'organization_id' => $organization->id,
                'category_id' => $category->id,
                'title' => 'Event arsip ' . $number,
                'date' => now()->subDays($number),
                'location' => 'Yogyakarta',
                'price' => 0,
                'stock' => 10,
            ]);
        }

        $this->get('/')
            ->assertOk()
            ->assertSee('Arsip acara')
            ->assertSee('Event arsip 1')
            ->assertSee('Event arsip 5')
            ->assertSee('Event selesai')
            ->assertDontSee('Event arsip 6');
    }
}
