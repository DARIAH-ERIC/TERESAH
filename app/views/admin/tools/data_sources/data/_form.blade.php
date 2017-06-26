<div class="panel">
    {{ FormHelper::open($model, $options) }}
        <div class="row">
            <div class="small-12 columns">
                {{ Form::label("data_type_id", Lang::get("views.admin.tools.data_sources.data.form.data_type.label")) }}
                {{ Form::select("data_type_id", $dataTypes, null, array("id" => "data_type_id")) }}
            </div>
            <!-- /small-12.columns -->
        </div>
        <!-- /row -->

        <div class="row" id="textarea_div">
            <div class="small-12 columns">
                {{ Form::label("value", Lang::get("views.admin.tools.data_sources.data.form.value.label")) }}
                {{ Form::textarea("value", null, array("placeholder" => Lang::get("views.admin.tools.data_sources.data.form.value.placeholder"))) }}
            </div>
            <!-- /small-12.columns -->
        </div>
        <!-- /row -->

        <div class="row" id="select_div">
            <div class="small-12 columns">
                {{ Form::label("value", Lang::get("views.admin.tools.data_sources.data.form.value.label")) }}
                {{ Form::select("data_type_options", array(), null, array("id" => "data_type_options")) }}
            </div>
            <!-- /small-12.columns -->
        </div>
        <!-- /row -->

        {{ Form::submit(Lang::get("views.admin.tools.data_sources.data.{$action}.form.submit"), array("class" => "button")) }} &ndash; {{ Lang::get("views.shared.form.or") }} {{ link_to_route("admin.tools.data-sources.show", Lang::get("views.shared.form.cancel"), array($tool->id, $dataSource->id), array("title" => e($tool->name))) }}
    {{ Form::close() }}
</div>
<!-- /panel -->
<script language="JavaScript">
    $(document).ready(function() {
        modifyViewTextAreaOrDropdownList($('#data_type_id').find('option:selected').val());
        $("#data_type_id").change(function() {
            modifyViewTextAreaOrDropdownList(this.value);
        });
        $("#data_type_options").change(function(e) {
            copyValueToTextarea($('#data_type_options option:selected').text());
        });
    });
    function copyValueToTextarea(myValue) {
        if(myValue !== "") {
            $("#value").val(myValue);
        }
    }
    function modifyViewTextAreaOrDropdownList(dataTypeId) {
        var dataTypeOptionArray = [];
        <?php
                $js_array = json_encode($dataTypeOptions);
                echo "dataTypeOptionArray = " . $js_array . ";\n";
        ?>
        $('#data_type_options').empty();

        var selectedDataTypeOption = '';
        <?php
        if(isset($data)) {
            echo "selectedDataTypeOption = '" . $data->value . "';\n";
        }
        ?>

        if(dataTypeOptionArray[dataTypeId] !== null && Object.keys(dataTypeOptionArray[dataTypeId]).length > 0) {
            $.each(dataTypeOptionArray[dataTypeId], function(id, value) {
                if(selectedDataTypeOption === value) {
                    $('#data_type_options').append($('<option>', {value: id, selected: "selected"}).text(value));
                } else {
                    $('#data_type_options').append($('<option>', {value: id}).text(value));
                }
            });
            copyValueToTextarea($('#data_type_options').find('option:selected').text());
            $("#select_div").removeClass("hidden");
            $("#textarea_div").addClass("hidden");
        } else {
            $("#select_div").addClass("hidden");
            $("#textarea_div").removeClass("hidden");
            $("#value").val(selectedDataTypeOption);
        }
    }
</script>
