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
    <font size="3" color="blue"><b>Currency Information</b></font>
    <div class="widget-toolbar">
        <a href="{{ route('acctrans.currency') }}" class="blue"><i class="fa fa-list"></i>&nbsp;List</a>
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

  <form action="{{route('currency.store')}}" method="POST">
  {{ csrf_field() }}
  <div class="row justify-content-left">
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

  <div class="row justify-content-left">
    <div class="col-md-3">
      <div class="input-group">
        <select name="acc_currency" class="col-xs-10 col-sm-8 chosen-select" id="acc_name_id" onchange="getAccountId(this.value)" required>
          <option value="" disabled selected>- Select Currency -</option>
          <option value="USD">USD</option>
        </select>
        @error('acc_name_id')
        <span class="text-danger">{{ $message }}</span>
        @enderror
      </div>
    </div>
    <div class="col-md-3">
      <div class="input-group ss-item-required">
        <div class="input-group-prepend">
          <div class="input-group-text" style="min-width:80px">Value:</div>
        </div>
        <input type="text" name="acc_currency_val" id="acc_currency_val" value="{{old('acc_currency_val')}}" class="form-control" required />
      </div>
    </div>

    <div class="col-md-1">
        <button type="submit" class="btn btn-sm btn-info" onclick="return confirm('Are You Sure? Want to Save It.');"
               title="Save">Save
        </button>
    </div>
   </div>
   </form>
<br/>
  <div class="row">
    <div class="col-md-12">
      @csrf
      <table class="table table-striped table-report">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
          <th class="text-center" scope="col">Currency</th>
          <th class="text-center" scope="col">Value</th>
          <th class="text-center">Options</th>
        </thead>
        <tbody>
           @foreach($rows as $row)
           <tr>
           <td style=display:none;>{{ $row->id }}</td>
           <td>{{ $row->vCurrName }}</td>
           <td>{{ $row->dCurrValue }}</td>
           <td align="center">
             <form  method="post" action="{{ url('/currency/destroy/'.$row->id) }}" class="delete_form">
               {{ csrf_field() }}
               {{ method_field('DELETE') }}
               <div class="btn-group btn-corner">
               <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
               </div>
             </form>
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

          </p>
        </ul>
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

  function getAccountId(acc_id) {
      $('#result_acc_name_id').val(acc_id);
  }

  function getItemCatId(cat_id) {
      $('#result_itm_cat_id').val(cat_id);
  }

  function getDropdownAccountList(acchead_id){
    var custid = $('#company_code').val();
    //alert(acchead_id);
    $.get('{{ url('/') }}/accountNameLookup/' + acchead_id, function(response) {
      var selectList = $('select[id="acc_name_id"]');
      selectList.chosen();
      selectList.empty();
      selectList.append('<option value="">--Select Account Name--</option>');
      $.each(response, function(index, element) {
        //alert(element.id + "," + element.acc_head);
        selectList.append('<option value="' + element.id +'@@'+ element.acc_head + '">' + element.acc_head + '</option>');
      });
      selectList.trigger('chosen:updated');
    });

  }
</script>

@stop
