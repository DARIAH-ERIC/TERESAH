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
    <h1>{{ Lang::get("views.admin.harvester.name.heading") }}</h1>
    <h3>{{ Lang::get("views.admin.harvester.text.enter_url") }}</h3>
    @if (isset($harvest))
        <h1>{{{ $harvest }}}</h1>
    @endif
    @if (isset($tools))
        <h3>{{ Lang::get("views.admin.harvester.text.number_tools") }}: {{{ sizeof($tools) }}}</h3>
        @foreach($tools as $tool)
            <p>{{ link_to_route("tools.show", e($tool->name), array("id" => $tool->slug), array("title" => e($tool->name))) }}</p>
        @endforeach
    @endif
    @if (isset($toolsFullyDescribed))
        <h3>{{ Lang::get("views.admin.harvester.text.number_tools_described") }}: {{{ sizeof($toolsFullyDescribed) }}}</h3>
        @foreach($toolsFullyDescribed as $tool)
            <p>{{ link_to_route("tools.show", e($tool->name), array("id" => $tool->slug), array("title" => e($tool->name))) }}</p>
        @endforeach
    @endif

    {{ Form::open(array("route" => "admin.harvester.save", "role" => "form")) }}
        {{ Form::label("name", Lang::get("views.admin.harvester.form.name.label")) }}
        {{ Form::text("name", null, array("name" => "name", "placeholder" => Lang::get("views.admin.harvester.form.name.placeholder"))) }}
        {{ Form::label("url", Lang::get("views.admin.harvester.form.url.label")) }}
        {{ Form::text("url", null, array("name" => "url", "placeholder" => Lang::get("views.admin.harvester.form.url.placeholder"))) }}
        {{ Form::label("dataSource", Lang::get("views.admin.harvester.form.data_source")) }}
        {{ Form::select("dataSource", $dataSources, array("name" => "dataSource")) }}
        {{ Form::submit(Lang::get("views.admin.harvester.form.save"), array("class" => "button")) }}
    {{ Form::close() }}
</section>
<!-- /row -->
@if(sizeof($harvesters) > 0)
<section class="row">
    <div class="small-12 columns">
        <table class="responsive">
            <thead>
            <tr>
                <th>{{ Lang::get("views.admin.harvester.table.name") }}</th>
                <th>{{ Lang::get("views.admin.harvester.table.url") }}</th>
                <th>{{ Lang::get("views.admin.harvester.table.launch_now") }}</th>
                <th>{{ Lang::get("views.admin.harvester.table.last_launched") }}</th>
                <th>{{ Lang::get("views.admin.harvester.table.active") }}</th>
                <th>{{ Lang::get("views.admin.harvester.table.data_source") }}</th>
                <th>{{ Lang::get("views.admin.harvester.table.actions") }}</th>
            </tr>
            </thead>

            <tbody>
            @foreach ($harvesters as $harvester)
            <tr>
                <td>{{{ $harvester->label }}}</td>
                <td>{{{ $harvester->url }}}</td>
                <td>{{{ $harvester->launch_now }}}</td>
                <td>{{{ $harvester->last_launched }}}</td>
                <td>{{{ $harvester->active }}}</td>
                <td>{{{ $dataSources[$harvester->data_source_id] }}}</td>
                <td>
                    {{ Form::open(array("route" => array("admin.harvester.harvest", $harvester->id), "role" => "form", "method" => "post")) }}
                        {{Form::hidden("_method", "PUT")}}
                        {{Form::button('<span class="glyphicons download_alt"></span>', array("type" => "submit", 'class' => 'btn-link'))}}
                    {{ Form::close() }}
<!--                    <a href="{{ URL::route("admin.harvester.harvest", array("id" => $harvester->id)) }}" title="{{ Lang::get("views.admin.harvester.index.actions.harvest.title") }}"><span class="glyphicons download_alt"></span></a>-->
                    <a href="{{ URL::route("admin.harvester.destroy", array("id" => $harvester->id)) }}" data-method="delete" data-confirm="{{ e(Lang::get("views.admin.harvester.index.actions.delete.confirm", array("name" => $harvester->name))) }}" rel="nofollow" title="{{ Lang::get("views.admin.harvester.index.actions.delete.title") }}"><span class="glyphicons remove"></span></a>
                </td>
            </tr>
            @endforeach
            </tbody>
        </table>
        <!-- /responsive -->
    </div>
    <!-- /small-12.columns -->
</section>
@endif
<!-- /row -->
@stop
