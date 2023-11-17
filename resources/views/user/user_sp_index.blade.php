@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b>User's Sales Person Information</b></font>
    <div class="widget-toolbar">
        <a href="{{route('user.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
    </div>
  </div>
</div></legend>

<div class="widget-body">
  <div class="widget-main">
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>
  <form action="{{route('user.sp.create')}}" method="post">
  {{ csrf_field() }}
  <div class="row">
     <div class="col-md-2">
       <input type="text" name="user_id" id="user_id" value="{{$user_id}}" class="form-control" readonly required/>
     </div>
     <div class="col-md-6">
       <input type="text" name="user_name" id="user_name" value="{{$user_name}}" class="form-control" readonly required/>
     </div>
  </div>
  <br/>
   <div class="row">
      <div class="col-md-5">
          <div class="input-group ss-item-required">
                  <select name="sp_id" class="col-xs-6 col-sm-4 chosen-select" id="sp_id" required>
                      <option value="-1">- Select Sales Person -</option>
                      @foreach($salespersons as $sp)
                          <option value="{{ $sp->id }}">{{ $sp->sales_name }}</option>
                      @endforeach
                  </select>
                  @error('sp_id')
                  <span class="text-danger">{{ $message }}</span>
                  @enderror
         </div>
      </div>
      <div class="col-md-3">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-add">Add</span></button>
      </div>
  </div>
  </form>
<br/>

  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view">
        <thead class="thead-blue">
          <th>Id</th>
          <th>Sales Person</th>
          <th>Designation</th>
          <th>Mobile</th>
          <th>Email</th>
          <th>Options</th>
        </thead>
        <tbody>
            @foreach($userssp as $row)
              <tr>
                <td>{{ $row->id }}</td>
                <td>{{ $row->sales_name }}</td>
                <td>{{ $row->vComboName }}</td>
                <td>{{ $row->sales_mobile }}</td>
                <td>{{ $row->sales_email }}</td>
                <td>
                     <form  method="post" action="{{ url('/user-sp/destroy/'.$row->u_spid) }}" class="delete_form">
                       {{ csrf_field() }}
                       {{ method_field('DELETE') }}
                       <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');"><i class="fa fa-pencil-delete-o">Delete</i></button>
                    </form>
                </td>
              </tr>
            @endforeach
          </tbody>
        </table>
      </div>
  </div>
</div>
</div>
</section>
@stop


@section('pagescript')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>
<script type="text/javascript">
  $(document).ready(function() {

  });
</script>

@stop
