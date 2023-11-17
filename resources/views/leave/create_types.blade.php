@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
  <input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>

<div class="title"></div>
  <legend>
<div class="title">
  <div class="widget-header widget-header-small">
    <font size="3" color="blue"><b>Leave Typs Entry Information</b></font>
    <div class="widget-toolbar">
        <a href="{{ route('leave.types.index') }}" class="blue"><i class="fa fa-list"></i>&nbsp;List</a>
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

  <form action="{{route('leave.types.store')}}" method="POST">
  {{ csrf_field() }} 
  <div class="row justify-content-center">
    <div class="col-md-3">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Company&nbsp;:</span>
        </div>
            <select name="company_code" class="autocomplete" id="company_code"  style="max-width:150px" required>
                <option value="" >--Select--</option>
                    @if ($companies->count())
                        @foreach($companies as $company)
                          <option  {{$company_code = $company->comp_id ? 'selected':'' }} value="{{$company->comp_id}}" >{{ $company->comp_id }}-{{ $company->name }}</option>
                        @endforeach
                    @endif
            </select>
      </div>
    </div> 
    </div>
  </div>
  <div class="row justify-content-center">
    <div class="col-md-3">
        <div class="input-group-prepend ">
            <div class="input-group-text" style="min-width:50px">Type:</div>
            <input type="text" name="leave_type" id="leave_type" class="form-control" placeholder="Leave Types" required/>
        </div>
    </div>
    <div class="col-md-4">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Description&nbsp;:</span>
        </div>
        <input type="text" name="leave_desc" id="leave_desc" class="form-control" placeholder="Description" required/>
      </div>
    </div>
    <div class="col-md-2">
      <div class="input-group mb-2">
        <div class="input-group-prepend">
          <span class="input-group-text">Days&nbsp;:</span>
        </div>
        <input type="text" name="leave_days" id="leave_days" class="form-control" placeholder="Days" required/>
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
  <div class="row justify-content-center">
    <div class="col-md-10">
      @csrf
      <table class="table table-striped table-report">
        <thead class="thead-blue">
          <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
           <th class="text-left" scope="col">Company</th>
          <th class="text-center" scope="col">Leave Type</th>
          <th class="text-center" scope="col">Leave Description</th>
          <th class="text-center" scope="col">Days</th> 
          <th class="text-center">Options</th>
        </thead>
        <tbody>
          @foreach($rows as $row)
          <tr>
            <td style=display:none;>{{ $row->id }}</td>
            <td align="left">{{ $row->name }}</td>
            <td align="center">{{ $row->leave_type }}</td>
            <td>{{ $row->leave_desc }}</td>
            <td align="center">{{ $row->days }}</td> 
            <td align="center">
              <form  method="post" action="{{ url('leave-types/destroy/'.$row->id) }}" class="delete_form">
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