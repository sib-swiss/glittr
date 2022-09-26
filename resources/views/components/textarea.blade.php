@props(['disabled' => false, 'rows' => 4])

<textarea
    {{ $disabled ? 'disabled' : '' }}
    rows="{{ $rows }}"
    {!! $attributes->merge(['class' => 'border-gray-300 focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50 rounded-md shadow-sm']) !!}>
</textarea>
