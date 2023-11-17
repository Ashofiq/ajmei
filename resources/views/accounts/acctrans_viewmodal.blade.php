@extends('layouts.app_m')
@section('content')
@include('inc.showAccModal')

<div class="row">
    <input type="hidden" name="menu_selection" id="menu_selection" value="ACC@1" class="form-control" required>
    <div class="col-sm-12">
      @foreach($rows_m as $row)
      <div class="row">
          <div class="col-sm-2">Voucher No:</div>
          <div class="col-sm-2">{{ $row->trans_type }}-{{ $row->voucher_no }}</div>
          <div class="col-sm-2">Narration:</div>
          <div class="row-sm-6">{!! $row->t_narration !!}{!! $row->t_narration_1 !!}</div>
      </div>
      <div class="row">
        <div class="col-sm-2">Voucher Date:</div>
        <div class="col-sm-2">{{ $row->voucher_date }}</div>
      </div>
      @endforeach
    </div>

    <div class="col-sm-12">
      <table class="table table-striped table-data table-report">
        <thead class="thead-dark">
          <tr>
            <th>Id</th>
            <th>Accounts Head</th>
            <th>Description</th>
            <th>Debit</th>
            <th>Credit</th>
          </tr>
        </thead>
        <tbody>
          <?php $i = 0;
           $t_d_amount = 0 ; $t_c_amount = 0 ;
          ?>
          @foreach($rows_d as $row)
          <tr>
            <td>{{ $i=$i+1 }}</td>
            <td>{{ $row->acc_head }}</td>
            <td>{{ $row->acc_origin }}</td>
            <td align="right">{{number_format($row->d_amount, 2)}}</td>
            <td align="right">{{ number_format($row->c_amount, 2) }}</td>
           <?php  $t_d_amount = $t_d_amount + $row->d_amount;
             $t_c_amount = $t_c_amount + $row->c_amount;
            ?>
          </tr>
          @endforeach
          <tr>
            <td colspan="3" align="right"><b>Total :</b></td>
            <td align="right"><b>{{ number_format($t_d_amount,2) }}</b></td>
            <td align="right"><b>{{ number_format($t_c_amount,2) }}</b></td>
          </tbody>
        </table>
    </div>
</div>

@stop

@section('pagescript')

<script type="text/javascript">
  $(document).ready(function() {

  });
</script>

@stop
