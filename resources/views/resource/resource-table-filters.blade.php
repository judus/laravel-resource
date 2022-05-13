@php
    /** @var Previon\Base\Resource\ResourceInterface $resource */
    /** @var Previon\Base\Display\ResourceTable $table */
    /** @var Previon\Base\Fields\AbstractField $field */
@endphp
<!-- include: resource.resource-table-filters.blade.php -->
<tr class="filters" role="row">
    {{-- Checkbox column --}}
    @if ($table->hasCheckboxes())
        <td class="blank"></td>
    @endif

    {{-- Filter input columns --}}
    @foreach ($resource->getFields() as $field)
        @if(! $field->isNotInContext(['table']))
            <td class="cell-type-{{ $field->getType() }} cell-input-{{ $field->getInputType() }} cell-name-{{ $field->getName() }}">
            @if($field->isFilterable())
                    @php
                        $filterValue = '';
                        if (array_key_exists($field->getName(), $table->getFilters())) {
                            $filterValue = $table->getFilters()[$field->getName()];
                        }
                    @endphp
                    <div>
                        @if(in_array($field->getType(), ['belongs_to', 'belongs_to_many']))
                            @php
                                $options = $field->getOptions();
                                $options = $field->getType() == 'belongs_to_many' ?
                                    ['' => __('Select filter')] + $options : $options;
                            @endphp
                            <span class="filter_column filter_text">
                                <select
                                    class="search_init text_filter form-control form-control-sm"
                                    data-field="{{ $field->getName() }}"
                                    name="filters[{{ $field->getName() }}]">
                                    @foreach ($options as $key => $value)
                                        @if(is_array($value))
                                            <optgroup label="{{ $key }}">
                                                @foreach($value as $v => $label)
                                                    @php
                                                        $selected = '';
                                                        if ($v == $filterValue) {
                                                            $selected = ' selected=selected';
                                                        }
                                                    @endphp
                                                    <option value="{{ $v }}"{{ $selected }}>{{ $label }}</option>
                                                @endforeach
                                            </optgroup>
                                        @else
                                            @php
                                            $selected = '';
                                            if ($key == $filterValue) {
                                                $selected = ' selected=selected';
                                            }
                                            @endphp
                                            <option value="{{ $key }}"{{ $selected }}>{{ $value }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </span>
                        @else
                            <span class="filter_column filter_text">
                                <input
                                    class="search_init text_filter form-control form-control-sm"
                                    type="text"
                                    name="filters[{{ $field->getName() }}]"
                                    data-field="{{ $field->getName() }}"
                                    value="{{ $filterValue }}"
                                    placeholder="{{ __('Filter by') }} '{{ $field->getLabel() }}'">
                            </span>
                        @endif
                    </div>
                @endif
            </td>
        @endif
    @endforeach

    {{-- The actions column if any --}}
    @if($table->hasActions())
        <td class="blank"></td>
    @endif

</tr>
<!-- end: resource.resource-table-filters.blade.php -->
