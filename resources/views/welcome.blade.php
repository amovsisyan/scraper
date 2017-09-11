<!DOCTYPE html>
<html lang="en">
    <head>
        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Scraper</title>
        <!-- Icons -->
        <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
        <!-- Styles -->
        <link href="/css/app.css" rel="stylesheet">
        <link href="/css/webroot.css" rel="stylesheet">
        <!-- Scripts -->
        <script src="/js/app.js"></script>
    </head>
    <body>
    <div class="preloader hide"></div>
        <section class="menu menu-right">
            <button type="button" id="runScrapper" class="btn btn-danger margin-20">Run Crawl</button>
            <button type="button" id="queueScrapper" class="btn btn-danger margin-20">Queue Crawl</button>
        </section>
        <section class="table-section">
            <div class="container" id="table-container">
                @include('table-wrapper')
            </div>
        </section>

        <!-- Modal Edit -->
        <div class="modal fade" id="modalEdit" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit post Id: <span class="modal-post-id"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-12">
                                <img src="" alt="">
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input class="form-control form-control-sm modal-edit-text-input" type="text">
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <textarea class="form-control form-control-sm modal-edit-desription-input" rows="3"></textarea>
                                </div>
                            </div>
                            <div class="col-xs-12">
                                <div class="form-group">
                                    <input class="form-control form-control-sm modal-edit-date-input" type="text">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-success confirm-post-edit">Save</button>
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Delete-->
        <div class="modal fade" id="modalDelete" tabindex="-1" role="dialog" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Delete post Id: <span class="modal-post-id"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-xs-3">
                                <img src="" alt="">
                            </div>
                            <div class="col-xs-9">
                                <p id="modalDeletePostTitle"></p>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger confirm-post-delete">Delete</button>
                    </div>
                </div>
            </div>
        </div>
    </body>

    {{--
                IMPORTANT
        All done by vanilla JS to show my JS skills, whole could done by jquery wothout any problem
    --}}
    <script src="/js/helpers.js"></script>
    <script src="/js/scrapper.js"></script>
</html>
