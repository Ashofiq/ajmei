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
    <font size="3" color="blue"><b>Edit Employee Manual Attendance Information</b></font>
    <div class="widget-toolbar">
        <a href="{{route('attendance.list')}}" class="blue"><i class="fa fa-list"></i> List</a>
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
 
<br/>
   
  <div class="row">
     
    <div class="col-md-12">

      <table class="table table-striped table-view">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">ID No</th>
          <th class="text-center" scope="col">Name</th>
          <th class="text-center" scope="col">Department</th>
          <th class="text-center" scope="col">Section</th>
          <th class="text-center" scope="col">Designation</th>
          <th class="text-center" scope="col">Salary</th>
          <th class="text-center" scope="col">Day</th> 
          <th class="text-center" scope="col">Hour</th> 
          <th class="text-center" scope="col">ATTn Bonus</th> 
        </thead>
        
        <tbody>
          @if(empty($lists))
            <div class="text-center">
              Data not found  
            </div
          @endif

          @foreach($lists as $row) 
            <td style="display:none;" >{{ $row->id }}</td>
            <td width="10%">{{ $row->empId }}</td>
            <td width="15%">{{ $row->employee->emp_name }}</td>
            <td width="16%">{{ $row->department->vComboName }}</td>
            <td width="8%">{{ $row->section->vComboName }}</td> 
            <td width="20%">{{ $row->department->vComboName }}</td> 
            <td width="20%">{{ $row->employee->emp_present_salary }}</td>
            <td width="20%"><input type="text" value="{{ $row->day }}" name="days"></td>
            <td width="8%"><input type="text" value="{{ $row->hour }}" name="hours"></td>  
            <td width="18%">{{ $row->attnBonus }}</td>
          </tr>
          @endforeach
          </tbody>
        </table>
        <br>
        <div class="submit-btn btn btn-success">submit</div>
      </div>
  </div>
  <div class="col-md-12">
    <div class="card-tools">
        
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

  $('.submit-btn').click(function(){

    var table = $(".table tbody");

    var data = [];
    table.find('tr').each(function (i, el) {
      var $tds = $(this).find('td');
      data.push({
        'id' :  $tds.eq(0).text(),
        'name' :  $tds.eq(2).text(),
        'department' :  $tds.eq(3).text(),
        'section' :  $tds.eq(4).text(),
        'designation' :  $tds.eq(5).text(),
        'day' :  $tds.eq(7).find('input').val(),
        'hour' :  $tds.eq(8).find('input').val(),
        'bonus' :  $tds.eq(9).text(),
      })
    });

      var from = $('#from').val();
      var to = $('#to').val();
      var title = $('#title').val();
      
    //   $.ajax({
    //       url: "{{  url('/') }}"+'/attendance/weekendentry',
    //       type: 'POST',
    //       data: {data, from, to, title, departmentId, sectionId}
    //   })
    //   .done(function(response) {


    //   });

    console.log(data);
  });

  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
  });

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
