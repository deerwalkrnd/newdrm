@extends('layouts.hr.app')

@section('title','Time Setting')

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
            </td>    
            
        </tr>
        @empty
        <tr>
            <th colspan=11 class="text-center text-dark">No Mail Setting Found</th>
        </tr>
        @endforelse
    </tbody>
</table>

@include('layouts.basic.tableFoot')
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        $('.drmDataTable').DataTable();
    })
</script>
@endsection