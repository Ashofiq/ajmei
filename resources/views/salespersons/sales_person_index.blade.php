@extends('layouts.app')
@section('css')
    <link rel="stylesheet" href="{{ asset('assets/css/jquery-ui.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/chosen.min.css') }}" />
@stop
@section('content')
<section class="content">
<input type="hidden" name="menu_selection" id="menu_selection" value="SD@1" class="form-control" required>

  <div class="title">
    <legend>
    <div class="widget-header widget-header-small">
        <h6 class="widget-title smaller">
          <font size="3" color="blue"><b>Sales Person Entry Form</b></font>
        </h6>
       <div class="widget-toolbar">
         <a href="#" class="blue"><i class="fa fa-list"></i> List</a>
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
 <form id="itm_Form" action="{{route('sales.persons.store')}}" method="post">
    {{ csrf_field() }}
    <div class="widget-body">
      <div class="widget-main">
        <div class="row">
         <div class="col-md-4">
              <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text" style="min-width:110px">Company:</div>
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

    <div class="row">
          <div class="col-md-4">
              <div class="input-group ss-item-required">
                  <div class="input-group-prepend ">
                      <div class="input-group-text" style="min-width:80px">Name&nbsp;:</div>
                  </div>
                  <input type="text" name="name" value="" class="form-control" autocomplete="off" required/>
             </div>
          </div>
      </div>

      <div class="row">
            <div class="col-md-4">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="min-width:80px">Designation&nbsp;:</span>
                </div>
                <select class="form-control m-bot15" name="designation" required>
                  <option value="" >--Select--</option>
                   @if ($sysinfos->count())
                       @foreach($sysinfos as $d)
                           <option {{ old('designation') == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->vComboName }}</option>
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
                       <div class="input-group-text" style="min-width:80px">Mobile&nbsp;:</div>
                   </div>
                   <input type="text" name="mobile" value="" class="form-control" autocomplete="off"/>
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
             <button class="btn btn-sm btn-success" type="submit"><i class="fa fa-save"></i> Add</button>
           </div>
        </div>
      </div>
    </div>
    </div>
  </form>
<div class="container">
  <div class="row justify-content-center">
      <div class="col-md-12">
      @csrf
        <table class="table table-striped table-data table-report">
          <thead class="thead-dark">
            <tr>
              <th>Id</th>
              <th>Sales Person</th>
              <th>Designation id</th>
              <th>Designation</th>
              <th>Mobile</th>
              <th>Email</th>
              <th>Options</th>
            </tr>
          </thead>
          <tbody>
            @foreach($rows as $row)
            <tr>
              <td>{{ $row->id }}</td>
              <td>{{ $row->sales_name }}</td>
              <td>{{ $row->sales_desig }}</td>
              <td>{{ $row->vComboName }}</td>
              <td>{{ $row->sales_mobile }}</td>
              <td>{{ $row->sales_email }}</td>
            <td>
                 <form  method="post" action="{{ url('/sales-persons/destroy/'.$row->id) }}" class="delete_form">
                   {{ csrf_field() }}
                   {{ method_field('DELETE') }}
                   <span href="#"  data-toggle="modal" class="btn btn-sm btn-success edit" title="Edit">
                        Edit
                    </span>
                   <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');"><i class="fa fa-pencil-delete-o">Delete</i></button>
                </form>
             </td>
            </tr>
            @endforeach

            </tbody>
          </table>

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

<!--- Start Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">	<b>Edit:: Item Master</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="#" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
         {{ method_field('POST') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">
         <div class="row">
             <div class="col-md-6">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Name&nbsp;:</span>
                 </div>
                 <input type="text" name="name" id="name" class="form-control" required/>

               </div>
   	       </div>
          </div>

          <div class="row">
                <div class="col-md-6">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" style="min-width:80px">Designation&nbsp;:</span>
                    </div>
                    <select class="form-control m-bot15" name="designation" id="designation_id" required>
                      <option value="" >--Select--</option>
                       @if ($sysinfos->count())
                           @foreach($sysinfos as $d)
                               <option {{ old('designation') == $d->id ? 'selected' : '' }} value="{{ $d->id  }}" >{{ $d->vComboName }}</option>
                           @endforeach
                       @endif
                   </select>
                  </div>
              </div>
          </div>

         <div class="row">
            <div class="col-md-6">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text">Mobile&nbsp;:</span>
                </div>
                 <input type="text" name="mobile" id="mobile" class="form-control"/>

              </div>
            </div>
          </div>

          <div class="row">
             <div class="col-md-6">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Email&nbsp;:</span>
                 </div>
                  <input type="text" name="email" id="email" class="form-control" autocomplete="off"/>

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

        //alert('HELLO :1: '+data[1]+' :2: '+ data[2]+' :3: '+data[3] + ' :4: '+ data[4]+' :5: '+data[5] );
        $('#id').val(data[0]);
        $('#name').val(data[1]);
        $('#designation_id').val(data[2]);
        $('#mobile').val(data[4]);
        $('#email').val(data[5]);

        $('#editForm').attr('action','{{route("sales.persons.update")}}');
        $('#editModal').modal('show');
    });

  });
</script>
@stop
