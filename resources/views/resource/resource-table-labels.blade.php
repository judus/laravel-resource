@php
    /** @var Previon\Base\Resource\ResourceInterface $resource */
    /** @var Previon\Base\Display\ResourceTable $table */
    /** @var Previon\Base\Fields\AbstractField $field */
@endphp
<!-- include: resource.resource-table-labels.blade.php -->
<tr class="labels js-table-checkable" role="row">
    @if ($table->hasCheckboxes())
        <th class="cell-checkbox text-center" style="width: 70px">
            <div class="custom-control custom-checkbox custom-control-primary d-inline-block">
                <input type="checkbox" class="custom-control-input" id="check-all-{{ $resource->getName() }}" name="check-all">
                <label class="custom-control-label" for="check-all-{{ $resource->getName() }}"></label>
            </div>
        </th>
    @endif

    @foreach ($resource->getFields() as $field)
        @if(! $field->isNotInContext(['table']))
            @php
                $cellLabel = $field->getName() == $resource->getLabelField() ? ' cell-label' : '';
                $active = $field->isOrdered() ? 'active ' . strtolower($field->getOrdered()) : '';
            @endphp
            <th class="cell-type-{{ $field->getType() }} cell-input-{{ $field->getInputType()}} cell-name-{{ $field->getName() }}{{ $cellLabel }}">
                @if ($table->isSortable() and $field->isSortable())
                    <a href="javascript:void(0)"
                       data-field="{{ $field->getName() }}"
                       class="{{ $active }}">{{ $field->getLabel() }}
                        <span class="sort">
                            <span class="asc"></span>
                            <span class="desc"></span>
                        </span>
                    </a>
                @else
                    {{ $field->getLabel() }}
                @endif
            </th>
        @endif
    @endforeach

    {{-- The actions column if any --}}
    @if($table->hasActions())
        <th class="cell-actions">{{ __('Actions') }}</th>
    @endif
</tr>
<!-- end: resource.resource-table-labels.blade.php -->
