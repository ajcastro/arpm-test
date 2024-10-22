<?php

namespace App\Models;

use App\Models\CartItem;
use App\Models\Customer;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * @property integer $id
 * @property string $status
 * @property Customer $customer
 * @property Carbon $completed_at
 * @property Collection<int, CartItem> $items
 */
class Order extends Model
{
    use HasFactory;

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function getTotalAmount(): float
    {
        return $this->items->reduce(
            fn (CartItem $item, $sum) => $sum + $item->price * $item->quantity,
            0
        );
    }

    public function getItemsCount(): int
    {
        return $this->items->count();
    }

    public function getLastAddedToCart(): ?Carbon
    {
        return $this->items->sortByDesc('created_at')->first()->created_at ?? null;
    }

    public function completedOrderExists(): bool
    {
        return $this->status === 'completed';
    }
}
