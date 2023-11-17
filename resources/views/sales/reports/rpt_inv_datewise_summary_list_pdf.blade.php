<html>
    <head>
        <style>
        body { margin: 0; font-size: 12px; font-family: "Arrial Narrow";}
            /** Define the margins of your page **/
            @page {
                margin: 100px 55px;
            }

            header {
                position: fixed;
                top: -45px;
                left: 0px;
                right: 0px;
                height: 50px;
                /** Extra personal styles **/

                color: white;
                text-align: center;
                line-height: 35px;
            }

            footer {
                position: fixed;
                bottom: 10px;
                text-align: center;
            }

            table {
              width: 100%;
              border-collapse: collapse;
            }
            h1 {
              border-bottom-style: solid;
            }
        </style>
    </head>
    <body>
        <!-- Define header and footer blocks before your content -->
        <header>
          <div class="row justify-content-center">
            <div class="col-md-12">
              <table class="table" >
                <tr><td colspan="5" align="center"><font size="5">Date Wise Summary Report</font></td></tr>
                <tr><td colspan="5" align="center"><font size="5">{{ date('d-m-Y', strtotime($fromdate)) }} TO
                {{ date('d-m-Y', strtotime($todate)) }}</font></td></tr>
              </table>
            </div>
          </div>
        </header>

        <footer>
          <div class="row">
            <div class="col-sm-12">
              <table border="0" cellspacing="0" cellpadding="0"
              style='font-family:"Arrial Narrow", Courier, monospace; font-size:80px'>

                  <tr>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td>&nbsp;</td>
                      <td height="40%">&nbsp;</td>
                  </tr>

              </table>
            </div>
          </div>
        </footer>
        <!-- Wrap the content of your PDF inside a main tag -->
        <main>
            <p>
              <div class="row justify-content-center">
                <div class="col-md-12">
                <table class="table table-bordered" border="1">
                 <tr>
                   <td align="center"><b>Invoice&nbsp;Date</b></td>
                   <td align="center"><b>Total&nbsp;Amount</b></td>
                   <td align="center"><b>Total&nbsp;Discount</b></td>
                   <td align="center"><b>Total&nbsp;VAT</b></td>
                   <td align="center"><b>Net&nbsp;Amount</b></td>
                </tr>
                <?php $inv_sub_total = 0; $inv_disc_value = 0;
                $inv_vat_value = 0; $inv_net_amt = 0; ?>
                @foreach($rows as $row)
                  <?php $inv_sub_total += $row->inv_sub_total;
                  $inv_disc_value += $row->inv_itm_disc_value + $row->inv_disc_value;
                  $inv_vat_value += $row->inv_vat_value;
                  $inv_net_amt += $row->inv_net_amt; ?>
                  <tr>
                    <td align="center">{{ date('d-m-Y', strtotime($row->inv_date)) }}</td>
                    <td align="right">{{ number_format($row->inv_sub_total,2) }}</td>
                    <td align="right">{{ number_format($row->inv_itm_disc_value + $row->inv_disc_value,2) }}</td>
                    <td align="right">{{ number_format($row->inv_vat_value,2) }}</td>
                    <td align="right">{{ number_format($row->inv_net_amt,2) }}</td>
                  </tr>
                  @endforeach
                  <tr>
                    <td align="center"><b>Total</b></td>
                    <td align="right"><b>{{ number_format($inv_sub_total,2) }}</b></td>
                    <td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></td>
                    <td align="right"><b>{{ number_format($inv_vat_value,2) }}</b></td>
                    <td align="right"><b><b>{{ number_format($inv_net_amt,2) }}</b></td>
                  </tr>
                </table>
              </div>
             </div>
            </p>
        </main>
    </body>
</html>
