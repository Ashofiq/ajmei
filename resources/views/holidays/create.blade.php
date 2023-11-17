@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" /> 
   
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>
<div class="title">
    <div  style="background-color:#e0e0e0" class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
        <font size="2" color="blue"><b>Holiday Entry</b></font>
        </h6>
        <div class="widget-toolbar">
            <a href="{{route('holiday.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div>
</div>
@if(Session::has('message'))
<div class="row">
   <div class="col-md-12">
     <p class="alert alert-success"><b>{{ Session::get('message') }}</b></p>
   </div>
</div>
@endif
    <div class="widget-body">
      <div class="widget-main">  
       <form id="emp_Form" action="{{route('holiday.store')}}" method="post">  
       {{ csrf_field() }}
       
        <div class="row">  
            <div class="col-md-4">
               <div class="input-group">
                 <div class="input-group-prepend">
                     <div class="input-group-text" style="min-width:130px">Company:</div>
                 </div>
                    <select name="company_code" class="autocomplete" id="company_code"  style="max-width:150px" required>
                    <option value="" >--Select--</option>
                        @if ($companies->count())
                            @foreach($companies as $company)
                                <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                            @endforeach
                        @endif
                    </select>
                </div>
           </div> 
        </div>
          
        <div class="row">
            <div class="col-md-6">
                <div class="input-group ss-item-required">
                    <div class="input-group-prepend ">
                        <div class="input-group-text" style="min-width:130px">Date:</div>
                    </div>
                    <input type="text" size = "15" name="holiday_date" onclick="displayDatePicker('holiday_date');"  value="{{ old('holiday_date') }}"  required />
                    <a href="javascript:void(0);" onclick="displayDatePicker('holiday_date');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a> 
            </div>
          </div>
        </div>

        <div class="row">
            <div class="col-md-7">
                <div class="input-group ss-item-required">
                    <div class="input-group-prepend">
                        <div class="input-group-text" style="min-width:130px">Remarks:</div>
                    </div>
                    <textarea name="remarks" rows="2" cols="100" class="form-control config" placeholder="Remarks" maxlength="500" required>{{ old('remarks') }}</textarea>
                </div>
            </div>
        </div>
          
         
        <div class="row justify-content-left">
          <div class="col-sm-12 text-left">
              <button class="btn btn-sm btn-success" type="submit" ><i class="fa fa-save"></i> Save</button>
              <a href="{{route('holiday.index')}}" class="btn btn-sm btn-info"><i class="fa fa-list"></i> List</a>
          </div>
        </div>
    </div>
  </div>
</form>
</section>

@stop
@section('pagescript')
  <script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
  <script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
  <script src="{{ asset('assets/js/ace.min.js') }}"></script>

 

@stop
