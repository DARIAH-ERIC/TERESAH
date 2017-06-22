@extends("layouts.default")

@section("breadcrumb", BreadcrumbHelper::renderAdmin(array(
    Lang::get("views.shared.navigation.admin.harvester.name")
)))

@section("master-head")
<div class="row">
    <div class="small-12 columns">
        <h1>{{ Lang::get("views.admin.harvester.name.heading") }}</h1>
    </div>
    <!-- /small-12.columns -->
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
        {{ Form::label("url", Lang::get("views.admin.harvester.form.url.label")) }}
        {{ Form::text("url", null, array("name" => "url", "placeholder" => Lang::get("views.admin.harvester.form.url.placeholder"))) }}
        {{ Form::submit(Lang::get("views.admin.harvester.form.harvest"), array("class" => "button")) }}
    {{ Form::close() }}
</section>
<!-- /row -->
@stop
