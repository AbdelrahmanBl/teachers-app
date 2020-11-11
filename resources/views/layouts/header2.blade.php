@if(session('Student') || session('ENTRY_TOKEN')  )
<nav class="navbar default-layout col-lg-12 col-12 p-0 fixed-top d-flex flex-row">
      <div class="text-center navbar-brand-wrapper d-flex align-items-top justify-content-center">

        <h2 class="brand"></h2>

      </div>
      <div class="navbar-menu-wrapper d-flex align-items-center">

        <button class="navbar-toggler navbar-toggler-right d-lg-none align-self-center" type="button"
          data-toggle="offcanvas">
          <span class="mdi mdi-menu"></span>
        </button>
      </div>
    </nav>
<script>
    $(document).on('click touchstart', function () {
        $( "#NAVCLOSE" ).click(function() {
        $("#sidebar").removeClass('active');
});
    });
</script>    
@endif    
