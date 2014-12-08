@extends("layouts.admin")

@section("breadcrumb", BreadcrumbHelper::renderAdmin(array(
    link_to_route("admin.tools.index", Lang::get("views.shared.navigation.admin.tools.name"), array(), array("title" => Lang::get("views.shared.navigation.admin.tools.title"))),
    Lang::get("views.shared.navigation.admin.tools.edit.name")
)))

@section("master-head")
    <div class="row">
        <div class="small-12 columns">
            <h1>{{ Lang::get("views.admin.tools.edit.heading") }}</h1>
        </div>
        <!-- /small-12.columns -->
    </div>
    <!-- /row -->
@stop

@section("content")
    <section class="row">
        <div class="small-12 medium-6 columns small-centered">
            @include("shared._error_messages")
            @include("admin.tools._form", array(
                $action = "edit",
                $model = $tool,
                $options = array(
                  "route" => array("admin.tools.update", $tool->id),
                  "method" => "put",
                  "role" => "form"
                )
            ))
        </div>
        <!-- /small-12.medium-6.columns.small-centered -->
    </section>
    <!-- /row -->
@stop
