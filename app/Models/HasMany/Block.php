<?php

namespace App\Models\HasMany;

use App\Models\BaseModel;
use App\Models\Blocks\Advantage;
use App\Models\Blocks\BannerSlider;
use App\Models\Blocks\Buttons;
use App\Models\Blocks\CallbackWithMap;
use App\Models\Blocks\ClientLogo;
use App\Models\Blocks\Contact;
use App\Models\Blocks\ContactRubric;
use App\Models\Blocks\ContactsWithMap;
use App\Models\Blocks\Description;
use App\Models\Blocks\Fact;
use App\Models\Blocks\FaqRubric;
use App\Models\Blocks\H2;
use App\Models\Blocks\ModelFact;
use App\Models\Blocks\ModelTab;
use App\Models\Blocks\Picture;
use App\Models\Blocks\Pictures;
use App\Models\Blocks\PricelistRubric;
use App\Models\Blocks\PricelistTitle;
use App\Models\Blocks\QuickLinks;
use App\Models\Blocks\SaleProducts;
use App\Models\Blocks\ShortDescription;
use App\Models\Blocks\Stage;
use App\Models\Blocks\Text;
use App\Models\Blocks\WhyWe;
use App\Models\Blocks\WorkTime;
use App\Models\Category;
use App\Models\Pricelist;
use App\Models\Product;
use App\Models\Review;
use App\Models\Brand;
use App\Models\Blocks\Staff;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Block extends BaseModel
{
    protected $table = 'blocks';

    protected $fillable = [];

    protected $guarded = [];
    protected $with = ['h2'];

    public $timestamps = false;

    public function h2(): HasOne
    {
        return $this->hasOne(H2::class);
    }

    public function description(): HasOne
    {
        return $this->hasOne(Description::class);
    }

    public function short_description(): HasOne
    {
        return $this->hasOne(ShortDescription::class);
    }

    public function pictures(): HasOne
    {
        return $this->hasOne(Pictures::class);
    }

    public function picture(): HasOne
    {
        return $this->hasOne(Picture::class);
    }

    public function contacts(): HasMany
    {
        return $this->hasMany(Contact::class)->orderPriority();
    }

    public function callbackWithMap(): HasOne
    {
        return $this->hasOne(CallbackWithMap::class);
    }

    public function pricelistTitle(): hasMany
    {
        return $this->hasMany(PricelistTitle::class)->orderPriority();
    }

    public function pricelistRubrics(): HasMany
    {
        return $this->hasMany(PricelistRubric::class)->orderPriority();
    }

    public function pricelist(): HasMany
    {
        return $this->hasMany(Pricelist::class)->orderPriority();
    }

    public function worktime(): HasMany
    {
        return $this->hasMany(WorkTime::class)->orderPriority();
    }

    public function contactsWithMap(): HasOne
    {
        return $this->hasOne(ContactsWithMap::class);
    }

    public function faqRubrics(): HasMany
    {
        return $this->hasMany(FaqRubric::class)->orderPriority();
    }

    public function facts(): HasMany
    {
        return $this->hasMany(Fact::class)->orderPriority();
    }

    public function modelFacts(): HasMany
    {
        return $this->hasMany(ModelFact::class)->orderPriority();
    }

    public function modelTabs(): HasMany
    {
        return $this->hasMany(ModelTab::class)->orderPriority();
    }


    public function quickLinks(): HasMany
    {
        return $this->hasMany(QuickLinks::class)->orderPriority();
    }

    public function homeHeader(): HasMany
    {
        return $this->hasMany(QuickLinks::class)->orderPriority();
    }
    //
    public function contactRubric(): HasMany
    {
        return $this->hasMany(ContactRubric::class)->orderPriority();
    }
    //
    public function delivery_list_text(): HasMany
    {
        return $this->hasMany(ContactRubric::class)->orderPriority();
    }
    //

    public function advantages(): HasMany
    {
        return $this->hasMany(Advantage::class)->orderPriority();
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class)->orderPriority();
    }

    public function whyWe(): HasMany
    {
        return $this->hasMany(WhyWe::class)->orderPriority();
    }

    public function staffs(): HasMany
    {
        return $this->hasMany(Staff::class)->orderPriority();
    }

    public function bannersSlider(): HasMany
    {
        return $this->hasMany(BannerSlider::class)->orderPriority();
    }

    public function process(): HasMany
    {
        return $this->hasMany(Text::class)->orderPriority();
    }

    public function stages(): HasMany
    {
        return $this->hasMany(Stage::class)->orderPriority();
    }

    public function logos(): HasMany
    {
        return $this->hasMany(ClientLogo::class)->orderPriority();
    }

    public function hitProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'block_hit_products', 'block_id', 'product_id');
    }

    public function popularProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'block_popular_products', 'block_id', 'product_id');
    }

    public function saleProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'block_sale_products', 'block_id', 'product_code', '', 'code')->withPivot('product_code');
    }

    public function blocksaleproducts(): HasMany
    {
        return $this->hasMany(SaleProducts::class, 'block_id');
    }

    public function favoriteCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'block_popular_categories', 'block_id', 'category_id');
    }

    public function favoriteBrends(): BelongsToMany
    {
        return $this->belongsToMany(Brand::class, 'block_popular_brands', 'block_id', 'brand_id');
    }

    public function popularCategories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'block_popular_categories', 'block_id', 'category_id');
    }

    public function blockButtons(): HasMany
    {
        return $this->hasMany(Buttons::class, 'block_id');
    }

}
