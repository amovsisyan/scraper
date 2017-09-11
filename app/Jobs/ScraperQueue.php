<?php

namespace App\Jobs;

use App\Http\Controllers\Crawl\CrawlController;
use App\Http\Controllers\Crawl\CrawlSettings;
use App\Result;
use Goutte\Client;
use Illuminate\Support\Facades\File;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Storage;

class ScraperQueue implements ShouldQueue
{
    use InteractsWithQueue, Queueable, SerializesModels;

    // todo make const
    public $neededRequests = 0;
    public $getCrlUri;
    public $update = [];
    public $crawlobj;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($neededRequests, $getCrlUri)
    {
        $this->neededRequests = $neededRequests;
        $this->getCrlUri = $getCrlUri;
        $this->crawlobj = new CrawlController;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // todo this part here and part in CrawlController are the same, so it should be arranged
        try {
            $this->crawlobj->emptyForFirst();

            $client = new Client();
            for ($i = 1; $i <= $this->neededRequests; $i++) {
                $offset = ($i - 1) * CrawlSettings::PERCRAWLLIMIT + 1;
                $requestsTill = $offset + CrawlSettings::PERCRAWLLIMIT;
                for ($page = $offset; $page < $requestsTill; $page++) {
                    $this->update = [];
                    $crawler = $client->request('GET', $this->getCrlUri.$page);
                    $crawler->filter('.news-blocks')->each(function ($node) {
                        // Date
                        $timestamp = $this->crawlobj->_generateCralwDate($node->filter('.nl-dates')->html());

                        // Image
                        $filename = $this->crawlobj->_generateImageAndGrab($node->filter('img')->attr('src'));

                        $this->update[] = [
                            'title' => trim($node->filter('h4')->text()),
                            'description' => trim($node->filter('.nl-anot')->text()),
                            'main_image' => $filename,
                            'date_upload' => date("Y-m-d H:i:s", $timestamp),
                            'url' => trim($node->filter('a')->attr('href')),
                        ];

                    });
                    if (!empty($this->update)) {
                        $newPosts = Result::insert(
                            $this->update
                        );
                    }
                }
            }
        } catch(\Exception $e) {
            print_r($e->getMessage());
        }
    }
}
