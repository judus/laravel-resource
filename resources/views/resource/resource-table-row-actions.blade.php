<!-- include: resource.resource-table-row-actions.blade.php -->
<td class="cell-actions">
    <div class="btn-group btn-group-sm">
        <a href="{{ route('resource.edit', ['resource' => $resource->getName(), 'id' => $resource->getId()]) }}"
           class="btn btn-default btn-sm">
            <i class="fa fa-pencil-alt mr-1"></i>
            <span>{{ __('Edit') }}</span>
        </a>
        <a href="#" class="btn btn-default btn-sm dropdown-toggle"
           data-toggle="dropdown"></a>
        <ul class="dropdown-menu pull-right">
            <li>
                <a href="{{ route('resource.show', ['resource' => $resource->getName(), 'id' => $resource->getId()]) }}">
                    <i class="fa fa-search mr-1"></i>
                    <span>{{ __('Detail') }}</span>
                </a>
            </li>
            <li class="divider"></li>
            <li>
                <form class="form-delete-link" style="display: inline"
                      action="{{route('resource.destroy', ['resource' => $resource->getName(), 'id' => $resource->getId()]) }}"
                      method="POST"
                      onclick="if (window.confirm('{{ __('Confirm deletion?') }}')) this.submit();">
                    @csrf
                    @method('delete')
                    <a href="javascript:">
                        <i class="fa fa-trash mr-1"></i>
                        <span>{{ __('Delete') }}</span>
                    </a>
                </form>
            </li>
        </ul>
    </div>
</td>
<!-- end: resource.resource-table-row-actions.blade.php -->
