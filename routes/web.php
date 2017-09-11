<?php
Route::get('/', ['uses'=>'HomeController@index','as'=>'homeIndex']);
Route::post('/edited', ['uses'=>'HomeController@editPost','as'=>'editPost']);
Route::post('/delete', ['uses'=>'HomeController@deletePost','as'=>'deletePost']);
Route::post('/emptyForFirst', ['uses'=>'Crawl\CrawlController@emptyForFirst','as'=>'emptyForFirst']);
Route::post('/crawl', ['uses'=>'Crawl\CrawlController@index','as'=>'crawlIndex']);

Route::post('/queue', ['uses'=>'Crawl\CrawlController@makeQueue','as'=>'makeQueue']);