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
    <font size="3" color="blue"><b> Weekly Salary Report</b></font>
    <div class="widget-toolbar">
        <a href="{{route('attendance.list')}}" class="blue"><i class="fa fa-list"></i> List</a>
    </div>
  </div>
</div>
</legend>

<form action="" method="post">
    {{ csrf_field() }}
    <div class="row">
      

      <div class="col-md-4">
        <div class="input-group ss-item-required"> 
          <select name="weekendEntryId" class="chosen-select" id="weekendEntryId" style="width: 350px" required>
            <option value="" disabled selected>- Select  -</option>
            @foreach($lists as $value)  
              <option value="{{ $value->id }}">{{ $value->title }}</option>
            @endforeach
           </select>
         </div>
      </div>

      <div class="col-md-4">
        <div class="input-group ss-item-required"> 
          <select name="sectionId" class=" chosen-select" id="sectionId" style="width: 350px" required>
            <option value="" disabled selected>- Select Section -</option>
            @foreach($sections as $value)  
              <option value="{{ $value->id }}">{{ $value->vComboName }}</option>
            @endforeach
            
           </select>
         </div>
      </div>


      <div class="col-md-4">
        <button type="submit" name="submit"  class="btn btn-sm btn-info submit"> wages And Salary Sheet </button>
        <button type="submit" name="submit"  class="btn btn-sm btn-info submit-summary">Summary Sheet</button>
      </div>
    </div>
  </form>

<div class="widget-body">
  <div class="widget-main">
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>

    
  <div class="row">
     
    <div class="col-md-12">
      @csrf
     
        
      </div>
  </div>
  <div class="col-md-12">
    <div class="card-tools">
        <ul class="pagination pagination-sm float-right">
          <p class="pull-right">
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

  $('.submit').click(function(e){
    e.preventDefault();
    var weekendEntryId = $('#weekendEntryId').val();
    var sectionId = $('#sectionId').val();
    window.location.href = "{{ url('/') }}/"+'attendance/details/'+weekendEntryId+'/'+sectionId;

  })

  $('.submit-summary').click(function(e){
    e.preventDefault();
    var weekendEntryId = $('#weekendEntryId').val();
    var sectionId = $('#sectionId').val();
    window.location.href = "{{ url('/') }}/"+'attendance/wages-sheet/'+weekendEntryId;

  })

</script>

@stop
