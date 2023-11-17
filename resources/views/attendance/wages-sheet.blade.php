<body>
  <div id="invoiceholder">
  <div id="invoice" class="effect2">

  <?php $i = 1; ?>

    <htmlpageheader name="page-header" style="padding-top:100px;">
    <div id="invoice-top">
        <div class="title">
          <h1 style="text-align: center;">Ajmeri Golden Fiber<span class="invoiceVal invoice_num"> </span></h1>
          <div style="text-align: center;">Silo Road Shantahar </div>
          <br>
        </div>
      </div>

      <div id="invoice-mid">   
        <div id="message">
          <h1 style="text-align: center;text-decoration: underline;">Weekly Wages Top Sheet</h1>
        </div> 
      </div>

      <div style="text-align: left; font-weight: bold">
          <div id="first-div" style="float:left;width:50%">
            Bill time: {{ $from  }} To {{ $to }}
          </div>
          <div id="first-div" style="float:left;width:47%; text-align:right">
            Payment Date: {{ $paymentDate }} 
          </div>
        </div>
    </htmlpageheader>


    <div id="invoice-bot" >
      
      <div id="table" >
        
        <table class="table-main">
          <thead>    
              <tr class="tabletitle">
                <th><h3> # </h3></th>
                <th> <h3>Particulars </h3></th>
                <th align="center"> <h3> Person </h3></th>
                <th align="center"> <h3> Advance </h3></th>
                <th align="center"> <h3> Amount </h3></th>
                <th><h3> <center> Remarks </center> </h3></th>
              </tr>
          </thead>

        <?php 
          $totalPerson = 0;
          $total = 0;
          $advance = 0;
        ?>
        @foreach($lists as $row)
          
          <tr class="list-item">
            <td data-label="" class="tableitem" width="5%">{{ $row['sl'] }}</td>
            <td data-label="Description" class="tableitem" width="30%">{{ $row['sectionName'] }}</td>
            <td data-label="Description" class="tableitem" width="20%" align="center">{{ $row['person'] }}</td>
            <td data-label="Description" class="tableitem" width="30%" align="center">{{ $row['advance'] }}</td>

            <td data-label="Description" class="tableitem" width="15%" align="center">{{ $row['amount'] }}</td>
            <td data-label="Total" class="tableitem" width="20%">  </td>
          </tr>
            
          <?php 
            $totalPerson += $row['person']; 
            $total += $row['amount']; 
            $advance += $row['advance']; 
          ?>

          @endforeach

            <tr class="list-item total-row" style="border: 1px solid black">
            
              <td data-label="" class="tableitem" width="5%"></td>
              <td data-label="Description" class="tableitem" style="text-align:right; border:1px solid black" width="30%"> Total </td>
              <td data-label="Description" class="" width="20%" style="text-align:right; border:1px solid black" align="center">{{ $totalPerson }} </td>
              <td data-label="Description" class="" width="30%" style="text-align:right; border:1px solid black" align="center">{{ $advance }} </td>

              <td data-label="Total" class="tableitem" width="15%" style="text-align:right; border:1px solid black" align="center">{{ $total }}</td>
              <td data-label="Total" class="tableitem" width="20%">  </td>
            </tr>
            
            <!-- <tr class="list-item total-row">
                <th class="tableitem"><h4>Accountant</h4></th>
                <th  class="tableitem"><h4>Verifyer</h4></th>
                <th class="tableitem"><h4>Manager</h4></th>
                <th class="tableitem"><h4>Factory Manager</h4></th>
                <th  class="tableitem"><h4>CEO</h4></th>
                <th  class="tableitem"><h4></h4></th>
            </tr> -->

            <!-- <br><br><br><br><br> -->
          
        </table>
      </div><!--End Table-->
      
      <htmlpagefooter name="page-footer">

        <div style="text-align: left; font-weight: bold">
          <div id="first-div" style="float:left;width:20%">
            Accountant
          </div>
          <div id="first-div" style="float:left;width:20%; ">
            Verifyer
          </div>
          <div id="first-div" style="float:left;width:20%; ">
            Manager
          </div>
          <div id="first-div" style="float:left;width:25%; padding-right: 20px">
            Factory Manager
          </div>
          <div id="first-div" style="float:right;width:10% ">
            CEO
          </div> 
        
        </div>
           
    </htmlpagefooter>
      
    </div><!--End InvoiceBot-->
    <footer>
      <div id="legalcopy" class="clearfix">
        <!-- <p class="col-right">Our mailing address is:
            <span class="email"><a href="mailto:supplier.portal@almonature.com">supplier.portal@almonature.com</a></span>
        </p> -->
      </div>
    </footer>
  </div><!--End Invoice-->
</div><!-- End Invoice Holder-->
  
<style>

@page {
  margin-footer:50px;
  margin-header: 25px;
  margin-top: 233px;
  header: page-header;
  footer: page-footer;
  text-align:center
}

.tableitem{
  text-align:justify;
}


@import url(https://fonts.googleapis.com/css?family=Roboto:100,300,400,900,700,500,300,100);
*{
  margin: 0;
  box-sizing: border-box;
  -webkit-print-color-adjust: exact;
}
body{
  font-weight: bold;
  font-family: 'Roboto', sans-serif;
}
::selection {background: #f31544; color: #FFF;}
::moz-selection {background: #f31544; color: #FFF;}
.clearfix::after {
    content: "";
    clear: both;
    display: table;
}
.col-left {
    float: left;
}
.col-right {
    float: right;
}
h1{
  font-size: 1.5em;
  color: #444;
}
h2{font-size: .9em;}
h3{
  font-size: 1.2em;
  font-weight: 300;
  line-height: 2em;
}
p{
  font-size: .75em;
  color: #666;
  line-height: 1.2em;
}
a {
    text-decoration: none;
    color: #00a63f;
}

#invoiceholder{
  width:100%;
  height: 100%;
}
#invoice{
  position: relative;
  margin: 0 auto;
  background: #FFF;
}

[id*='invoice-']{ /* Targets all id with 'col-' */
/*  border-bottom: 1px solid #EEE;*/
  padding: 20px;
}

#invoice-top{border-bottom: 2px solid #00a63f;}
#invoice-mid{min-height: 110px;}
#invoice-bot{ min-height: 240px;}

.logo{
    display: inline-block;
    vertical-align: middle;
	width: 110px;
    overflow: hidden;
}
.info{
    display: inline-block;
    vertical-align: middle;
    margin-left: 20px;
}
.logo img,
.clientlogo img {
    width: 100%;
}
.clientlogo{
    display: inline-block;
    vertical-align: middle;
	width: 50px;
}
.clientinfo {
    display: inline-block;
    vertical-align: middle;
    margin-left: 20px
}
.title{
  float: right;
}
.title p{text-align: right;}
#message{margin-bottom: 30px; display: block;}
h2 {
    margin-bottom: 5px;
    color: #444;
}
.col-right td {
    color: #666;
    padding: 5px 8px;
    border: 0;
    font-size: 0.75em;
    border-bottom: 1px solid #eeeeee;
}
.col-right td label {
    margin-left: 5px;
    font-weight: 600;
    color: #444;
}
.cta-group a {
    display: inline-block;
    padding: 7px;
    border-radius: 4px;
    background: rgb(196, 57, 10);
    margin-right: 10px;
    min-width: 100px;
    text-align: center;
    color: #fff;
    font-size: 0.75em;
}
.cta-group .btn-primary {
    background: #00a63f;
}
.cta-group.mobile-btn-group {
    display: none;
}
table{
  width: 100%;
  border-collapse: collapse;
}

th{
    border: 1px solid black;
    text-align: center;
    font-weight: bold;
    color: black;
    font-size: 200%;
    border-bottom: 1px solid black;
}

td{
    border: 1px solid black;
    padding: 10px;
    /* border-bottom: 1px solid #cccaca; */
    font-size: 0.70em;
    text-align: center;
}

.total-row td{
  text-align: center;
}

.tabletitle th {
  border-bottom: 2px solid #ddd;
  text-align: right;
}


.tabletitle th:nth-child(2) {
    text-align: left;
}
th {
    font-size: 0.7em;
    text-align: left;
    padding: 5px 10px;
}
.item{width: 50%;}
.list-item td {
    text-align: right;
}
.list-item td:nth-child(2) {
    text-align: left;
}
.total-row th,
.total-row td {
    text-align: center;
    font-weight: 700;
    font-size: .75em;
    border: 0 none;
}
.table-main {
    
}
footer {
    border-top: 1px solid #eeeeee;;
    padding: 15px 20px;
}
.effect2
{
  position: relative;
}
.effect2:before, .effect2:after
{
  z-index: -1;
  position: absolute;
  content: "";
  bottom: 15px;
  left: 10px;
  width: 50%;
  top: 80%;
  max-width:300px;
  background: #777;
  -webkit-box-shadow: 0 15px 10px #777;
  -moz-box-shadow: 0 15px 10px #777;
  box-shadow: 0 15px 10px #777;
  -webkit-transform: rotate(-3deg);
  -moz-transform: rotate(-3deg);
  -o-transform: rotate(-3deg);
  -ms-transform: rotate(-3deg);
  transform: rotate(-3deg);
}
.effect2:after
{
  -webkit-transform: rotate(3deg);
  -moz-transform: rotate(3deg);
  -o-transform: rotate(3deg);
  -ms-transform: rotate(3deg);
  transform: rotate(3deg);
  right: 10px;
  left: auto;
}
@media screen and (max-width: 767px) {
    h1 {
        font-size: .9em;
    }
    #invoice {
        width: 100%;
    }
    #message {
        margin-bottom: 20px;
    }
    [id*='invoice-'] {
        padding: 20px 10px;
    }
    .logo {
        width: 140px;
    }
    .title {
        float: none;
        display: inline-block;
        vertical-align: middle;
        margin-left: 40px;
    }
    .title p {
        text-align: left;
    }
    .col-left,
    .col-right {
        width: 100%;
    }
    .table {
        margin-top: 20px;
    }
    #table {
        white-space: nowrap;
        overflow: auto;
    }
    td {
        white-space: normal;
    }
    .cta-group {
        text-align: center;
    }
    .cta-group.mobile-btn-group {
        display: block;
        margin-bottom: 20px;
    }
     /*==================== Table ====================*/
    .table-main {
        border: 0 none;
    }  
      .table-main thead {
        border: none;
        clip: rect(0 0 0 0);
        height: 1px;
        margin: -1px;
        overflow: hidden;
        padding: 0;
        position: absolute;
        width: 1px;
      }
      .table-main tr {
        border-bottom: 2px solid #eee;
        display: block;
        margin-bottom: 20px;
      }
      .table-main td {
        font-weight: 700;
        display: block;
        padding-left: 40%;
        max-width: none;
        position: relative;
        border: 1px solid #cccaca;
        text-align: left;
      }
      .table-main td:before {
        /*
        * aria-label has no advantage, it won't be read inside a table
        content: attr(aria-label);
        */
        content: attr(data-label);
        position: absolute;
        left: 10px;
        font-weight: normal;
        text-transform: uppercase;
      }
    .total-row th {
        display: none;
    }
    .total-row td {
        text-align: left;
    }
    footer {text-align: center;}
}

</style>
  

</body>