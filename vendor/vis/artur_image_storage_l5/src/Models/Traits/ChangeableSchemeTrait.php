<?php namespace Vis\ImageStorage;

use Illuminate\Support\Facades\Schema;
use \Illuminate\Database\Schema\Blueprint;

trait ChangeableSchemeTrait
{
    public function doCheckSchemeFields()
    {
        $fields = $this->getConfigFields();

        foreach ($fields as $field => $fieldInfo) {
            $columnNames = [];

            if (isset($fieldInfo['tabs'])) {
                foreach ($fieldInfo['tabs'] as $tab => $tabInfo) {
                    $columnNames[] = $field . $tabInfo['postfix'];
                }
            } else {
                $columnNames[] = $field;
            }

            foreach ($columnNames as $key => $columnName) {
                if (!Schema::hasColumn($this->table, $columnName)) {

                    $fieldParams = explode("|", $fieldInfo['field']);

                    $fieldType   = $fieldParams[0];
                    $fieldLength = isset($fieldParams[1]) ? $fieldParams[1] : false;

                    Schema::table($this->table, function (Blueprint $table) use ($columnName, $fieldType, $fieldLength) {
                        $field_add = $table->$fieldType($columnName);
                        if ($fieldLength) {
                            $field_add->length($fieldLength);
                        }
                    });
                }
            }
        }
    }
}
