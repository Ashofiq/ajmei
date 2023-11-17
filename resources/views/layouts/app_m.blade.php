<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
 <style>
    dl {
      display: grid;
      grid-template-columns: max-content auto;
    }

    dt {
      grid-column-start: 1;
    }

    dd {
      grid-column-start: 2;
    }
  </style>
</head>

<body class="e2a-animate">
  <div class="e2a-wrapper e2a-collapsible-sidebar e2a-collapsible-sidebar-collapsed">

    <!-- Content Wrapper. Contains page content -->
    <div id="e2a-tab-content" class="tab-content container">
          @yield('content')
    </div>

  </div>
</body>
</html>
