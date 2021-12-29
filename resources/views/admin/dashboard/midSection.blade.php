<!-- section for middle part start-->
<div class="row justify-content-around my-2">
    <div class="col-md-7 box_background p-3 mb-4">
        <span class="leave_table_title">Leave Balance</span>
        <div class="mt-3">
            <table class="unit_table mx-auto w-100">
                <tr class="table_title" style="background-color: #3573A3;">
                    <th>Leave Type</th>
                    <th>Accrued</th>
                    <th>Allowed</th>
                    <th>Leave Taken</th>
                    <th>Balance</th>
                </tr>
                @foreach($leaveBalance as $leaveType => $balance)
                <tr>
                    <td>{{ $leaveType }}</td>
                    <td>{{ $balance['accrued'] }}</td>
                    <td>{{ $balance['allowed'] }}</td>
                    <td>{{ $balance['taken'] }}</td>
                    <td>{{ $balance['balance'] }}</td>
                </tr>
                @endforeach
            </table>
        </div>
    </div>
    <div class="col-md-4 box_background p-3 mb-4 birthdays_container">
        <span class="mb-2">Upcoming Birthdays</span>

        @forelse($birthdayList as $person)
        <div class="row px-3 mt-3 justify-content-around">
            <div class="col-md-1 birthdate_box">
                {{ date('M', strtotime($person->date_of_birth)) }} <br>
                <b>{{ date('d', strtotime($person->date_of_birth)) }}</b>
            </div>
            <div class="col-md-9 birthday_person">
                <h1>{{ $person->first_name." ".$person->last_name }}</h1>
                @php
                    $year = date('m',strtotime($person->date_of_birth)) == 1 ? date('Y')+1 : date('Y');
                @endphp
                <h4>{{date('D',strtotime($year."-".date('m-d',strtotime($person->date_of_birth)))) }}, {{ date('M d', strtotime($person->date_of_birth)) }}</h4>
            </div>
        </div>
        @empty
        No Birthday found
        @endforelse


        <!-- borthday list -->

    </div>
</div>
<!-- section for middle part end-->