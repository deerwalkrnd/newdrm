@extends('layouts.hr.app')

@section('title','Leave Request')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Mail Setting", "url" => "/leave-request/create"])

<table class="unit_table mx-auto drmDataTable">
    <thead>
    ` <tr class="table_title" style="background-color: #0f5288;">
        <th scope="col" class="ps-4">S.N</th>
            <th scope="col">Name</th>
            <th scope="col" class="text-center">Send Mail</th>    
        </tr>`
    </thead>
    <tbody>
        @forelse($mails as $mail)
        <tr>
            <th scope="row" class="ps-4 text-dark">{{ $loop->iteration }}</th>
            <td>{{ $mail->name }}</td>       
            <td>
                <form method="POST" action="/mail/{{$mail->id}}">
                    @csrf
                    <input type="hidden" name="send_mail" value="0">
                    <center>
                        <input onchange="this.form.submit()" class="form-check-input" name="send_mail" value="1" type="checkbox" role="switch" id="send_mail" 
                        {{ (isset($mail) && $mail->send_mail == '1') ? 'checked':'' }}
                        {{ old('send_mail') == '1' ? 'checked':'' }} />
                    </center>   
                </form>
                <!-- <form action="/mail/save/{{ $mail->id }}" method="POST" class="d-inline"> -->
                    <!-- <div class="form-check form-switch center">
                        <a href="/mail/save/{{ $mail->id }}">
                        <input type="hidden" name="send_mail" value="0">
                        <input class="form-check-input" name="send_mail" value="1" type="checkbox" role="switch" id="flexSwitchCheckChecked" 
                        {{ (isset($mail) && $mail->send_mail == '1') ? 'checked':'' }}
                        {{ old('send_mail') == '1' ? 'checked':'' }} />
                        </a>
                    </div> -->
                <!-- </form> -->
            </td>    
            
        </tr>
        @empty
        <tr>
            <th colspan=11 class="text-center text-dark">No LeaveRequest Found</th>
        </tr>
        @endforelse
    </tbody>
</table>
{{-- $leaveRequests->links() --}}

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })

     //Search leave by date 
    function search(){
        let date = $('#date').val();
        if(date)
            $(location).attr('href','/leave-request/approve?d='+date);
    }

    function reset(){
        $(location).attr('href','/leave-request/approve');
    }
    function validate(){
        let send_mail = document.getElementById('send_mail').checked;
        $(location).attr('href','/mail?m='+send_mail);
    }

    //Search by date or Employee
    // function search(){
    //     let date = $('#punch_date').val();
    //     let employee_id = $('#employee_id').val();
    //     console.log(employee_id);
    //     if(date)
    //         $(location).attr('href','/punch-in-detail?d='+date);
    //     if(employee_id)
    //         $(location).attr('href','/punch-in-detail?e='+employee_id);

    // }

    $('.employee-livesearch').select2({    
        ajax: {
            url: '/employee/search',
            data: function (params) {
                var query = {
                    q: params.term,
                }
                    // Query parameters will be ?search=[term]
                return query;
            },
            dataType: 'json',
            delay: 250,
            processResults: function (data) {
                return {
                    results: $.map(data, function (item) {
                        let full_name = (item.middle_name === null) ? item.first_name + " " + item.last_name : item.first_name + " " + item.middle_name + " " + item.last_name;
                        return {
                            text: full_name,
                            id: item.id
                        }
                    })
                };
            },
            cache: true
        }
    });
</script>
@endsection