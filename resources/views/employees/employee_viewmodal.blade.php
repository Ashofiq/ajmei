@extends('layouts.app_m')
@section('content')
<div class="row">
<input type="hidden" name="menu_selection" id="menu_selection" value="HRM@1" class="form-control" required>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Employee Id:</dt><dd>&nbsp;{{ $rows_m->emp_id_no }}</dd>
      <dt>UID No Old:</dt><dd>&nbsp;{{ $rows_m->emp_uid_no_old }}</dd>
      <dt>Employee Name:</dt><dd>&nbsp;{{ $rows_m->emp_name }}</dd> 
      <dt>Designation:</dt><dd>&nbsp;{{ $rows_m->designation }}</dd>
      <dt>Department:</dt><dd>&nbsp;{{ $rows_m->department }}</dd>
      <dt>Section:</dt><dd>&nbsp;{{ $rows_m->section }}</dd>
      <dt>Sec. Category:</dt><dd>&nbsp;{{ $rows_m->sec_category }}</dd>
      <dt>Type:</dt><dd>&nbsp;{{ $rows_m->type }}</dd>
      <dt>Shift:</dt><dd>&nbsp;{{ $rows_m->shift }}</dd>
      <dt>Pay Type:</dt><dd>&nbsp;{{ $rows_m->paytype }}</dd>
      <dt>Category:</dt><dd>&nbsp;{{ $rows_m->cate1 }}</dd>
      <dt>OUT Mark:</dt><dd>&nbsp;{{ $rows_m->outmark }}</dd> 
      <dt>OT Type:</dt><dd>&nbsp;{{ $rows_m->ottype }}</dd>
      <dt>Resign Status:</dt><dd>&nbsp;{{ $rows_m->resign }}</dd>
    </dl>
  </div>
  <div class="col-sm-6">
    <dl id="dt-list-1" class="dl-horizontal">
      <dt>Bank Account No: </dt><dd>&nbsp;{{ $rows_m->emp_bank_acc_no }}</dd>
      <dt>National Id: </dt><dd>&nbsp;{{ $rows_m->emp_national_id }}</dd>
      <dt>Skill Grade: </dt><dd>&nbsp;{{ $rows_m->emp_skill_grade }}</dd>
      <dt>Joining Date: </dt><dd>&nbsp;{{ date('d-m-Y',strtotime($rows_m->emp_joining_dt)) }}</dd>
      <dt>Birth Date: </dt><dd>&nbsp;{{ date('d-m-Y',strtotime($rows_m->emp_birth_dt)) }}</dd>
      <dt>Joining Salary: </dt><dd>&nbsp;{{ $rows_m->emp_joining_salary }}</dd>
      <dt>Present Salary:</dt><dd>&nbsp;{{ $rows_m->emp_present_salary }}</dd>
      <dt>Salary Grade: </dt><dd>&nbsp;{{ $rows_m->emp_sal_grade }}</dd>
      <dt>Actual Salary:</dt><dd>&nbsp;{{ $rows_m->emp_actual_salary }}</dd>
      <dt>Others Salary:</dt><dd>&nbsp;{{ $rows_m->emp_others_salary }}</dd>
      <dt>Promotion Date:</dt><dd>&nbsp;{{ date('d-m-Y',strtotime($rows_m->emp_promo_date)) }}</dd>
      <dt>Out Date:</dt><dd>&nbsp;{{ date('d-m-Y',strtotime($rows_m->emp_out_date)) }}</dd>
      <dt>OLD Id No:</dt><dd>&nbsp;{{ $rows_m->emp_old_id_no }}</dd>
      <dt>Secret Id:</dt><dd>&nbsp;{{ $rows_m->emp_secret_id }}</dd>
    </dl>
  </div>
 
</div>

@stop

@section('pagescript')

<script type="text/javascript">
  $(document).ready(function() {

  });
</script>

@stop
