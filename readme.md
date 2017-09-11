## Powered By A. Movsisyan
### Laravel simple Scraper based on Queue

At first please make sure You know how to install Laravel project.
After please add some database and run migrations.

There are two types of crawling in this simple app.

- The first.

    Yup, The first is just simple query loop from front side. Works as expected, but it has two problems.

   1. it has problem when we empty many results, you should run more than once, as we have 30 seconds on each request, which I didn't want to touch,
   2. second it is query loop which is problem itself, app dies till results will receive.

    To run this just click on "Run Crawler" button.


- The second.

    Done by Laravel queue. Works as expected, haven't detect any problem, may be error handling isn't as better as should be.

    To run this just click on "Queue Crawler" button, after run "php artisan queue:work" by terminal

####It there will be some bugs I am free for solving them.