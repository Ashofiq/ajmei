<ul>
    @foreach($childs as $child)
        <li>
            {{ $child->acc_head }}{{ $child->file_level == 0 ? '':' - '.$child->file_level }}
            @if(count($child->childs))
                @include('accounts.manageChild',['childs' => $child->childs])
            @endif
        </li>
    @endforeach
</ul>
