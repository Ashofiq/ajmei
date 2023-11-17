@extends('layouts.app')
@section('content')
<section class="content">
<div class="title"><legend>System Information</legend></div>
<div class="container">
<input type="hidden" name="menu_selection" id="menu_selection" value="SYS@1" class="form-control" required>
  <div class="row">
    <div class="col-12">
        @if(Session::has('message'))
           <p class="alert alert-success">{{ Session::get('message') }}</p>
        @endif
    </div>
  </div>

   <form action="{{route('sysinfo.search')}}" method="post">
  {{ csrf_field() }}
   <div class="row">
       <div class="col-md-4">
         <a href="#" data-toggle="modal" data-target="#addModal" class="btn btn-success btn-sm">
         <i class="fa fa-plus"></i>Add New</a>
      </div> 
              <div class="col-md-4">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" style="min-width:100px">Dropdown Type&nbsp;:</span>
                  </div>
                  <select class="form-control m-bot15" name="search_dropdown_type" required>
                    <option value="" >--Select--</option>
                     @if ($dropdowntypes->count())
                         @foreach($allSysinfo as $d)
                          <option value="{{ $d->vComboType  }}" >{{ $d->vComboType }}</option>
                         @endforeach
                     @endif
                 </select>
                </div>
            </div> 
            <div class="col-md-3">
        <button type="submit" name="submit"  class="btn btn-sm btn-info"><span class="fa fa-search"></span></button>
      </div> 
  </div>
  </form>
<br/>
  <div class="row">
          <div class="col-md-12">
             @csrf
             <table class="table table-striped table-view">
              <thead class="thead-dark">
                 <th style="display:none;" class="text-center" scope="col">Sys.ID</th>
                 <th style=display:none; class="text-center" scope="col">Company Id</th>
                 <th class="text-center" scope="col">Company</th>
                 <th class="text-center" scope="col">Type</th>
                 <th class="text-center" scope="col">Name</th>
                 <th class="text-center" scope="col">Description</th>
                 <th class="text-center" scope="col">Level</th>
                 <th class="text-center" scope="col">Attn Bonus</th>
                 <th class="text-center" scope="col">Options</th>
               </thead>
               <tbody>
                 @foreach($rows as $row)
                 <tr>
                   <td style=display:none;>{{ $row->id }}</td>
                   <td style=display:none;>{{ $row->combo_company_id }}</td>
                   <td>{{ $row->comp_name }}</td>
                   <td>{{ $row->vComboType }}</td>
                   <td>{{ $row->vComboName }}</td>
                   <td>{{ $row->vComboDesc }}</td>
                   <td class="text-center">{{ $row->level }}</td>
                   <td class="text-center">{{ $row->attn_bonus }}</td>
                    <td>
                      <form  method="post" action="{{ url('/dropdowns/destroy/'.$row->id) }}" class="delete_form">
                        {{ csrf_field() }}
                        {{ method_field('DELETE') }}
                        <div class="btn-group btn-corner">
                          <a href="#" class="btn btn-xs btn-primary edit">Edit</a>
                          @if($row->vComboType != $row->vComboName)
                          <button class='btn btn-danger btn-sm delete'  type="submit" onclick="return confirm('Are You Sure? Want to Delete It.');">Delete</button>
                          @endif
                        </div>
                      </form>
                  </td>
                 </tr>
                 @endforeach
               </tbody>
          </table>

      </div>
  </div>



</div>
</section>

<!-- Start Add Modal -->
<div class="modal fade" id="addModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Add System Information</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <br/>
      <form action="{{route('sysinfo.store')}}" method="POST" enctype="multipart/form-data">
        {{ csrf_field() }}
        <div class="modal-body">

        <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="min-width:100px">Company Code&nbsp;:</span>
                </div>
                <select class="form-control m-bot15" name="company_code" required>
                  <option value="" >--Select--</option>
                   @if ($companies->count())
                       @foreach($companies as $company)
                           <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id  }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                       @endforeach
                   @endif
               </select>
              </div>
          </div>
        </div>
        <div class="row">
              <div class="col-md-8">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" style="min-width:100px">Dropdown Type&nbsp;:</span>
                  </div>
                  <select class="form-control m-bot15" name="dropdown_type" required>
                    <option value="" >--Select--</option>
                     @if ($dropdowntypes->count())
                         @foreach($allSysinfo as $d)
                             <option value="{{ $d->vComboType  }}" >{{ $d->vComboType }}</option>
                         @endforeach
                     @endif
                 </select>
                </div>
            </div>
        </div>

         <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text" style="min-width:100px">Name&nbsp;:</span>
                 </div>
                 <input type="text" name="dropdown_name"  class="form-control" placeholder="">
               </div>
   	       </div>
          </div>

         <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="min-width:100px">Description&nbsp;:</span>
                </div>
                <textarea class="form-control" name="description" rows="3"></textarea>
              </div>
            </div>
          </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text" style="min-width:100px">Level&nbsp;:</span>
               </div>
               <input type="text" name="level" class="form-control" placeholder="">
             </div>
           </div>
         </div>

         <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text" style="min-width:100px">Attendance Bonus&nbsp;:</span>
               </div>
               <input type="text" id="attn_bonus" name="attn_bonus" class="form-control" placeholder="Attendance Bonus">
             </div>
           </div>
         </div>

        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </div>
      </form>

    </div>
  </div>
</div>
<!-- End Add Modal -->

<!--- Start Edit Modal -->
<div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">	<b>System Information</b></h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <form action="{{ route('sysinfo.update') }}" method="post" id="editForm" enctype="multipart/form-data">
        {{ csrf_field() }}
         {{ method_field('POST') }}
        <input type="text" name="id" id="id" class="form-control" readonly="true" />
        <br/>
        <div class="modal-body">

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text">Company Name&nbsp;:</span>
                 </div>
                 <select class="form-control m-bot15" name="company_code" id="company_code"  required>
                   <option value="" >--Select--</option>
                    @if ($companies->count())
                        @foreach($companies as $company)
                            <option {{ $company_code == $company->comp_id ? 'selected' : '' }} value="{{ $company->comp_id }}" >{{ $company->comp_id }}--{{ $company->name }}</option>
                        @endforeach
                    @endif
                </select>
               </div>
   	       </div>
          </div>

          <div class="row">
                <div class="col-md-8">
                  <div class="input-group mb-3">
                    <div class="input-group-prepend">
                      <span class="input-group-text" style="min-width:100px">Dropdown Type&nbsp;:</span>
                    </div>
                    <select class="form-control m-bot15" name="dropdown_type" id="dropdown_type"  required>
                      <option value="" >--Select--</option>
                       @if ($dropdowntypes->count())
                           @foreach($allSysinfo as $d)
                           <option value="{{ $d->vComboType  }}" >{{ $d->vComboType }}</option>
                           @endforeach
                       @endif
                   </select>
                  </div>
              </div>
          </div>

          <div class="row">
              <div class="col-md-8">
                <div class="input-group mb-3">
                  <div class="input-group-prepend">
                    <span class="input-group-text" style="min-width:100px">Name&nbsp;:</span>
                  </div>
                  <input type="text" name="dropdown_name"  id="dropdown_name" class="form-control" placeholder="">
                </div>
    	       </div>
           </div>

          <div class="row">
             <div class="col-md-8">
               <div class="input-group mb-3">
                 <div class="input-group-prepend">
                   <span class="input-group-text" style="min-width:100px">Description&nbsp;:</span>
                 </div>
                 <textarea class="form-control" name="description" id="description"  rows="3" required></textarea>
               </div>
             </div>
           </div>

           <div class="row">
            <div class="col-md-8">
              <div class="input-group mb-3">
                <div class="input-group-prepend">
                  <span class="input-group-text" style="min-width:100px">Level&nbsp;:</span>
                </div>
                <input type="text" name="level" id="level"  class="form-control" placeholder="">
              </div>
            </div>
          </div>

          <div class="row">
           <div class="col-md-8">
             <div class="input-group mb-3">
               <div class="input-group-prepend">
                 <span class="input-group-text" style="min-width:100px">Attendance Bonus&nbsp;:</span>
               </div>
               <input type="text" id="attn_bonus" name="attn_bonus" class="form-control" placeholder="Attendance Bonus">
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

<script type="text/javascript">
  $(document).ready(function() {

    $('.edit').on('click',function() {
        $tr = $(this).closest('tr');
        var data = $tr.children("td").map(function(){
          return $(this).text();
        }).get();
        console.log(data);

        //alert('HELLO :1:'+data[1]+':2:'+ data[2]+':3:'+data[3] + ':4:'+ data[4]+':5:'+data[5] +':6:'+data[6]+':7:'+data[7]+':8:'+ data[8]+':Nine:'+data[9]+':ten:'+data[10]+':eleven:'+ data[11]+':12:'+ data[14]);
        $('#id').val(data[0]);
        $('#company_code').val(data[1]);
        $('#dropdown_type').val(data[3]);
        $('#dropdown_name').val(data[4]);
        $('#description').val(data[5]);
        $('#level').val(data[6]);

        $('#editForm').attr('action','{{route("sysinfo.update")}}');
        $('#editModal').modal('show');
    });
  });
</script>

<script>
      // $(".send").click(function(event){
      //   event.preventDefault();
      //   console.log();
      //   $.ajax({
      //     type:'POST',
      //     url: '{{ url('/') }}/sysinfo-update',
      //     data: productInfoObj,
      //     success:function(data) {
      //       console.log(data);
      //         Swal.fire({
      //             position: 'top-end',
      //             icon: 'success',
      //             title: 'Product Added Successfully',
      //             showConfirmButton: false,
      //             timer: 1500
      //         })
              
      //         $('input[type="text"]').val();
      //     },
      //     error: function(e){
      //         console.log(e);
      //     }
      //   });
      // });

</script>

@stop
