@php
    use Illuminate\Support\Arr;

    /** @var Previon\Base\Resource\Views\ResourceTable $table */
    /** @var Previon\Base\Resource\Contracts\ResourceInterface $resource */

    // TODO: Pass $resource directly to the view and clear this line
    $resource = $table->getResource();

    // TODO: Improve hasFilter(), the table model is aware of the resource
    $hasFilters = $table->hasFilters() && $resource->hasFilterableFields();

    // TODO: Move this to the model
    $colspan = 0;
    $table->hasCheckboxes() and $colspan++;
    $table->hasActions() and $colspan++;

    // TODO: Move this to the model
    foreach($resource->getFields() as $field) {
        $field->isNotInContext(['table']) or $colspan++;
    }
@endphp
<div class="table-responsive widget-resource-table">
    <table
        class="table table-outer-bordered table-striped table-condensed table-hover small js-table-checkable"
        data-id="{{ $table->getId() }}"
        data-resource="{{ $resource->getName() }}">
        @include('resource.resource-table-colgroup')
        @if($table->hasLabels() or $resource->hasFilterableFields())
            <thead>
                @includeWhen($table->hasLabels(), 'resource.resource-table-labels')
                @includeWhen($hasFilters, 'resource.resource-table-filters')
            </thead>
        @endif
        <tbody>
            @if ($table->hasRows())
                @foreach($table->getRows() as $resource)
                    <tr id="{{ $resource->getId() }}">
                        {{-- Checkbox column --}}
                        @if ($table->hasCheckboxes())
                            <td class="cell-checkbox">
                                <div class="custom-control custom-checkbox custom-control-primary d-inline-block">
                                    <input type="checkbox" class="custom-control-input" id="row_{{ $resource->getName() }}_{{ $resource->getId() }}" name="ids[]">
                                    <label class="custom-control-label" for="row_{{ $resource->getName() }}_{{ $resource->getId() }}"></label>
                                </div>
                                {{-- <input type="checkbox" name="ids[]" value="{{ $resource->getId() }}"> --}}
                            </td>
                        @endif

                        {{-- Data columns --}}
                        @foreach($resource->getFields() as $field)
                            @if(! $field->isNotInContext(['table']))
                                @php
                                    /** @var Previon\Base\Fields\AbstractField $field */
                                    $isLabelField = $field->getName() == $resource->getLabelField();
                                    $cellLabel =  $isLabelField ? ' cell-label' : '';
                                @endphp
                                <td class="cell-type-{{ $field->getType() }}{{ $cellLabel }}">
                                    @if($isLabelField)
                                        <a href="{{ url()->route('resource.show', [$resource->getName(), $resource->getId()]) }}"
                                            class="link-incognito">
                                            {!! $field->getValue()  !!}
                                        </a>
                                        @else
                                        {!! $field->getValue()  !!}
                                    @endif
                                </td>
                            @endif
                        @endforeach

                        {{-- Actions column --}}
                        @includeWhen($table->hasActions(), 'resource.resource-table-row-actions')
                    </tr>
                @endforeach
            @else
                <tr>
                    <td class="no-records" colspan="{{ $colspan }}">{{ __('No entries') }}</td>
                </tr>
            @endif
        </tbody>
        @if ($resource->getPaginator() && $resource->getPaginator()->hasPages())
            <tfoot>
                <tr>
                    <td class="no-records" colspan="{{ $colspan }}">
                        <div class="row">
                            <div class="col-4">
                                @if($table->hasCheckboxes())
                                    <div class="button-link">
                                        <a href="#" class="btn btn-default btn-sm">
                                            <i class="fa fa-trash mr-1"></i>
                                            <span>{{ __('Delete all selected') }}</span>
                                        </a>
                                    </div>
                                @endif
                            </div>
                            <div class="col-4">
                                <div class="paginator">{{ $resource->getPaginator()->appends(Arr::except(Request::query(), $resource->getPaginator()->getPageName()))->links() }}</div>
                            </div>
                            <div class="col-4 text-right">
                                <div class="button-link">
                                    <a href="{{ route('resource.create', [$resource->getName()]) }}"
                                       class="btn btn-default btn-sm">
                                        <i class="fa fa-plus-circle mr-1"></i>
                                        <span>{{ __('Create new ' . $resource->getLabel()) }}</span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        @endif
    </table>
</div>
