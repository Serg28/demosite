<?php

namespace App\Models;

use App\Models\MorphOne\Rating;
use App\Models\MorphOne\Seo;
use App\Traits\PrepareModelFields;
use App\Traits\SeoCustomUrlTrait;
use App\Traits\SeoTrait;
use App\Traits\Sluggable;
use App\Traits\SlugUrlFieldTrait;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Str;

class BaseModel extends Model
{
    use \Vis\Builder\Helpers\Traits\TranslateTrait;
    use SeoTrait;
    use \Vis\Builder\Helpers\Traits\ImagesTrait;
    use \Vis\Builder\Helpers\Traits\QuickEditTrait;
    use \Venturecraft\Revisionable\RevisionableTrait;
    use \Vis\Builder\Helpers\Traits\Rememberable;
    use \Bkwld\Cloner\Cloneable;
    use HasFactory;
    use SeoCustomUrlTrait;
    use SlugUrlFieldTrait;
    use PrepareModelFields;
    use Sluggable;

    protected $fillable = [];

    protected $guarded = [];

    protected $revisionFormattedFieldNames = [
        'title' => 'Название',
        'description' => 'Описание',
        'is_active' => 'Активация',
        'picture' => 'Изображение',
        'short_description' => 'Короткий текст',
        'created_at' => 'Дата создания',
    ];

    protected $revisionFormattedFields = [
        '1' => 'string:<strong>%s</strong>',
        'public' => 'boolean:No|Yes',
        'deleted_at' => 'isEmpty:Active|Deleted',
    ];

    protected $revisionCleanup = true;

    protected $revisionCreationsEnabled = true;

    protected $revisionEnabled = true;

    protected $historyLimit = 500;

    public function seo(): MorphOne
    {
        return $this->morphOne(Seo::class, 'seo');
    }

    public function rating(): MorphMany
    {
        return $this->morphMany(Rating::class, 'ratingspage');
    }

    public function getFillable()
    {
        return $this->fillable;
    }

    public function setFillable(array $params): void
    {
        $this->fillable = $params;
    }

    /*public function getSlug(): string
    {
        $arrayTitle = json_decode($this->title, true);
        $title = $arrayTitle[defaultLanguage()] ?? '';

        return Str::slug(strip_tags($title));
    }*/

    public function getUrl(): string
    {
        return geturl($this->getUri());
    }

    public function getUri(): string
    {
        return '/news/' . $this->getSlug() . '-' . $this->id;
    }

    public function getDate(): string
    {
        $date = strtotime($this->created_at);

        return date('d', $date) . ' ' . $this->getMonth($this->created_at) . ' ' . date('Y', $date);
    }

    public function getMonth($date)
    {
        $month = [
            '1' => 'Января',
            '2' => 'Февраля',
            '3' => 'Марта',
            '4' => 'Апреля',
            '5' => 'Мая',
            '6' => 'Июня',
            '7' => 'Июля',
            '8' => 'Августа',
            '9' => 'Сентября',
            '10' => 'Октября',
            '11' => 'Ноября',
            '12' => 'Декабря',
        ];

        return __($month[date('n', strtotime($date))]);
    }

    /*
     * filter active page
     * @return object query
     */
    public static function scopeActive($query)
    {
        if (\request()->has('show')) {
            return $query;
        }

        return $query->where('is_active', '1');
    }

    /*
     * order query priority asc
     * @return object query
     */
    public static function scopeOrderPriority($query)
    {
        return $query->orderBy('priority', 'asc');
    }

    /*
     * check correct url and return info about page
     * @param object $query
     * @param string $slug this slug page
     * @param integer $id this id page
     * @return object
     */
    public function scopePageInfo($query, $slug, $id)
    {
        $page = $query->where('id', $id)->active()->first();

        if (!isset($page->id) || $page->getSlug() != $slug) {
            App::abort(404);
        }

        return $page;
    }

    /*
     * get node this page
     * @return object Tree
     */
    public function getNode()
    {
        $segments = $segments = explode('/', Request::path());
        $nodeSlug = '';

        //search last segment not url page
        foreach ($segments as $segment) {
            if ($segment != $this->getSlug() . '-' . $this->id) {
                $nodeSlug = $segment;
            }
        }

        if (!$nodeSlug) {
            return false;
        } else {
            return Tree::where('slug', 'like', $nodeSlug)->first();
        }
    }

    public function humanDate(string $field = 'created_at'): string
    {
        $date = $this->{$field};

        if (!($date instanceof Carbon)) {
            return $date;
        }

        return $date->isoFormat('D MMMM GGGG');
    }

}
