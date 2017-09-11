<?php

namespace App\Http\Controllers\Crawl;

use App\Jobs\ScraperQueue;
use App\Result;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Http\Request;
use Goutte\Client;
use App\Http\Controllers\Controller;

class CrawlController extends Controller
{
    /*
     *                  GENERAL
     *
     * By Just Running Crawler we could Receive as much as we want, but we should send many requests,
     * also there is problem with image empty (it could handler <= 200 image deleteing per request)
     *
     * Queue works perfectly, in this two types better one is queue
     *
     */

    public $update = [];

    // todo make const
    protected static $quantity = 100;
    protected static $perCrawlLimit = 5;

    protected static $crlDomain = 'http://www.tert.am/';
    protected static $crlLocal = 'am/';
    protected static $crlAdditional = 'news/';

    protected static function getCrlUri()
    {
        return self::$crlDomain . self::$crlLocal . self::$crlAdditional;
    }

    public function index(Request $request)
    {
        $client = new Client();
        try {
            $offset = ($request->num - 1) * CrawlSettings::PERCRAWLLIMIT + 1;
            $requestsTill = $offset + CrawlSettings::PERCRAWLLIMIT;
            for ($page = $offset; $page < $requestsTill; $page++) {
                $this->update = [];
                $crawler = $client->request('GET', self::getCrlUri().$page);
                $crawler->filter('.news-blocks')->each(function ($node) {
                    // Date
                    $timestamp = $this->_generateCralwDate($node->filter('.nl-dates')->html());

                    // Image
                    $filename = $this->_generateImageAndGrab($node->filter('img')->attr('src'));

                    // InputArray
                    $this->update[] = [
                        'title' => trim($node->filter('h4')->text()),
                        'description' => trim($node->filter('.nl-anot')->text()),
                        'main_image' => $filename,
                        'date_upload' => date("Y-m-d H:i:s", $timestamp),
                        'url' => trim($node->filter('a')->attr('href')),
                    ];

                });
                if (!empty($this->update)) {
                    Result::insert(
                        $this->update
                    );
                }
            }
        } catch (\Exception $e) {
            return response(
                [
                    'error' => true,
                    'response' => $e->getMessage()
                ], 404
            );
        }
        return response(
            [
                'error' => false,
                'num' => $request->num,
            ]
        );
    }

    public function emptyForFirst()
    {
        // think its faster to delete whole, after insert by batches than use updateOrCreate for each row
        // if NOT, what to take the basic, URL ? What if tert.am edited one of his post's title || description || image ...
        // we must go and find this post in 1000 delete, to make sure it was updated by crawl ?
        // if there be search system, possible the crawl system may be overwritten
        Result::truncate();
        $dirs = [public_path(), 'img', 'scrapper_images'];
        File::deleteDirectory(implode(DIRECTORY_SEPARATOR, $dirs));

        return $this->countNeededRequests();
    }

    protected function countNeededRequests()
    {
        $client = new Client();
        $crawler = $client->request('GET', self::getCrlURi());
        $perpage = count($crawler->filter('.news-blocks'));
        $neededRequests = CrawlSettings::QUANTITY/$perpage/CrawlSettings::PERCRAWLLIMIT;

        return $neededRequests;
    }

    public function makeQueue()
    {
        $this->dispatch(new ScraperQueue($this->countNeededRequests(), self::getCrlUri()));
    }

    public function _generateCralwDate($html)
    {
        $dirtyDate = $html;
        $dirtyDateArr = explode('•', $dirtyDate);
        return strtotime(trim($dirtyDateArr[0]));
    }

    public function _generateImageAndGrab($src)
    {
        $file = file_get_contents($src);
        $srcArr = explode('/', $src);
        $srcArrLen = count($srcArr);
        // todo in real app I think $srcArr[$srcArrLen-3], $srcArr[$srcArrLen-2] must be category and subcategory for post
        $neededSrcs = [
            trim($srcArr[$srcArrLen-3]), trim($srcArr[$srcArrLen-2]), trim($srcArr[$srcArrLen-1])
        ];
        $filename = implode(DIRECTORY_SEPARATOR, $neededSrcs);
        Storage::disk('scrapper_images')->put($filename, $file);

        return $filename;
    }
}
