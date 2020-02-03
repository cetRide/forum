@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row ">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <div class="level">
                            <span>
 <a href="{{route('profile', $thread->creator)}}">{{$thread->creator->name}}</a> posted:
                            {{$thread->title}}
                                </span>
                            @can('update', $thread)
                                <form action="{{$thread->path()}}" method="POST">
                                    {{csrf_field()}}
                                    {{method_field('DELETE')}}
                                    <button type="submit" class="btn btn-danger">Delete Thread</button>
                                </form>
                            @endcan
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="body">
                            {{$thread->body}}
                        </div>
                    </div>
                </div>
                @foreach($thread->replies as $reply)
                    @include('threads.reply')
                @endforeach
                {{$replies->links()}}
                @if (auth()->check())
                    <form action="{{$thread->path().'/replies'}}" method="POST">
                        {{csrf_field()}}
                        <div class="form-group">
                            <textarea name="body" id="body" class="form-control"
                                      placeholder="Have something to say?"></textarea>
                        </div>
                        <button class="btn btn-primary" type="submit">Post</button>
                    </form>
                @else
                    <p class="text-center">Please <a href="{{route('login')}}">sign in</a> to participate in this
                        discussion</p>
                @endif
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <div class="body">
                            <p>
                                This thread was published {{$thread->created_at->diffForHumans()}} by <a
                                    href="#">{{$thread->creator->name}}</a>, and currently has
                                {{$thread->replies_count}} {{str_plural('comment', $thread->replies_count)}}.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection
