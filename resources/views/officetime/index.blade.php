@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<!-- Start Add Modal -->
@include('inc.showAccModal')
<!-- End Add Modal -->
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>
<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b>Office Timing Information</b></font>
    <div class="widget-toolbar">
        <a href="{{route('timesheet.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
  <form action="{{route('timesheet.search')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-3">
         <a href="{{route('timesheet.create')}}" class="btn btn-success btn-sm">
           <i class="fa fa-plus"></i>Add New</a>
        </div>

      <div class="col-md-5">
        <div class="input-group ss-item-required"> 
          <select name="shift_id" class="col-xs-6 col-sm-4 chosen-select" id="shift_id" required>
              <option value="" disabled selected>- Select Shift -</option>
                @foreach($sysinfos as $infos)
                  <option {{ old('shift_id') == $infos->id ? 'selected' : '' }} value="{{ $infos->id }}">{{ $infos->vComboName }}</option>
                @endforeach
           </select>
            @error('shift_id')
              <span class="text-danger">{{ $message }}</span>
            @enderror
               
         </div>
      </div>
      <div class="col-md-3">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search"></span></button>
      </div>
  </div>
  </form>
<br/>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Shift</th> 
          <th class="text-center" scope="col">In Time</th> 
          <th class="text-center" scope="col">Out Time</th> 
          <th class="text-center" scope="col">Grace Time</th> 
          <th class="text-center" colspan="2">Options</th>
        </thead>
        
        <tbody> 
          @foreach($rows as $row) 
            <td>{{ $row->id }}</td>
            <td width="10%">{{ $row->vComboName }}</td> 
            <td width="20%">{{ date('H:i:s',strtotime($row->dtStart)) }}</td> 
            <td width="20%">{{ date('H:i:s',strtotime($row->dtEnd)) }}</td>
            <td width="20%">{{ date('H:i:s',strtotime($row->dtGraceTime)) }}</td>  
            <td width="18%">
              <form  method="post" action="{{ url('/timesheet/destroy/'.$row->id) }}" class="delete_form">
                {{ csrf_field() }}
                {{ method_field('DELETE') }} 
                <div class="btn-group btn-corner">
                  <a><a href="{{ route('timesheet.edit',$row->id) }}" class="btn btn-xs btn-primary edit">Edit</a> 
                  <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
                </div>
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
  </div>
  <div class="col-md-12">
    <div class="card-tools">
        <ul class="pagination pagination-sm float-right">
          <p class="pull-right">
             {{ $rows->render("pagination::bootstrap-4") }} 
          </p>
        </ul>
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
  // show modal
  $('.viewModal').click(function(event) {
      event.preventDefault(); 
      var url = $(this).attr('href');
      //alert(url);
      $('#exampleModal').modal('show');
      $.ajax({
          url: url,
          type: 'GET',
          dataType: 'html',
      })
      .done(function(response) {
          $("#exampleModal").find('.modal-body').html(response);
      });
    });

  }); 
</script>

@stop
