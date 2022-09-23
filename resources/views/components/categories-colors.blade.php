@foreach($categories_colors as $category_id => $color)
.tag-category-{{ $category_id }} {
    --category-color: {{ $color['rgb']['R'] }} {{ $color['rgb']['G'] }} {{ $color['rgb']['B'] }};
}
@endforeach
