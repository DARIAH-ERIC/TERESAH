@extends("layouts.default")

@section("master-head")
<div class="row">
    Master-head
</div>
<!-- /row -->
@stop

@section("content")
<section class="row">
    <h1>Harvester page</h1>
    <h3>You can enter an URL where a tool exists in order to harvest it into TERESAH</h3>
    <h3>The data will be inserted using the "Has Tool Registry" Data source</h3>
    @if (isset($harvest))
        <h1>{{{ $harvest }}}</h1>
    @endif

    {{ Form::open(array("route" => "harvester.harvest", "role" => "form")) }}
        {{ Form::label("url", Lang::get("views.sessions.form.url.label")) }}
        {{ Form::text("url", null, array("name" => "url")) }}
        {{ Form::submit(Lang::get("harvester.harvest"), array("class" => "button")) }}
    {{ Form::close() }}
</section>
<!-- /row -->
@stop
