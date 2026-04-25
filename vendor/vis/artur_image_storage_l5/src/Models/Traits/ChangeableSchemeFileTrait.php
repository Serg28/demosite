<?php namespace Vis\ImageStorage;

use Illuminate\Support\Facades\Schema;
use \Illuminate\Database\Schema\Blueprint;

trait ChangeableSchemeFileTrait
{
    public function doCheckSchemeSizes()
    {
        $sizes = $this->getConfigSizes();

        foreach ($sizes as $size => $info) {

            $columnName = $this->sizePrefix . $size;

            if (!Schema::hasColumn($this->table, $columnName)) {

                Schema::table($this->table, function (Blueprint $table) use ($columnName) {
                    $table->text($columnName);
                });
            }
        }
    }

    public function doUpdateSizes()
    {
        $sizes = $this->getConfigSizesModifiable();

        foreach ($sizes as $size => $info) {

            $columnName = $this->sizePrefix . $size;

            $entities = $this->where($columnName, '=', '')->get();

            foreach ($entities as $key => $entity) {
                $entity->makeFile($size);
                $entity->save();
            }
        }
    }
}
