<!DOCTYPE html>
<html>
<head>
    <title>Laravel Category Treeview Example - NiceSnippets.com</title>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js" ></script>
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/css/bootstrap.min.css" />

    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" />
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
    <!-- link href="/css/treeview.css" rel="stylesheet" -->
    <link rel="stylesheet" type="text/css" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.css" />

</head>
<body>
    <div class="container">
    <input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
        <div class="panel panel-primary">
            <div class="panel-heading">Manage Chart of Accounts TreeView</div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h3>{{$company->id}}::{{$company->name}}</h3>
                            <ul id="tree1">
                                @foreach($categories as $category)
                                    <li>
                                        {{ $category->acc_head }}{{ $category->file_level==0?'':' - '.$category->file_level}}
                                        @if(count($category->childs))
                                            @include('accounts.manageChild',['childs' => $category->childs])
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- script src="/js/treeview.js"></script -->
    <script type="text/javascript" charset="utf8" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-treeview/1.2.0/bootstrap-treeview.min.js"></script>

</body>
</html>
