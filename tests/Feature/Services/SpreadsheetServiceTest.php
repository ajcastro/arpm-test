<?php

namespace Tests\Feature\Services;

use App\Jobs\ProcessProductImage;
use App\Services\SpreadsheetService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Queue;
use Mockery\MockInterface;
use Tests\TestCase;


class SpreadsheetServiceTest extends TestCase
{
    use RefreshDatabase;

    public function testProcessSpreadsheetShouldBeSuccessful(): void
    {
        Queue::fake();

        $filepath = 'some-file-path';

        $products = [
            [
                'product_code' => 'AXE',
                'quantity' => 3,
            ],
            [
                'product_code' => 'USB',
                'quantity' => 5,
            ]
        ];

        $this->mock('importer', function (MockInterface $mock) use ($filepath, $products) {
            $mock->shouldReceive('import')->with($filepath)
                ->andReturn($products);
        });

        $spreadsheetService = new SpreadsheetService();

        $spreadsheetService->processSpreadsheet($filepath);

        foreach ($products as $product) {
            $this->assertDatabaseHas('products', [
                'code' => $product['product_code'],
                'quantity' => $product['quantity'],
            ]);
        }

        Queue::assertPushed(ProcessProductImage::class);
    }
}
