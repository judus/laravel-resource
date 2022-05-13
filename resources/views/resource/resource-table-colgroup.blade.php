@php
    /** @var Previon\Base\Resource\ResourceInterface $resource */
    /** @var Previon\Base\Display\ResourceTable $table */
    /** @var Previon\Base\Fields\AbstractField $field */
@endphp
<!-- include: resource.resource-table-colgroup.blade.php -->
<colgroup>
    {{-- Column for checkboxes if enabled --}}
    @if ($table->hasCheckboxes())
        <col class="col-checkbox">
    @endif
    {{-- Column for each field in context --}}
    @foreach ($resource->getFields() as $field)
        @if(! $field->isNotInContext(['table']))
            @php
                $sort = $field->getName() == $table->getOrder()[0] ? ' sort ' . strtolower($table->getOrder()[1]) : '';
                $primary = $field->getName() == $resource->getIdentifier() ? ' col-primary' : '';
                $colLabel = $field->getName() == $resource->getLabelField() ? ' col-label' : '';
            @endphp
            <col class="col-type-{{ $field->getType() }} col-input-{{ $field->getInputType() }} col-name-{{ $field->getName() }}{{ $primary }}{{ $colLabel }}{{ $sort }}">
        @endif
    @endforeach
    {{-- Columns for the up/down sorting button if enabled --}}
    @if ($table->hasSortingButtons())
        <col class="col-action-down">
        <col class="col-action-up">
    @endif
    {{-- Column for the action button group --}}
    @if($table->hasActions())
        <col class="col-actions">
    @endif
</colgroup>
<!-- end: resource.resource-table-colgroup.blade.php -->
