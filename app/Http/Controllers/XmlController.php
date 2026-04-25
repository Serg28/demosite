<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Str;

set_time_limit(600);
ini_set('max_execution_time', '600');

class XmlController extends Controller
{
    private string $path = 'App\\Services\\Xml\\';

    private string $file_path = '/xml-feed/';

    public function index(string $nameField, int $id): Response
    {
        $feed = Feed::where('feed_name', $nameField)->where('id', $id)->active()->firstOrfail();

        $classXml = $this->path . ucfirst(Str::camel($feed->feed_name));

        if (!class_exists($classXml)) {
            abort(404);
        }

        return (new $classXml())->render($feed);
    }

    public function getXmlFile($feed_file)
    {
        //$fileXml = public_path().$this->file_path.ucfirst(Str::camel($feed_file));

        $fileXml = public_path() . $this->file_path . Lang::locale() . '-' . $feed_file . '.xml'; //подставляем язык из locale (url)

        if (!file_exists($fileXml)) {
            abort(404);
        }

        //return File::get($fileXml);
        return response()->file($fileXml, [
            'Content-Type' => 'application/xml'
        ]);
    }
}
