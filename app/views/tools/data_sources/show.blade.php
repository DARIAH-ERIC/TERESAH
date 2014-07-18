@extends("layouts.default")

@section("breadcrumb", BreadcrumbHelper::render(array(
    Lang::get("views/pages/navigation.browse.name"), 
    link_to_route("tools.index", Lang::get("views/pages/navigation.browse.all.name"), null, array("title" => Lang::get("views/pages/navigation.browse.all.title"))),
    e($tool->name)
)))

@section("content")
    <article class="row">
        <div class="col-sm-12">
            <header>
                <div class="symbol">
                    <abbr title="{{{ $tool->name }}}">{{{ $tool->getAbbreviation() }}}</abbr>
                </div>
                <!-- /symbol -->

                <h1>{{{ $tool->name }}} <small>{{ Lang::get("views/tools/data_sources/show.on") }}</small></h1>
            </header>

            @if (!$dataSources->isEmpty())
                @include("tools.data_sources._navigation", compact("dataSources"))

                <div class="tab-content">
                    <div class="tab-pane active">
                        @if (!empty($dataToolName = $dataSource->getDataValue("name", $tool->id)))
                            <h2>{{{ $dataToolName }}}</h2>

                            @if (!empty($dataToolDescription = $dataSource->getDataValue("description", $tool->id)))
                                <p>{{{ $dataToolDescription }}}</p>
                            @endif

                            <hr />
                        @endif

                        @if (!$dataSourceData->isEmpty())
                            <h3>{{ Lang::get("views/tools/data_sources/show.heading.available_data") }}</h3>

                            <dl>  
                                @foreach ($dataSourceData as $data)
                                    <dt>{{{ $data->key }}}</dt>
                                    <dd>{{{ $data->value }}}</dd>
                                @endforeach
                            </dl>
                        @else
                            <div class="alert alert-info">
                                <p class="text-center">{{ Lang::get("views/tools/data_sources/show.messages.no_data") }}</p>
                            </div>
                            <!-- /alert.alert-info -->
                        @endif
                    </div>
                    <!-- /tab-pane.active -->
                </div>
                <!-- /tab-content -->
            @else
                <div class="alert alert-info">
                    <p class="text-center">{{ Lang::get("views/tools/data_sources/show.messages.no_data_sources") }}</p>
                </div>
                <!-- /alert.alert-info -->
            @endif
        </div>
        <!-- /col-sm-12 -->
    </article>
    <!-- /row -->
@stop
