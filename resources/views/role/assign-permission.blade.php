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
    <font size="3" color="blue"><b>Role Mapping Information</b></font>
    <div class="widget-toolbar">
        <a href="{{ route('role.index') }}" class="blue"><i class="fa fa-list"></i> List</a>
    </div>
    <div class="widget-toolbar">
        <a href="{{ route('role.index') }}" class="blue"><i class="fa fa-arrow-left"></i> Back</a>
    </div>
  </div>
</div></legend>

<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>

<form action="{{route('role.access.store')}}" method="POST">
  {{ csrf_field() }}
   <div class="row">
    <div class="col-md-2">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Code&nbsp;:</span>
        </div>
        <input type="text" name="id" id="id" value="{{$id}}" class="form-control" readonly required/>
      </div>
    </div>
    <div class="col-md-3">
     <div class="input-group mb-2">
       <div class="input-group-prepend">
         <span class="input-group-text">Name&nbsp;:</span>
       </div>
       <input type="text" name="role_name" id="role_name" value="{{$role_name}}" class="form-control" readonly required/>
    </div>
    </div>
    <!--div class="col-md-4">
       <div class="input-group ss-item-required">
           <select name="group" class="chosen-select" id="group"  style="max-width:250px" required>
             <option value="-1" >--Group--</option>
                 @if ($group->count())
                     @foreach($group as $cmb)
                         <option {{ old('group') == $cmb->group ? 'selected' : '' }} value="{{$cmb->group}}" >{{ $cmb->group }}</option>
                     @endforeach
                 @endif

           </select>
      </div>
    </div -->

     <div class="col-md-1">
       <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Are You Sure? Want to Save It.');"
              title="Save">Save
       </button>
     </div>
  </div>

<br/>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-report">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Group</th>
          <th class="text-center" scope="col">Name</th>
          <th><input type="checkbox" id="checkAll" name="checkAll" onClick="china_toggle(this)" class="ace">
            <span class="lbl"> Check All</span></th>
        </thead>
        <tbody>
          @foreach($permission_list as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->group }}</td>
            <td>{{ $row->name }}</td>
            <td>
              @if($roles->permissions != '')
                @if(in_array($row->key, json_decode($roles->permissions)))
                  <input id="permissions[]" name="permissions[]" checked="true" onchange="selectAll($(this), 'all')" value="{{$row->key}}" type="checkbox" class="ace">
                  <span class="lbl"></span>
                @else
                  <input id="permissions[]" name="permissions[]" onchange="selectAll($(this), 'all')" value="{{$row->key}}" type="checkbox" class="ace">
                  <span class="lbl"></span>
                @endif
              @else
                <input id="permissions[]" name="permissions[]" onchange="selectAll($(this), 'all')" value="{{$row->key}}" type="checkbox" class="ace">
                <span class="lbl"></span>
              @endif
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
  </div>
 </form>
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
  function selectAll(el, elclass){
      if(el.is(':checked')){
          $('.'+elclass).prop('checked', true)
      }else{
          $('.'+elclass).prop('checked', false)
      }
  }

  function china_toggle(source)
  {
      checkboxes = document.getElementsByName('permissions[]');
    	for(var i=0, n=checkboxes.length;i<n;i++)
    	{
    				checkboxes[i].checked = source.checked;
    	}
  }

</script>

@stop
