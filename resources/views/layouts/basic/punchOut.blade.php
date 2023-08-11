<div class="col">
    <span class="punch_out_container">        
        <form class="punch_out_form" action="/punch-out" method="POST" onsubmit="return confirm('Do you want to punch-out?');">
            @csrf
            <input type="hidden" placeholder="Punch In/Out Remarks">
            <span class="punch_out_button">
                <button>Punch Out</button>
            </span>
        </form>
    </span>
</div>