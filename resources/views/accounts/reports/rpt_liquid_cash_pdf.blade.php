<!DOCTYPE>
<html>
<head>
  <style>
  body { margin: 0; font-size: 11px; font-family: "Arrial Narrow";}

  table {

  }

  td {
    border-top: none;
    border: 1px solid black;
  }
  th {
      border: none;
  }

  table {
    width: 100%;
    border-collapse: collapse;
  }
  </style>
    <title>Control Wise Subsidiary Ledger Balance</title>

</head>
<body>
<section class="content">
<div class="container">

  <div class="row justify-content-center">
    <div class="col-md-12">
      <table>
        <thead>
          <thead>
              <tr><th align="center" width="100%" colspan="8"><font size="6"><b>{{$comp_name}}</b><font></th></tr>
              <tr><th align="center" colspan="8"><font size="2"><b>Liquid Cash Report</b><font></th></tr>
              <tr><th align="center" colspan="8"><font size="3"><b>Date Range:&nbsp;{{ date('d/m/Y', strtotime($fromdate)) }} to {{ date('d/m/Y', strtotime($todate)) }}</b><font></th></tr>
          </thead>
          <tr>
            <td align="center"><b>SL No</td>
            <td align="center"><b>Account Name</td>
            <td align="center" colspan="2"><b>Balance</b></td>
          </tr>
          <tr>
            <td align="center" width="3%"></td>
            <td align="center" width="55%"></td>
            <td align="center" width="13%"><b>Debit</b></td>
            <td align="center" width="13%"><b>Credit</b></td>
          </tr>
        </thead>
        <tbody>
         <?php
          $total_op = 0;   $total_bal = 0;
          $total_tr_debit = 0;
          $total_tr_credit = 0;
          $i = 0;
          $total_bal_debit = 0;
          $total_bal_credit = 0;
         ?>
          @foreach($rows as $row)
          <?php
            $op  = $row->op_debit - $row->op_credit;
            $bal = $op + $row->tr_debit -  $row->tr_credit;

            $total_op = $total_op + $op;
            $total_bal  = $total_bal + $bal;

            $total_tr_debit = $total_tr_debit + $row->tr_debit;
            $total_tr_credit = $total_tr_credit + $row->tr_credit;

            if($bal>0) $total_bal_debit = $total_bal_debit + $bal;
            if($bal<0) $total_bal_credit = $total_bal_credit + $bal;

            ?>
          <tr>
             <td align="center">{{ $i += 1 }}</td>
             <td>{{ $row->acc_head }}</td>
             <td align="right">{{ $bal > 0 ? number_format($bal,2):'0.00'}}</td>
             <td align="right">{{ $bal < 0 ? number_format(abs($bal),2) :'0.00'}}</td>
          </tr>

          @endforeach
          <tr>
            <td align="right" colspan="2"><b>Total:</b></td>
            <td align="right"><b>{{ number_format($total_bal_debit,2) }}</b></td>
            <td align="right"><b>{{ number_format(abs($total_bal_credit),2) }}</b></td>
          </tr>
          </tbody>
        </table>
        </div>
  </div>

</div>
</section>
</body>
</html>
