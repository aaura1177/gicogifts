<?php

namespace Database\Seeders;

use App\Models\Faq;
use Illuminate\Database\Seeder;

class FaqSeeder extends Seeder
{
    public function run(): void
    {
        $rows = [
            [
                'category' => 'shipping',
                'question' => 'How long does delivery take?',
                'answer' => 'We ship within 3–7 business days across most of India once your order is paid and packed. You will receive tracking as soon as the courier picks up.',
                'sort_order' => 1,
            ],
            [
                'category' => 'returns',
                'question' => 'What if I do not love my gift box?',
                'answer' => 'We accept breakage-only returns within 7 days of delivery with photo proof. Food items and perishables are non-returnable once shipped.',
                'sort_order' => 2,
            ],
            [
                'category' => 'gifting',
                'question' => 'Can I add a gift message?',
                'answer' => 'Yes. At checkout you can mark the order as a gift and add a short message. We can also hide prices on the printed invoice on request.',
                'sort_order' => 3,
            ],
            [
                'category' => 'corporate',
                'question' => 'Do you handle corporate or bulk orders?',
                'answer' => 'Yes — visit Corporate Gifting and share your headcount, city, and timeline. We respond with a tailored proposal within 2 business days.',
                'sort_order' => 4,
            ],
            [
                'category' => 'craft',
                'question' => 'Are these products really handmade?',
                'answer' => 'Every box lists artisans and regions we work with. Natural variation is part of the craft — that is the point.',
                'sort_order' => 5,
            ],
        ];

        foreach ($rows as $row) {
            Faq::query()->updateOrCreate(
                ['question' => $row['question']],
                array_merge($row, ['is_active' => true])
            );
        }
    }
}
