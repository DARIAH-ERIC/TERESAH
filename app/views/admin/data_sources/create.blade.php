@extends("layouts.admin")

@section("content")
    <div class="row">
        <div class="col-sm-8 col-centered">
            <h1 class="text-center">{{ Lang::get("views/admin/data_sources/create.heading") }}</h1>

            @include("shared._error_messages")
            @include("admin.data_sources._form", array(
                $action = "create",
                $options = array(
                  "route" => "admin.data-sources.store",
                  "role" => "form"
                )
            ))
        </div>
        <!-- /col-sm-8.col-centered -->
    </div>
    <!-- /row -->
@stop