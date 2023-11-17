@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>
<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b>Customer Delivery Information</b></font>
    <div class="widget-toolbar">
        <a href="{{ route('cust.delv.index',$id) }}" class="blue"><i class="fa fa-list"></i> List</a>
    </div>
    <div class="widget-toolbar">
        <a href="{{ route('cust.index') }}" class="blue"><i class="fa fa-arrow-left"></i> Back</a>
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

  <form action="{{route('cust.delv.store')}}" method="POST">
  {{ csrf_field() }}
  <input type="hidden" name="cust_id" id="cust_id" value="{{$id}}" class="form-control" readonly required/>
  <div class="row">
    <div class="col-md-3">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Customer Code&nbsp;:</span>
        </div>
        <input type="text" name="cust_code" id="cust_code" value="{{$cust_code}}" class="form-control" readonly required/>
      </div>
    </div>
    <div class="col-md-6">
     <div class="input-group mb-2">
       <div class="input-group-prepend">
         <span class="input-group-text">Customer Name&nbsp;:</span>
       </div>
       <input type="text" name="cust_name" id="cust_name" value="{{$cust_name}}" class="form-control" readonly required/>
    </div>
    </div>
  </div>
  <div class="row justify-content-center">
     <div class="col-md-3">
         <div class="form-group">
           <input type="text" name="deliveryto" id="deliveryto" class="form-control" placeholder="Delivery To" required/>
        </div>
     </div>
     <div class="col-md-4">
              <div class="input-group ss-item-required">
                  <textarea name="address" rows="2" cols="100" class="form-control config" placeholder="Narration" maxlength="500" required></textarea>
              </div>
      </div>
     <div class="col-md-2">
         <div class="form-group">
           <input type="text" name="mobile" id="mobile" class="form-control" placeholder="Mobile"/>
        </div>
     </div>
     <div class="col-md-2">
        <div class="input-group ss-item-required">
            <select name="district_id" class="chosen-select" id="district_id"  style="max-width:250px" required>
            <option value="-1" >--District--</option>
                @if ($dist_list->count())
                    @foreach($dist_list as $cmb)
                        <option {{ old('district_id') == $cmb->id ? 'selected' : '' }} value="{{$cmb->id}}" >{{ $cmb->vCityName }}</option>
                    @endforeach
                @endif
            </select>
       </div>
     </div>

      <div class="col-md-1">
        <button type="submit" class="btn btn-sm btn-primary" onclick="return confirm('Are You Sure? Want to Save It.');"
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
          <th class="text-center" scope="col">Delivered To</th>
          <th class="text-center" scope="col">Address</th>
          <th class="text-center" scope="col">Mobile</th>
          <th class="text-center" scope="col">Distict</th>
          <th class="text-center" colspan="2">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->deliv_to }}</td>
            <td>{{ $row->deliv_add }}</td>
              <td>{{ $row->deliv_mobile }}</td>
            <td>{{ $row->vCityName }}</td>
            <td>
              <form  method="post" action="{{ url('/cust/delv/destroy/'.$row->id) }}" class="delete_form">
              {{ csrf_field() }}
              {{ method_field('DELETE') }}
              <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
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
            {{ $rows->render("pagination::bootstrap-4") }} 
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
</script>

@stop
