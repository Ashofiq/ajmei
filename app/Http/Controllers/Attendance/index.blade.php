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
    <font size="3" color="blue"><b>Employee Manual Attendance Information </b></font>
    <div class="widget-toolbar">
        <!-- <a href="{{route('attendance.list')}}" class="blue"><i class="fa fa-list"></i> List</a> -->
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

  <?php 
    if (isset($lastEntryMessage)) {
      echo '<p class="alert alert-danger text-center">'. $lastEntryMessage .'</p>';
    }
  ?>

<br/>
    @if(!empty($results))
      <div class="row">
        <div class="col-md-2">
          From : <input type="date" id="from" value="{{ $lastEntry->fromDate }}" disabled>
        </div>
        <br>
        <div class="col-md-2">
         To: <input type="date" id="to" value="{{ $lastEntry->toDate }}" disabled>
        </div>
        <br>
        <div class="col-md-4">
          <input type="hidden" value="{{ $lastEntry->id }}" class="activeEntryId">
          Title: <input type="text" placeholder="title" id="title" style="width: 100%" value="{{ $lastEntry->title }}" disabled>
        </div>

        <!-- <div class="col-md-4"><br>
         
        </div> -->

     
      </div

    @endif
    <br>
    <br>
  <div class="row">
     
    <div class="col-md-12">
      @csrf
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
          <th class="text-center" scope="col">OT Hour</th> 
          <th class="text-center" scope="col">Adv. Deduct.</th> 
          <th class="text-center" scope="col">ATTn Bonus</th> 
          <th class="text-center" scope="col">yMark</th> 
          <th class="text-center" scope="col">Previous Day</th> 

        </thead>
        
        <tbody>
          @if(empty($results))
            <div class="text-center">
              Data not found  
            </div
          @endif

          <?php //echo "<pre>"; print_r($results); ?>
          @foreach($results as $row) 
          <?php $row = (object) $row; ?>
            <td style="display:none;" >{{ $row->id }}</td>
            <td width="5%">{{ $row->idNo }}</td>
            <td width="10%">{{ $row->name }}</td>
            <td width="16%">{{ $row->department }}</td>
            <td width="18%">{{ $row->section }}</td> 
            <td width="15%">{{ $row->designation }}</td> 
            <td width="10%">{{ $row->salary }}</td>
            <td width="8%"><input style="width:100%" type="text" name="days" value="{{ $row->day }}"></td>
            <td width="8%"><input style="width:100%" type="text" name="hours" value="{{ $row->hour }}"></td>  
            <td width="8%"><input style="width:100%" type="text" name="otHour" value="{{ $row->otHour }}"></td>  
            <td width="10%"><input style="width:100%" type="text" name="adv_deduction" value="{{ $row->adv_deduction }}"></td>
            <td width="10%">{{ $row->attnBonus }}</td>
            <td width="8%"><input style="width:100%" id="yMark" value="{{ $row->yMark }}"> </td>
            <td width="8%"><input style="width:100%" type="text" name="previousDays" value="{{ $row->previousDays }}"></td>
            <td style="display:none;"><input id="departmentId" value="{{ $row->departmentId }}"> </td>
            <td style="display:none;"><input id="sectionId" value="{{ $row->sectionId }}"> </td>
            <td style="display:none;"><input id="unitId" value="{{ $row->unitId }}"> </td>
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

  $('#new').on('click', function(){
    
    is_checked = $(this).is(':checked');
    console.log('ddd:', is_checked);
    if(is_checked){ 
      $('#newDate').show();
    }else { 
      $('#newDate').hide();
    }

  });

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
        'previousDays' :  $tds.eq(12).find('input').val(),
        'hour' :  $tds.eq(8).find('input').val(),
        'otHour' :  $tds.eq(9).find('input').val(),
        'adv_deduction' :  $tds.eq(10).find('input').val(),
        'bonus' :  $tds.eq(11).text(),
        'yMark' :   $tds.eq(12).find('input').val(),
        'departmentId': $tds.eq(14).find('input').val(),
        'sectionId': $tds.eq(15).find('input').val(),
        'unitId': $tds.eq(16).find('input').val(),
      })
    });

    console.log(data[0].unitId);

      var from = $('#from').val();
      var to = $('#to').val();
      var title = $('#title').val();
      var activeEntry = $('.activeEntryId').val();
     
      console.log('data: ', $('.activeEntryId').val());
      
      $.ajax({
          url: "{{  url('/') }}"+'/attendance/weekendentry',
          type: 'POST',
          data: {data, from, to, title, activeEntry}
      })
      .done(function(response) {
        console.log('response', response);

        if (response == true) {
          Swal.fire({
              position: 'top-end',
              icon: 'success',
              title: 'Attendance Done',
              showConfirmButton: false,
              timer: 1500
          })
        }else{
          Swal.fire({
              position: 'top-end',
              icon: 'error',
              title: 'Attendance Allready Done',
              showConfirmButton: false,
              timer: 2200
          })
        }

      });

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
