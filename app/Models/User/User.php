<?php

namespace App\Models\User;

use AIGenerate\Models\Stock\StockGenerate;
use AIGenerate\Models\User\Contracts\UserContract;
use AIGenerate\Services\Mails\Contracts\UserMailable;
use AIGenerate\Services\Mails\Model\Contracts\Receiver;
use App\Services\Point\Contracts\HasPoint;
use App\Services\Point\Contracts\HasPointHistory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Passport\HasApiTokens;
use Spatie\Sitemap\Contracts\Sitemapable;
use Spatie\Sitemap\Tags\Url;

class User extends \AIGenerate\Models\User\User implements HasPointHistory, HasPoint, UserContract, Sitemapable, Receiver, UserMailable
{
    protected $connection = 'api';

    protected $table = 'ai_generate_admin.users';

    use HasApiTokens;

    public function information(): HasOne
    {
        return $this->hasOne(UserInformation::class);
    }

    public function toSitemapTag(): Url|string|array
    {
        return Url::create(config('constant.sitemap') . "/user/profile/{$this->getKey()}");
    }

    public function getWith(): array
    {
        return $this->with;
    }

    public function stockGenerates(): HasMany
    {
        return $this->hasMany(StockGenerate::class);
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getMyPageLink(): string
    {
        return '';
    }
}
