<?php namespace Vis\ImageStorage;

class Tag extends AbstractImageStorage
{
    protected $table = 'vis_tags';
    protected $configPrefix = 'tag';

    public function images()
    {
        return $this->morphedByMany('Vis\ImageStorage\Image', 'entity', 'vis_tags2entities', 'id_tag', 'id_entity');
    }

    public function documents()
    {
        return $this->morphedByMany('Vis\ImageStorage\Document', 'entity', 'vis_tags2entities', 'id_tag', 'id_entity');
    }

    public function videos()
    {
        return $this->morphedByMany('Vis\ImageStorage\Video', 'entity', 'vis_tags2entities', 'id_tag', 'id_entity');
    }

    public function galleries()
    {
        return $this->morphedByMany('Vis\ImageStorage\Gallery', 'entity', 'vis_tags2entities', 'id_tag', 'id_entity');
    }

    public function videoGalleries()
    {
        return $this->morphedByMany('Vis\ImageStorage\VideoGallery', 'entity', 'vis_tags2entities', 'id_tag', 'id_entity');
    }

    public function relateToTag($id, $type)
    {
        $this->$type()->syncWithoutDetaching($id);
        $this->flushCacheRelation($this->$type()->getRelated());
    }
}
