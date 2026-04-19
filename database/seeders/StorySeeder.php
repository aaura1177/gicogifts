<?php

namespace Database\Seeders;

use App\Models\Story;
use Illuminate\Database\Seeder;

class StorySeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'slug' => 'last-pichwai-painters-nathdwara',
                'title' => 'The Last Pichwai Painters of Nathdwara',
                'excerpt' => 'How a temple town keeps centuries of pigment and devotion alive on cloth.',
                'body' => $this->markdownBody(
                    'Nathdwara is not a place you rush through. Between aarti bells and monsoon clouds, pichwai painters still grind lapis and malachite the slow way — on stone, with water, until the pigment sings against cotton. We built the Mewar Heritage Box around that patience: a print you can frame, brass you can light, and chai that buys the afternoon time to actually look.'
                ),
                'cover_image' => 'https://picsum.photos/seed/pichwai-story/1200/630',
            ],
            [
                'slug' => 'sanganer-water-block-prints',
                'title' => 'Why Sanganer\'s Water Makes Block Prints Sing',
                'excerpt' => 'The chemistry of river minerals, mordant cotton, and carved pearwood.',
                'body' => $this->markdownBody(
                    'Jaipur gets the postcards, but Sanganer gets the rinse. Printers here swear the local water "bites" the dye just right — enough iron to fix indigo, enough softness to keep the block from skipping. The Jaipur Colour Box is our love letter to that alchemy: scarf, coasters, dry fruit, and a thread of saffron like a high note.'
                ),
                'cover_image' => 'https://picsum.photos/seed/sanganer-story/1200/630',
            ],
            [
                'slug' => 'banswara-tribal-weaver-home',
                'title' => 'Inside a Banswara Tribal Weaver\'s Home',
                'excerpt' => 'Looms in courtyards, jewellery in tin boxes, and tea that tastes like smoke and monsoon.',
                'body' => $this->markdownBody(
                    'The Tribal Discovery Box exists because we kept getting asked for "something real." Here, real means a weaver who still cards his own blend, a jeweller who files by eye, and terracotta that carries the thumbprint of the person who pressed it. If luxury is attention, this is luxury without the velvet rope.'
                ),
                'cover_image' => 'https://picsum.photos/seed/banswara-story/1200/630',
            ],
        ];

        foreach ($rows as $row) {
            Story::query()->updateOrCreate(
                ['slug' => $row['slug']],
                [
                    'title' => $row['title'],
                    'excerpt' => $row['excerpt'],
                    'body' => $row['body'],
                    'cover_image' => $row['cover_image'],
                    'meta_title' => $row['title'].' | GicoGifts Stories',
                    'meta_description' => $row['excerpt'],
                    'published_at' => now(),
                    'is_published' => true,
                ]
            );
        }
    }

    private function markdownBody(string $lead): string
    {
        $suffix = <<<'MD'


### At the bench

We visit every maker we name. If a colour batch shifts, we update the site - no stock photos of fantasy Rajasthan.

### Bring it home

Pair this story with a box from the same region, or send it to someone who reads the liner notes.
MD;

        return $lead.$suffix;
    }
}
