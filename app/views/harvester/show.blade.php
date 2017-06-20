@extends("layouts.default")

@section("master-head")
<div class="row">
    Master-head
</div>
<!-- /row -->
@stop

@section("content")
<section class="row">
    {{ Form::open(array("route" => "harvester.harvest", "role" => "form")) }}
        {{ Form::label("url", Lang::get("views.sessions.form.url.label")) }}
        {{ Form::text("url", null, array("name" => "url")) }}
        {{ Form::submit(Lang::get("harvester.harvest"), array("class" => "button")) }}
    {{ Form::close() }}
</section>
<!-- /row -->
@stop
