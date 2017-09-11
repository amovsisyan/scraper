<div class="table-wrapper">
    <table class="table table-hover">
        @if (!empty($response))
            @if (!empty($response['header']))
                <thead>
                    <tr>
                        @foreach($response['header'] as $header)
                            <th>{{$header}}</th>
                        @endforeach
                    </tr>
                </thead>
            @endif
            @if (!empty($response['header']))
                <tbody>
                    @foreach($response['data'] as $item)
                        <tr class="tr-row" id="post-{{$item->id}}">
                            <th class="item-id">{{$item->id}}</th>
                            <th class="item-title"><a href="{{$item->url}}" class="item-href">{{$item->title}}</a></th>
                            <th class="item-description">{{$item->description}}</th>
                            <th class="item-main-image"><img src="\img\scrapper_images\{{$item->main_image}}" class="item-main-image-src" alt=""></th>
                            <th class="item-date">{{$item->date_upload}}</th>
                            <th><button type="button" class="btn btn-primary btn-sm modal-edit-btn" data-toggle="modal" data-target="#modalEdit"><i class="material-icons">edit</i></button></th>
                            <th><button type="button" class="btn btn-danger btn-sm modal-delete-btn" data-toggle="modal" data-target="#modalDelete"><i class="material-icons">delete</i></button></th>
                        </tr>
                    @endforeach
                </tbody>
            @endif
        @endif
    </table>
    <div class="align-center">
        {{ $response['data']->links() }}
    </div>
</div>