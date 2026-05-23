<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderStatus extends Model
{
    public $timestamps = false;

    protected $fillable = ['title', 'color', 'priority'];

    public function t(string $field): string
    {
        $data = $this->$field;

        if (is_array($data)) {
            return $data[app()->getLocale()] ?? $data['uk'] ?? $data['ru'] ?? reset($data) ?? '';
        }

        if (is_string($data)) {
            $decoded = json_decode($data, true);
            if (is_array($decoded)) {
                return $decoded[app()->getLocale()] ?? $decoded['uk'] ?? $decoded['ru'] ?? reset($decoded) ?? '';
            }

            return $data;
        }

        return (string) $data;
    }
}
