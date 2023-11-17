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
          <font size="3" color="blue"><b>Suppliers Edit Form</b></font>
        </h6>
       <div class="widget-toolbar">
         <a href="{{route('suppliers.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
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

  <form id="supp_Form" action="{{route('suppliers.update')}}" method="post">
    {{ csrf_field() }}
    <input type="hidden" class="form-control input-sm" readonly name="result_supplier_id" value="{{ $rows->id }}" id="result_supplier_id">
    <input type="hidden" class="form-control input-sm" readonly name="company_id" value="{{ $rows->supp_com_id }}" id="company_id">

    <div class="row">
      <div class="col-md-6">
        <div class="input-group ss-item-required">
          <div class="input-group-prepend ">
            <div class="input-group-text" style="min-width:80px">Name&nbsp;:</div>
          </div>
          <input type="text" name="name" id="name" value="{{ $rows->supp_name }}" class="form-control" autocomplete="off" required/>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="input-group ss-item-required">
          <div class="input-group-prepend">
            <div class="input-group-text" style="min-width:80px">Address1:</div>
          </div>
          <textarea name="address1" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500" required>{{ $rows->supp_add1 }}</textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="input-group ss-item-required">
          <div class="input-group-prepend">
            <div class="input-group-text" style="min-width:80px">Address2:</div>
          </div>
          <textarea name="address2" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500">{{ $rows->supp_add2 }}</textarea>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-6">
        <div class="input-group ss-item-required">
          <!--div class="input-group-prepend">
          <div class="input-group-text" style="min-width:80px">District:</div>
        </div -->
        <select name="district_id" class="chosen-select" id="district_id"  required>
          <option value="-1" >--Select District--</option>
          @if ($dist_list->count())
            @foreach($dist_list as $cmb)
              <option {{ $rows->supp_dist_id == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->vCityName }}</option>
            @endforeach
          @endif
        </select>
      </div>
    </div>
   </div>
        <div class="row">
          <div class="col-md-4">
                 <div class="input-group ss-item-required">
                     <div class="input-group-prepend ">
                         <div class="input-group-text" style="min-width:80px">Mobile&nbsp;No&nbsp;:</div>
                     </div>
                     <input type="text" name="mobile" value="{{$rows->supp_mobile}}" class="form-control" autocomplete="off"/>
                </div>
             </div>
        </div>
        <div class="row">
          <div class="col-md-4">
                 <div class="input-group ss-item-required">
                     <div class="input-group-prepend ">
                         <div class="input-group-text" style="min-width:80px">Phone&nbsp;No&nbsp;:</div>
                     </div>
                     <input type="text" name="phone" value="{{$rows->supp_phone}}" class="form-control" autocomplete="off"/>
                </div>
             </div>
        </div>
        <div class="row">
          <div class="col-md-4">
            <div class="input-group ss-item-required">
                <div class="input-group-prepend">
                  <div class="input-group-text" style="min-width:80px">Email&nbsp;:</div>
                </div>
                <input type="text" name="email" value="{{$rows->supp_email}}" class="form-control" autocomplete="off"/>
              </div>
           </div>
             <div class="col-md-1">
               <button class="btn btn-sm btn-success" type="submit">Update</button>
             </div>
          </div>
          </form>
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

<script type="text/javascript">
  $(document).ready(function() {


  });
</script>
@stop
