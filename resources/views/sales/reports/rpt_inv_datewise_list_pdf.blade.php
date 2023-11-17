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

            table tr td{
              padding: 5px;
            }

            table thead{
              font-weight: bold
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
                <tr><td colspan="5" align="center"><font size="5"><b>Date Wise Sales Report</b></font></td></tr>
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
                <table border="1" cellspacing="0" cellpadding="0">
                    <thead>
                      <tr>
                        <td><b>Invoice&nbsp;Date</b></td>
                        <td><b>Invoice No</b></td>
                        <td><b>Customer</b></td>
                        <td><b>Address</b></td>
                        <td><b>Item Name</b></td>
                        <td><b>Rate</b></td>
                        <td><b>Qty</b></td>
                        <td><b>Unit</b></td>
                        <td><b>Total Disc</b></td>
                        <td><b>Net Amount</b></td>
                      </tr>
                    </thead>
                    <tbody>
                    <?php 
                    
                        $inv_qty = 0;
                        $inv_disc_value = 0;
                        $inv_net_amt=0;
                    
                    ?>
                    @foreach($rows as $row)
                    <?php 
                        $inv_qty += $row->inv_qty;
                        $inv_disc_value += $row->inv_disc_value; 
                        $n_amt = ($row->inv_qty * $row->inv_item_price) + $row->inv_disc_value;
                        $inv_net_amt += $n_amt;
                    ?>
                    <tr>
                        <td>{{ $row->inv_date }}</td>
                        <td>{{ $row->inv_no }}</td>
                        <td>{{ $row->cust_name }}</td>
                        <td>{{ $row->cust_add1 }} {{ $row->cust_add2 }}</td>
                        <td>{{ $row->item_name }}{{ $row->itm_cat_name }}</td>
                        <td>{{ $row->inv_item_price }}</td>
                        <td align="right">{{ $row->inv_qty }}</td>
                        <td>{{ $row->inv_unit }}</td>
                        <td align="right">{{ $row->inv_disc_value }}</td>
                        <td align="right">{{ number_format($n_amt,2) }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td colspan="6">&nbsp;</td> 
                        <td align="right"><b>{{ number_format($inv_qty,2) }}</b></td>
                        <td>&nbsp;</td> 
                        <td align="right"><b>{{ number_format($inv_disc_value,2) }}</b></td>
                        <td align="right"><b>{{ number_format($inv_net_amt,2) }}</b></td>
                    </tr>
                    </tbody>
                </table>
              </div>
             </div>
            </p>
        </main>
    </body>
</html>
