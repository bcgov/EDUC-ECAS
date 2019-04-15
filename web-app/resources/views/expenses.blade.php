@extends('app')

@section('content')
<div class="card">
    <div class="card-header"><h1>Expenses - Session #1</h1></div>
    <div class="card-body">
        <table class="table table-hover">
            <tr>
                <th>Type</th>
                <th>Description</th>
                <th class="text-right">Amount</th>
                <th>Status</th>
            </tr>
            <tbody>
                @foreach($expenses as $expense)
                    <tr>
                        <td>{{ $expense['type'] }}</td>
                        <td>{{ $expense['description'] }}</td>
                        <td class="text-right">{{ $expense['amount'] }}</td>
                        <td>{{ $expense['status'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection