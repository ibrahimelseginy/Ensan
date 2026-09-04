<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

final class MobileNotification extends Model
{
    use \App\Traits\UploadsImages;

    public const CATEGORY_GENERAL = 'عام';
    public const CATEGORY_CAMPAIGN = 'حملات';
    public const CATEGORY_DONATION = 'تبرعات';
    public const CATEGORY_URGENT = 'عاجل';

    protected $fillable = [
        'title',
        'body',
        'image_path',
        'category',
        'target_audience',
        'is_sent',
        'sent_at'
    ];

    public static function getCategories(): array
    {
        return [
            ['id' => self::CATEGORY_GENERAL, 'label' => 'عام', 'icon' => 'info-circle'],
            ['id' => self::CATEGORY_CAMPAIGN, 'label' => 'حملات', 'icon' => 'megaphone'],
            ['id' => self::CATEGORY_DONATION, 'label' => 'تبرعات', 'icon' => 'heart'],
            ['id' => self::CATEGORY_URGENT, 'label' => 'عاجل', 'icon' => 'exclamation-triangle'],
        ];
    }

    protected $appends = ['image_url'];
}
