@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>

  <div class="title">
    <legend>
    <div class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
          <font size="3" color="blue"><b>Suppliers Entry Form</b></font>
        </h6>
       <div class="widget-toolbar">
         <!--<a href="{{route('suppliers.index')}}" class="blue"><i class="fa fa-list"></i> List</a>-->
        </div>
    </div></legend>
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
        <form id="get_Form" action="{{route('get.suppliers')}}" method="post">
        {{ csrf_field() }}
        <div class="row">
         <div class="col-md-4">
              <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:80px">Company:</div>
                </div>
                &nbsp;<select name="company_code" class="autocomplete" id="company_code"  style="max-width:150px" required>
                   <option value="-1" >--Select--</option>
                       @if ($companies->count())
                           @foreach($companies as $company)
                               <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                           @endforeach
                       @endif
                   </select>
               </div>
          </div>
           
     </div>
   </form>

  <form id="supp_Form" action="{{route('suppliers.store')}}" method="post">
    {{ csrf_field() }}
    <input type="hidden" class="form-control input-sm" readonly name="result_supplier_id" value="{{ old('result_supplier_id') }}" id="result_supplier_id">
    <input type="hidden" class="form-control input-sm" readonly name="company_id" value="{{ old('company_id') }}" id="company_id">

    <div class="row">
      <!--<div class="col-md-3">-->
      <!--    <div class="input-group">-->
      <!--        <div class="input-group-prepend">-->
      <!--            <div class="input-group-text" style="min-width:80px">Supplier Code:</div>-->
      <!--            <div class="col-xs-10 col-sm-6 @error('supplier_code') has-error @enderror">-->
                      
      <!--                <input type="text" class="form-control" name="supplier_code" id="supplier_code" placeholder="" readonly required>-->
      <!--                <span id="result"></span>-->
      <!--                @error('supplier_code')-->
      <!--                <span class="text-danger">-->
      <!--                  {{ $message }}-->
      <!--                </span>-->
      <!--                @enderror-->
      <!--            </div>-->
      <!--        </div>-->
      <!--   </div>-->
      <!-- </div>-->
       <div class="col-md-4">
         <div class="input-group ss-item-required">
           <select name="acc_head" class="chosen-select" id="supplier_id" onchange="supplier()" required>
             <option value="" disabled selected>- Select Account Head -</option>
             <option value="85"> SUNDRY CREDITORS  </option>
             <!--@foreach($suppliers as $supplier)-->
             <!-- <option {{ old('supplier_id') == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}##{{ $supplier->supp_name }}">{{ $supplier->supp_name }}</option>-->
             <!--@endforeach-->
           </select>
           @error('supplier_id')
           <span class="text-danger">{{ $message }}</span>
           @enderror
         </div>
       </div>
     </div>

    <div class="row">
      <div class="col-md-6">
        <div class="input-group ss-item-required">
          <div class="input-group-prepend ">
            <div class="input-group-text" style="min-width:80px">Name&nbsp;:</div>
          </div>
          <input type="text" name="name" id="name" class="form-control" autocomplete="off" required/>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="input-group ss-item-required">
          <div class="input-group-prepend">
            <div class="input-group-text" style="min-width:80px">Address1:</div>
          </div>
          <textarea name="address1" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500">{{ old('address1') }}</textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="input-group ss-item-required">
          <div class="input-group-prepend">
            <div class="input-group-text" style="min-width:80px">Address2:</div>
          </div>
          <textarea name="address2" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500">{{ old('address2') }}</textarea>
        </div>
      </div>
    </div>
  
        <div class="row">
          <div class="col-md-4">
                 <div class="input-group ss-item-required">
                     <div class="input-group-prepend ">
                         <div class="input-group-text" style="min-width:80px">Mobile&nbsp;No&nbsp;:</div>
                     </div>
                     <input type="text" name="mobile" value="" class="form-control" autocomplete="off"/>
                </div>
             </div>
        </div>
        <div class="row">
          <div class="col-md-4">
                 <div class="input-group ss-item-required">
                     <div class="input-group-prepend ">
                         <div class="input-group-text" style="min-width:80px">Phone&nbsp;No&nbsp;:</div>
                     </div>
                     <input type="text" name="phone" value="" class="form-control" autocomplete="off"/>
                </div>
             </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend">
                  <div class="input-group-text" style="min-width:80px">Email&nbsp;:</div>
                </div>
                <input type="text" name="email" value="" class="form-control" autocomplete="off"/>
              </div>
           </div>
             <div class="col-md-1">
               <button class="btn btn-sm btn-success" type="submit">Save</button>
             </div>
          </div>
          </form>
        </div>
        
<div class="row container">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-view" id="datatable">
        <thead class="thead-blue">
          <th class="text-center" scope="col">ID</th>
          <th class="text-center" scope="col">Name</th>
          <th class="text-center" scope="col">Address1</th>
          <th class="text-center" scope="col">Address2</th>
          <th class="text-center" scope="col">District</th>
          <th class="text-center" scope="col">Mobile</th>
          <th class="text-center" scope="col">Phone</th>
          <th class="text-center" scope="col">Email</th>
          <th class="text-center">Options</th>
        </thead>
        <tbody>
          @foreach($suppliers as $row)
          <tr>
            <td width="10%">AJS-000{{$row->id}}</td>
            <td width="15%">{{ $row->supp_name }}</td>
            <td width="20%">{{ $row->supp_add1 }}</td>
            <td width="20%">{{ $row->supp_add2 }}</td>
            <td width="8%">{{ $row->vCityName }}</td>
            <td width="8%">{{ $row->supp_mobile }}</td>
            <td width="8%">{{ $row->supp_phone }}</td>
            <td width="8%">{{ $row->supp_email }}</td>
            <td width="15%">
              <a><a href="{{ route('suppliers.edit',$row->id) }}" class="btn btn-xs btn-primary edit">EDIT</a>
            </td>
          </tr>
          @endforeach
          </tbody>
        </table>
      </div>
  </div>
  
      </div>
@stop


@section('pagescript')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>

<script type="text/javascript">
  var compcode = $('#company_code').val();
  $('#company_id').val(compcode);

  function supplier() {
      var supplier_id = $('#supplier_id').val();
      value = supplier_id.split("##");
      $('#supplier_code').val(value[0]);
      $('#result_supplier_id').val(value[0]);
      $('#name').val(value[1]);
  }
</script>

<script>
      $(document).ready( function () {
        console.log('datatable')
        $('#datatable').DataTable({ "id": [{ "orderSequence": [ "DESC" ] },] });
    });
</script>


<script type="text/javascript">
  $(document).ready(function() {


  });
</script>
@stop
