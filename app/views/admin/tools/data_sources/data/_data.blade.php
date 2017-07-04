@if ($data->dataType)
    <tr>
        <td>{{{ $data->dataType->label }}}</td>
        <td>{{ $data->value }}</td>
        <td>{{ link_to_route("admin.users.show", e($data->user->name), array("id" => $data->user->id), array("title" => e($data->user->name))) }}</td>
        <td>
            <a href="{{ URL::route("admin.tools.data-sources.data.edit", array($tool->id, $dataSource->id, $data->id)) }}" title="{{ Lang::get("views.admin.tools.data_sources.show.actions.edit.title") }}"><span class="glyphicons pencil"></span></a> 
            <a href="{{ URL::route("admin.tools.data-sources.data.destroy", array($tool->id, $dataSource->id, $data->id)) }}" data-method="delete" data-confirm="{{ e(Lang::get("views.admin.tools.data_sources.show.actions.delete.confirm", array("label" => $data->dataType->label))) }}" rel="nofollow" title="{{ Lang::get("views.admin.tools.data_sources.show.actions.delete.title") }}"><span class="glyphicons remove"></span></a>
        </td>
    </tr>
@endif
