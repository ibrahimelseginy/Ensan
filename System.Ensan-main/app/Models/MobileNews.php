<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\UploadsImages;
use Illuminate\Support\Facades\Storage;

final class MobileNews extends Model
{
    use UploadsImages;

    public const CATEGORY_GENERAL = 'عام';
    public const CATEGORY_CAMPAIGN = 'حملات';
    public const CATEGORY_DONATION = 'تبرعات';
    public const CATEGORY_URGENT = 'عاجل';

    protected $fillable = ['title', 'content', 'image_path', 'category', 'views', 'shares'];

    public static function getCategories(): array
    {
        return [
            ['id' => self::CATEGORY_GENERAL, 'label' => 'عام', 'icon' => 'info-circle'],
            ['id' => self::CATEGORY_CAMPAIGN, 'label' => 'حملات', 'icon' => 'megaphone'],
            ['id' => self::CATEGORY_DONATION, 'label' => 'تبرعات', 'icon' => 'heart'],
            ['id' => self::CATEGORY_URGENT, 'label' => 'عاجل', 'icon' => 'exclamation-triangle'],
        ];
    }

    public function getImageColumn(): string
    {
        return 'image_path';
    }



    protected $appends = ['image_url'];
}
