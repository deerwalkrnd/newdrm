<!-- page title start -->
<section class="my-3 pt-3">
    <div class="text-center">
        <h1 class="fs-2 title">{{ $table_title }}</h1>
    </div>
    <div class="underline mx-auto"></div>
</section>
<!-- page title end -->

<div class="table_container">
    @if(isset($url))
    <!-- add button start -->
    <div class="button_div">
        <a class="add_button" href="{{ $url }}"><i class="fas fa-plus"></i> Add</a>
    </div>
    <!-- add button end -->
    @endif
    <!-- table start -->
    <div>
        
