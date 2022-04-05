<div class="col">
    <div class="row">
        <span class="punch_out_container">
            <form class="punch_out_form" action="/punch-in" method="POST">
                @csrf
                <input type="hidden" name="code" value="OXqSTexF5zn4uXSp">

                @if(isset($reason) && ($reason == "required"))
                    <input placeholder="Punch In/Out Remarks" name="reason">
                @endif
                <span class="punch_out_button">
                    <button>Punch In</button>
                </span>
            </form>
        </span>
    </div>
    @if(isset($reason) && ($reason == "required"))
    <div class="row">
        <span class="punch_out_container" style="position: relative;">
            <form class="punch_out_form">
                @error('reason')
                    <p class="text-danger">{{ $message }}</p>
                @enderror
            </form>
        </span>
    </div>
    @endif
</div>