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
    <font size="3" color="blue"><b>Customer Price Information</b></font>
    <div class="widget-toolbar">
        <a href="{{ route('cust.index') }}" class="blue"><i class="fa fa-list"></i>&nbsp;List</a>
    </div>
    <div class="widget-toolbar">
        <a href="{{ url('/cust-price-index/'.$id.'/'.$comp) }}" class="blue"><i class="fa fa-arrow-left"></i>&nbsp;Back</a>
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

  <form action="{{route('cust.price.store')}}" method="POST">
  {{ csrf_field() }}
  <input type="hidden" name="cust_id" id="cust_id" value="{{$id}}" class="form-control" readonly required/>
  <div class="row justify-content-center">
    <div class="col-md-3">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Company&nbsp;:</span>
        </div>
        <input type="hidden" name="comp_id" id="comp_id" value="{{$comp}}" class="form-control" readonly required/>
        <input type="text" name="company" id="company" value="{{$company_name}}" class="form-control" readonly required/>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Customer Code&nbsp;:</span>
        </div>
        <input type="text" name="id" id="id" value="{{$id}}" class="form-control" readonly required/>
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
    <div class="col-md-4">
        <div class="input-group-prepend ">
            <div class="input-group-text" style="min-width:50px">Item:</div>
                <select name="item_id" class="col-xs-10 col-sm-8 chosen-select" id="item_id" onchange="item()" required>
                    <option value="" disabled selected>- Select Item -</option>
                    @foreach($item_pend_list as $list)
                        <option {{ old('item_id') == $list->id ? 'selected' : '' }} value="{{ $list->id }}">{{ $list->item_name }}-{{ $list->itm_cat_name }}</option>
                    @endforeach
                </select>
                @error('item_id')
                <span class="text-danger">{{ $message }}</span>
                @enderror
        </div>
    </div>
    <div class="col-md-2">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Price&nbsp;:</span>
        </div>
        <input type="text" name="itm_price" id="itm_price" class="form-control" placeholder="Item price" required/>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Comm&nbsp;:</span>
        </div>
        <input type="text" name="itm_comm" id="itm_comm" class="form-control" placeholder="Commission"/>
      </div>
    </div>
     <div class="col-md-2.5">
       <div class="input-group mb-1">
         <div class="input-group-prepend">
           <span class="input-group-text">Valid&nbsp;:</span>
         </div>
         <input type="date" name="fromdate" id="fromdate" value="{{old('fromdate')}}" class="form-control" placeholder="From Date" required/>
       </div>
     </div>
     <div class="col-md-2.5">
       <div class="input-group mb-1">
         <div class="input-group-prepend">
           <span class="input-group-text">To&nbsp;:</span>
         </div>
         <input type="date" name="todate" id="todate" value="{{old('todate')}}" class="form-control" placeholder="To Date" required/>
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
<form action="{{route('cust.price.search')}}" method="POST">
{{ csrf_field() }}
<input type="hidden" name="s_comp_id" id="s_comp_id" value="{{$comp}}" class="form-control" readonly required/>
<input type="hidden" name="s_cust_id" id="s_cust_id" value="{{$id}}" class="form-control" readonly required/>

<div class="row justify-content-center">
  <div class="col-md-4">
      <div class="input-group-prepend ">
          <div class="input-group-text" style="min-width:50px">Item:</div>
              <select name="s_item_id" class="col-xs-10 col-sm-8 chosen-select" id="s_item_id" >
                  <option value="" disabled selected>- Select Item -</option>
                  @foreach($item_list as $list)
                      <option {{ old('s_item_id') == $list->id ? 'selected' : '' }} value="{{ $list->id }}">{{ $list->item_name }}-{{ $list->itm_cat_name }}</option>
                  @endforeach
              </select>
              @error('s_item_id')
              <span class="text-danger">{{ $message }}</span>
              @enderror
      </div>
  </div>
  <div class="col-md-1">
    <button type="submit" class="btn btn-sm btn-info" title="Search">Search</button>
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
          <th class="text-center" scope="col">Item Code</th>
          <th class="text-center" scope="col">Item Name</th>
          <th class="text-center" scope="col">Price</th>
          <th class="text-center" scope="col">Commission</th>
          <th class="text-center" scope="col">Valid</th>
          <th class="text-center">To</th>
          <th class="text-center">Flag</th>
          <th class="text-center">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td>{{ $row->item_code }}</td>
            <td>{{ $row->item_name }} ({{ $row->itm_cat_name }})</td>
            <td>{{ $row->cust_price }}</td>
            <td>{{ $row->cust_comm }}</td>
            <td>{{date('d-m-Y',strtotime($row->p_valid_from))}}</td>
            <td>{{date('d-m-Y',strtotime($row->p_valid_to))}}</td>
            <td align="center">{{ $row->p_del_flag == 1 ? 'Y':'' }} </td>
            <td align="center">
              <form  method="post" action="{{ url('cust/price/destroy/'.$row->id.'/'.$id.'/'.$comp) }}" class="delete_form">
                {{ csrf_field() }}
                {{ method_field('DELETE') }}
                <div class="btn-group btn-corner">
                <span href="#"  data-toggle="modal" class="btn btn-sm btn-success edit" title="Edit">
                 Edit</span>
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



<!--- Start Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">	<b>Edit:: Price Master</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
         {{ method_field('POST') }}
        <input type="text" name="update_id" id="update_id" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">

        <div class="row">
          <div class="col-md-2">
                   <div class="input-group mb-3">
                     <div class="input-group-prepend">
                       <span class="input-group-text">Company&nbsp;:</span>
                     </div>
                     <input type="text" name="company_id" id="company_id" class="form-control" autocomplete="off" required readonly/>
                   </div>
       	    </div>
            <div class="col-md-4">
                     <div class="input-group mb-3">
                       <input type="text" name="company_name" id="company_name" class="form-control" autocomplete="off" required readonly/>
                     </div>
         	    </div>
        </div>
        <div class="row">
          <div class="col-md-2">
                   <div class="input-group mb-3">
                     <div class="input-group-prepend">
                       <span class="input-group-text">Customer&nbsp;:</span>
                     </div>
                     <input type="text" name="customer_code" id="customer_code" class="form-control" autocomplete="off" required readonly/>
                   </div>
       	    </div>
            <div class="col-md-4">
                     <div class="input-group mb-3">
                       <input type="text" name="customer_name" id="customer_name" class="form-control" autocomplete="off" required readonly/>
                     </div>
         	    </div>
        </div>
          <div class="row">
                 <div class="col-md-8">
                   <div class="input-group mb-3">
                     <div class="input-group-prepend">
                       <span class="input-group-text">Item Code&nbsp;:</span>
                     </div>
                     <input type="text" name="itm_code" id="itm_code" class="form-control" autocomplete="off" required readonly/>
                   </div>
       	       </div>
          </div>

         <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Item Name&nbsp;:</span>
                 </div>
                 <input type="text" name="itm_name" id="itm_name" class="form-control" required readonly/>

               </div>
   	       </div>
          </div>

          <div class="row">
              <div class="col-md-8">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text">Item Price&nbsp;:</span>
                  </div>
                  <input type="text" name="itm_u_price" id="itm_u_price" class="form-control" required/>

                </div>
            </div>
           </div>
           
          <div class="row">
               <div class="col-md-8">
                 <div class="input-group mb-3">
                   <div class="input-group-prepend">
                     <span class="input-group-text">Commission&nbsp;:</span>
                   </div>
                   <input type="text" name="itm_u_comm" id="itm_u_comm" class="form-control"/>

                 </div>
             </div>
            </div>

         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Vaid&nbsp;:</span>
                </div>
                 <input type="text" size = "15" name="valid_from" id="valid_from" onclick="displayDatePicker('valid_from');" required />
                 <a href="javascript:void(0);" onclick="displayDatePicker('valid_from');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>

              </div>
            </div>
          </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Vaid&nbsp;:</span>
                 </div>
                  <input type="text" size = "15" name="valid_to" id="valid_to" onclick="displayDatePicker('valid_to');" required />
                  <a href="javascript:void(0);" onclick="displayDatePicker('valid_to');"><img src="{{ asset('assets/images/calendar.png') }}" alt="calendar" border="0"></a>

               </div>
             </div>
           </div>

        </div>
         <div class="modal-footer">
           <button type="submit" class="btn btn-primary">Update</button>
           <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
         </div>
       </form>

    </div>
  </div>
</div>
<!--- End Edit Modal -->

@stop


@section('pagescript')
<script src="{{ asset('assets/js/jquery-ui.min.js') }}"></script>
<script src="{{ asset('assets/js/chosen.jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/ace-elements.min.js') }}"></script>
<script src="{{ asset('assets/js/ace.min.js') }}"></script>
<script src="{{ asset('assets/blogic_js/sel_box_search.js') }}"></script>

<script type="text/javascript">
  $(document).ready(function() {

    $('.edit').on('click',function() {
        $tr = $(this).closest('tr');
        var data = $tr.children("td").map(function(){
          return $(this).text();
        }).get();
        console.log(data);

        $('#company_id').val($('#comp_id').val());
        $('#company_name').val($('#company').val());
        $('#customer_code').val($('#id').val());
        $('#customer_name').val($('#cust_name').val());

        //alert('HELLO :1:'+data[1]+':2:'+ data[2]+':3:'+data[3] + ':4:'+ data[4]+':5:'+data[5] +':6:'+data[6]+':7:'+data[7]+':8:'+ data[8]+':Nine:'+data[9]+':ten:'+data[10]+':eleven:'+ data[11]+':12:'+ data[14]);
        $('#update_id').val(data[0]);
        $('#itm_code').val(data[1]);
        $('#itm_name').val(data[2]);
        $('#itm_u_price').val(data[3]);
        $('#itm_u_comm').val(data[4]);
        $('#valid_from').val(data[5]);
        $('#valid_to').val(data[6]);

        $('#editForm').attr('action','{{route("cust.price.update")}}');
        $('#editModal').modal('show');
    });

  });
</script>
@stop
