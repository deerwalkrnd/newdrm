@extends('layouts.hr.app')

@section('title','Leave Report')

@section('content')
@include('layouts.basic.tableHead',["table_title" => "Leave Balance","url"=>"/"])
<table class="unit_table mx-auto">
    <thead></thead>
        <tr class="table_title" style="background-color: #0f5288;">
            <th scope="col" class="ps-4" rowspan=2>S.N</th>
            <th scope="col" rowspan=2>Employee</th>
            <th scope="col" rowspan=2>Year</th>
            <th scope="col" colspan=4>Home</th>
            <th scope="col" colspan=4>Floating</th>
            <th scope="col" colspan=4>Sick</th>
            <th scope="col" colspan=4>Mourning</th>
            <th scope="col" colspan=4>Maternity</th>
            <th scope="col" rowspan=2>Unpaid</th>
            <th scope="col" rowspan=2>Carry Over</th>
        </tr>
        <tr class="table_title" style="background-color: #0f5288;">
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
            <th>AC</th>
            <th>A</th>
            <th>T</th>
            <th>B</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>Sagar Shrestha</td>
            <td>2022</td>
            <td>10</td>
            <td>10</td>
            <td>2</td>
            <td>8</td>
            <!--  -->
            <td>13</td>
            <td>13</td>
            <td>6</td>
            <td>7</td>
            <!--  -->
            <td>10</td>
            <td>10</td>
            <td>2</td>
            <td>8</td>
            <!--  -->
            <td>10</td>
            <td>10</td>
            <td>2</td>
            <td>8</td>
            <!--  -->
            <td>10</td>
            <td>10</td>
            <td>2</td>
            <td>8</td>
            <!--  -->
            <td>0.0</td>
            <td>0.0</td>
        </tr>

        <tr>
            <td>1</td>
            <td>Satyadeep Neupane</td>
            <td>2022</td>
            <td>10</td>
            <td>10</td>
            <td>2</td>
            <td>8</td>
            <!--  -->
            <td>13</td>
            <td>13</td>
            <td>6</td>
            <td>7</td>
            <!--  -->
            <td>10</td>
            <td>10</td>
            <td>2</td>
            <td>8</td>
            <!--  -->
            <td>10</td>
            <td>10</td>
            <td>2</td>
            <td>8</td>
            <!--  -->
            <td>10</td>
            <td>10</td>
            <td>2</td>
            <td>8</td>
            <!--  -->
            <td>0.0</td>
            <td>0.0</td>
        </tr>
    </tbody>    
</table>


@include('layouts.basic.tableFoot')
@endsection