<?php

namespace App\Console\Commands;

use App\Models\AvailabilityOrder;
use App\Models\Category;
use App\Models\CategoryProduct;
use App\Models\Characteristic;
use App\Models\CharacteristicOption;
use App\Models\CharacteristicRelation;
use App\Models\Comment;
use App\Models\DeliverySchedule;
use App\Models\DiscountCard;
use App\Models\Faq;
use App\Models\Feed;
use App\Models\Feedback;
use App\Models\FollowPrice;
use App\Models\Guarantee;
use App\Models\LegalEntitiesRecipient;
use App\Models\MenuBase;
use App\Models\MenuColSection;
use App\Models\MenuFooter;
use App\Models\MenuHeader;
use App\Models\MenuSection;
use App\Models\MenuSeocatalog;
use App\Models\News;
use App\Models\Order;
use App\Models\OrderPayment;
use App\Models\OrderProductOption;
use App\Models\OrderProducts;
use App\Models\OrderUtm;
use App\Models\Pricelist;
use App\Models\Product;
use App\Models\ProductCharacteristicOption;
use App\Models\ProductJoinBlock;
use App\Models\ProductJoinProduct;
use App\Models\ProductPromocode;
use App\Models\ProductPromotion;
use App\Models\PromoCode;
use App\Models\Promotion;
use App\Models\Question;
use App\Models\Review;
use App\Models\SeoGroups;
use App\Models\SeoUrls;
use App\Models\Settlement;
use App\Models\ShopInfo;
use App\Models\SliderMain;
use App\Models\StatisticSearch;
use App\Models\Subscription;
use App\Models\Tag;
use App\Models\Team;
use App\Models\UnfinishedBasket;
use App\Models\UnfinishedBasketsProducts;
use App\Models\User;
use App\Models\Warehouse;
use App\Models\WarehouseDeliverySchedule;
use App\Models\WarehouseDeliveryScheduleInfo;
use App\Models\WarehouseOrderProduct;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Vis\Builder\Revision;

class PrepareNewSite extends Command
{
    protected $signature = 'site:prepare';

    protected $description = 'Prepare a new site';

    public function handle(): void
    {
        $this->info('Clear database');

        DB::statement('SET foreign_key_checks=0');

        Product::truncate();
        Category::where('id', '!=', 1)->delete();
        CategoryProduct::truncate();
        AvailabilityOrder::truncate();
        Characteristic::truncate();
        CharacteristicOption::truncate();
        ProductCharacteristicOption::truncate();
        CharacteristicRelation::truncate();
        StatisticSearch::truncate();
        DB::table('category_characteristic')->delete();
        Feedback::truncate();
        Order::truncate();
        OrderProducts::truncate();
        OrderProductOption::truncate();
        OrderUtm::truncate();
        OrderPayment::truncate();
        Review::truncate();
        Revision::truncate();
        Subscription::truncate();
        User::where('id', '!=', 1)->delete();
        DB::table('vis_images')->delete();
        News::truncate();
        Tag::truncate();
        Faq::truncate();
        Question::truncate();
        PromoCode::truncate();
        DiscountCard::truncate();
        Comment::truncate();
        MenuHeader::truncate();
        MenuFooter::truncate();
        MenuSection::truncate();
        MenuSeocatalog::truncate();
        MenuColSection::truncate();
        MenuBase::truncate();
        Guarantee::truncate();
        DeliverySchedule::truncate();
        Feed::truncate();
        FollowPrice::truncate();
        LegalEntitiesRecipient::truncate();
        Pricelist::truncate();
        ProductJoinBlock::truncate();
        ProductJoinProduct::truncate();
        ProductPromocode::truncate();
        ProductPromotion::truncate();
        Promotion::truncate();
        SeoGroups::truncate();
        SeoUrls::truncate();
        Settlement::truncate();
        Warehouse::truncate();
        WarehouseDeliverySchedule::truncate();
        WarehouseDeliveryScheduleInfo::truncate();
        WarehouseOrderProduct::truncate();
        UnfinishedBasket::truncate();
        UnfinishedBasketsProducts::truncate();
        //Team::truncate();
        SliderMain::truncate();
        //ShopInfo::truncate();

        DB::statement('SET foreign_key_checks=1');

        $this->info('Clear cache');

        Artisan::call('cache:clear');

        $this->info('create index elastic');

        Artisan::call('search:reindex');
    }
}
