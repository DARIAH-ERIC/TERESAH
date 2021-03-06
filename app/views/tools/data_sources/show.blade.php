@extends("layouts.default")

@if (str_contains(URL::previous(), "search"))
    @section("breadcrumb", BreadcrumbHelper::render(array(
        link_to(URL::previous(), Lang::get("views.shared.navigation.search.name")),
        e($tool->name)
    )))
@elseif (str_contains(URL::previous(), "by-facet"))
    @if (Session::get("breadcrumb") !== null)
        @section("breadcrumb", BreadcrumbHelper::render(Session::get("breadcrumb")))
    @else
        @section("breadcrumb", BreadcrumbHelper::render(array(
            link_to_route("tools.index", Lang::get("views.shared.navigation.browse.all.name"), null, array("title" => Lang::get("views.shared.navigation.browse.all.title"))),
            link_to(URL::previous(), Lang::get("views.shared.navigation.browse.by_facet.name")),
            e($tool->name)
        )))
    @endif
@elseif (str_contains(URL::previous(), "my-tools"))
    @section("breadcrumb", BreadcrumbHelper::render(array(
        link_to(URL::previous(), Lang::get("views.users.tools.name")),
        e($tool->name)
    )))
@else
    @section("breadcrumb", BreadcrumbHelper::render(array(
        link_to_route("tools.index", Lang::get("views.shared.navigation.browse.all.name"), null, array("title" => Lang::get("views.shared.navigation.browse.all.title"))),
        e($tool->name)
    )))
@endif

@section("master-head")
    <div class="row">
        <div class="small-12 medium-7 columns">
            <div class="symbol">
                <abbr title="{{{ $tool->name }}}">{{{ $tool->abbreviation }}}</abbr>
            </div>
            <!-- /symbol -->

            <h1><span itemprop="name">{{{ $tool->name }}}</span></h1>
        </div>
        <!-- /small-12.medium-7.columns -->

        <div class="small-12 medium-5 columns">
            @if (Auth::user() != null)
             <ul class="toolbar">
                @if (Auth::user()->toolUsages()->where("tool_id", "=", $tool->id)->count() != 0)                                        
                    <li>
                        <a id="toolUsageButton" class="starred button right" data-callback="{{ URL::route("tools.unuse", array("toolID" => $tool->id)) }}" data-action="DELETE" title="{{ Lang::get("views.tools.data_sources.show.unuse.title") }}" role="button"></a>
                    </li>
                @else
                    <li>                        
                        <a id="toolUsageButton" class="unstarred button right" data-callback="{{ URL::route("tools.use", array("toolID" => $tool->id)) }}" data-action="GET" title="{{ Lang::get("views.tools.data_sources.show.use.title") }}" role="button"></a>
                    </li>
                @endif     
                @if (Auth::user()->hasAdminAccess())                                        
                    <li>
                        <a href="{{ URL::route("admin.tools.edit", array("id" => $tool->id)) }}" class="button right" title="{{ Lang::get("views.shared.navigation.admin.tools.edit.title") }}" role="button"><span class="glyphicons pencil"></span></a>
                    </li>
                @endif
             </ul>
            <!-- /toolbar -->
            @endif            
        </div>
        <!-- /small-12.medium-5.columns -->
    </div>
    <!-- /row -->
@stop

@section("content")
    <section class="row">
        @if (!$tool->is_filled)
            <h2 class="red-warning"><span class="glyphicons warning_sign"></span>This tool does not have all necessary describing elements, it will not be shown to the users. Please edit this tool.</h2>
        @endif
        <article class="small-12 columns" vocab="http://schema.org/" typeof="SoftwareApplication">
            @include("tools.data_sources._navigation", array("dataSources" => $tool->dataSources))
            <div style="display: none;" property="name">{{{ $tool->name }}}</div>
            <div class="tabs-content">
                @foreach ($tool->dataSources as $dataSource)
                    <div class="content{{ Active::path(ltrim(parse_url(URL::route("tools.data-sources.show", array($tool->slug, $dataSource->slug)))["path"], "/"), " active") }}">
<!--                    <div property="{{URL::route("tools.data-sources.show", array($tool->slug, $dataSource->slug))}}" class="content{{ Active::path(ltrim(parse_url(URL::route("tools.data-sources.show", array($tool->slug, $dataSource->slug)))["path"], "/"), " active") }}">-->
                        <div class="row">
                            <div class="small-12 medium-8 columns">
                                @if (!$dataSource->data->isEmpty())
                                    @if (($name = $dataSource->getLatestToolDataFor($tool->id, "name")) && ($description = $dataSource->getLatestToolDataFor($tool->id, "description")))
                                        <h2>{{{ $name }}}</h2>

                                        <p property="description">{{{ $description }}}</p>

                                        <hr />
                                    @endif

                                    <h3>{{ Lang::get("views.tools.data_sources.show.heading.available_data") }}</h3>

                                    <dl class="data">
                                        @foreach ($dataSource->groupedData as $label => $dataList)
                                            <dt>{{{ $label }}}</dt>
                                            <dd>
                                                @foreach ($dataList as $index => $data)
                                                    @if ($data->dataType)
                                                        <?php
                                                            $rdfValue = str_replace("http://schema.org/", "", $data->dataType->rdf_mapping);
                                                            $valueOrLabel = $data->value;
                                                            if(array_key_exists($data->value, $dataTypeOptions)) {
                                                                $valueOrLabel = $dataTypeOptions[$data->value];
                                                            }
                                                        ?>
                                                        @if (filter_var($data->value, FILTER_VALIDATE_URL))
                                                            {{ link_to($data->value, Str::limit($valueOrLabel, 60), array("property" => $rdfValue)) }}{{ ($index < count($dataList) - 1) ? "," : null }}
                                                        @elseif ($data->dataType->linkable && $data->dataType->schema_linkable)
                                                            {{ link_to_route("tools.by-facet", $valueOrLabel, array($data->dataType->slug, $data->slug), array("property" => $rdfValue)) }}{{ ($index < count($dataList) - 1) ? "," : null }}
                                                        @elseif ($data->dataType->linkable)
                                                            <span style="display: none;" property="{{{ $rdfValue }}}">{{{ $data->value }}}</span>
                                                            {{ link_to_route("tools.by-facet", $valueOrLabel, array($data->dataType->slug, $data->slug)) }}{{ ($index < count($dataList) - 1) ? "," : null }}
                                                        @else
                                                            <span property="{{{ $rdfValue }}}">{{{ $data->value }}}</span>{{ ($index < count($dataList) - 1) ? "," : null }}
                                                        @endif
                                                    @endif
                                                @endforeach
                                            </dd>
                                        @endforeach
                                    </dl>
                                    <!-- /data -->

                                    <h3>{{ Lang::get("views.tools.data_sources.show.available_data_formats") }}</h3>

                                    @if (in_array("JsonLD", $rdf_formats))
                                        {{ link_to_route('tools.export', "RDF/JsonLD", array($tool->slug, "jsonld"), array("class" => "button data-format", "role" => "button")) }}
                                    @endif
                                    @if (in_array("Turtle", $rdf_formats))
                                        {{ link_to_route('tools.export', "RDF/Turtle", array($tool->slug, "turtle"), array("class" => "button data-format", "role" => "button")) }}
                                    @endif
                                    @if (in_array("XML", $rdf_formats))
                                        {{ link_to_route('tools.export', "RDF/XML", array($tool->slug, "rdfxml"), array("class" => "button data-format", "role" => "button")) }}
                                    @endif
                                    @if (in_array("nTriples", $rdf_formats))
                                        {{ link_to_route('tools.export', "RDF/N-Triples", array($tool->slug, "ntriples"), array("class" => "button data-format", "role" => "button")) }}
                                    @endif
                                @else
                                    <div class="alert alert-info">
                                        <p class="text-center">{{ Lang::get("views.tools.data_sources.show.messages.no_data") }}</p>
                                    </div>
                                    <!-- /alert.alert-info -->
                                @endif
                            </div>
                            <!-- /small-12.medium-8.columns -->

                            <aside class="small-12 medium-4 columns">
                                <h3 class="icon info small">About the Data Source</h3>

                                <p>{{{ nl2br($dataSource->description) }}}</p>
                            </aside>
                            <!-- /small-12.medium-4.columns -->
                        </div>
                        <!-- /row -->
                    </div>
                    <!-- /content -->
                @endforeach
            </div>
            <!-- /tabs-content -->
        </article>
        <!-- /small-12.columns -->
    </section>
    <!-- /row -->

    @if (count($similarTools) > 0)
        <section class="row">
            <div class="small-12 columns">
                <h1 class="icon similar-tools">{{ Lang::get("views.tools.data_sources.show.similar_tools") }}</h1>

                <ul class="small-block-grid-1 medium-block-grid-4">
                    @foreach($similarTools as $similarTool)
                        @include("tools._tool", array("tool" => $similarTool, "type" => "block-grid"))
                    @endforeach
                </ul>
                <!-- /small-block-grid-1.medium-block-grid-4 -->
            </div>
            <!-- /small-12.columns -->
        </section>
        <!-- /row -->
    @endif
@stop
