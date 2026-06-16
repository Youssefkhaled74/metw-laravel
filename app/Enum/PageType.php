<?php

namespace App\Enum;

enum PageType: string
{
    case TERMS = 'terms';
    case POLICY = 'policy';
    case PRIVACY = 'privacy';
    case ABOUT = 'about';
    case OTHER = 'other';

    public function label(): string
    {
        return __('admin-dashboard.page_types.' . $this->value);
    }
}
