<ul>
    @foreach($childs as $child)
        <li>
            {{ $child->itmname }} {{ $child->itmdesc }}
            @if(count($child->childs))
                @include('items.itm_manageChild',['childs' => $child->childs])
            @endif
        </li>
    @endforeach
</ul>
