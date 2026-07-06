<?php

namespace App\Models;

use App\Enum\PageType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use App\Models\Page;

class PolicyAcceptance extends Model
{
    use HasFactory;

    protected $fillable = [
        'account_type',
        'policy_version',
        'accepted_at',
    ];

    protected $casts = [
        'accepted_at' => 'datetime',
    ];

    public function accountable(): MorphTo
    {
        return $this->morphTo();
    }

    public static function currentVersion(): ?string
    {
        $pages = Page::query()
            ->whereIn('type', [PageType::TERMS->value, PageType::POLICY->value])
            ->valid()
            ->latest('updated_at')
            ->get(['updated_at']);

        if ($pages->isEmpty()) {
            return null;
        }

        return $pages
            ->map(fn ($page) => $page->updated_at?->format('YmdHis'))
            ->filter()
            ->implode('|');
    }

    public function isCurrent(): bool
    {
        $currentVersion = static::currentVersion();

        return $currentVersion === null || $this->policy_version === $currentVersion;
    }
}
