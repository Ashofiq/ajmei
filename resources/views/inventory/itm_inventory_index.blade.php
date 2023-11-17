@extends('layouts.app')
@section('css')

    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<!-- Start Add Modal -->
  @include('inc.showAccModal')
<!-- End Add Modal -->
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="MM@1" class="form-control" required>
  <div class="title">
    <legend>
    <div class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
          <font size="3" color="blue"><b>Purchase List</b></font>
        </h6>
       <div class="widget-toolbar">
         <a href="{{route('itm.inventory.index')}}" class="blue"><i class="fa fa-list"></i> List</a>
        </div>
    </div></legend>
  </div>
<div class="container">

  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>
  <form action="{{route('itm.inventory.index')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-2">
         <a href="{{route('itm.inventory.create')}}" class="btn btn-success btn-sm">
                  <i class="fa fa-plus"></i>Add New</a>
      </div>
      <div class="col-md-3">
          <div class="input-group ss-item-required">
            <select name="supplier_id" id="supplier_id" class="chosen-select"  onchange="getSupplierDetails(this.value)">
                <option value="" disabled selected>- Select Supplier -</option>
                @foreach($suppliers as $supplier)
                    <option {{ old('supplier_id') == $supplier->id ? 'selected' : '' }} value="{{ $supplier->id }}">{{ $supplier->supp_name }}</option>
                @endforeach
            </select>
            @error('supplier_id')
            <span class="text-danger">{{ $message }}</span>
            @enderror
            </div>
        </div>
         <div class="col-md-2">
          <input type="text" name="pur_order_no" id="pur_order_no" value="{{old('pur_order_no')}}" class="form-control" placeholder="Enter Purchase No"/>
        </div>
      <div class="col-md-2">
          <div class="form-group">
            <input type="text" size = "9" name="fromdate" onclick="displayDatePicker('fromdate');"  value="{{ date('d-m-Y',strtotime($fromdate)) }}" />
            <a href="javascript:void(0);" onclick="displayDatePicker('fromdate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
          </div>
      </div>
      <div class="col-md-2">
          <div class="form-group">
            <input type="text" size = "9" name="todate" onclick="displayDatePicker('todate');"  value="{{ date('d-m-Y',strtotime($todate)) }}" />
            <a href="javascript:void(0);" onclick="displayDatePicker('todate');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>
          </div>
      </div>
     <div class="col-md-1">
       <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search">Search</span></button>
     </div>
 </div>
</form>
<br/>

<div class="row justify-content-center">
    <div class="col-md-12">
      <table class="table table-striped table-data table-view">
        <thead class="thead-dark">
          <tr>
            <th>Id</th>
            <th>PO&nbsp;No</th>
            <th>Purchase&nbsp;Date</th>
            <th>PI&nbsp;No</th><th>Sysno</th>
            <th>Supplier</th>
            <th>Comments</th>
            <th>Total Qty</th>
            <th>Total Amount</th>
            <th>Options</th>
          </tr>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td>{{ $row->id }}</td>
            <td>{{ $row->pur_order_ref }}</td>
            <td>{{ date('d-m-Y',strtotime($row->pur_order_date)) }}</td>
            <td>{{ $row->pur_pi_no }}</td>
            <td>{{ $row->pur_order_no }}</td>
            <td>{{ $row->supp_name }}</td>
            <td>{{ $row->pur_comments }}</td>
            <td align="right">{{ number_format($row->pur_total_qty,2)}}</td>
            <td align="right">{{ number_format($row->pur_total_amount,2)}}</td>
            <td>
                <form  method="post" action="{{ url('/itm-inventory/destroy/'.$row->id.'/'.$row->pur_order_no) }}" class="delete_form">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <div class="btn-group btn-corner">
                  <span href="{{ url('/') }}/itm-inventory/item-m-view/{{$row->id}}" data-toggle="modal" data-id="{{$row->id}}"
                      class="btn btn-sm btn-info viewModal" title="View Details" data-placement="top" >
                     View
                  </span>
                  <a href="{{ url('/') }}/itm-inventory-edit/{{$row->id}}/{{$row->pur_order_refid}}" class="btn btn-xs">Edit</a>
                  <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
                  <a href="{{ route('purchase.voucher.pdf',$row->id) }}" class="btn btn-xs btn-info" title="Invoice" target="_blank">Purchase</a>
                  <span href="{{ url('/') }}/pur-invoice/acc-m-view/{{$row->pur_order_no}}" data-toggle="modal" data-id="{{$row->pur_order_no}}" class="btn btn-sm btn-success viewModal" title="View">
                      Acc.Doc
                  </span>
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
      // show modal
      $('.viewModal').click(function(event) {
          event.preventDefault();

          var url = $(this).attr('href');
        //  alert(url);
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
