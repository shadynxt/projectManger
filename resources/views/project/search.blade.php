@extends('layout')

@section('content')


<h1>Displaying Results for:  "{{ $value }}" </h1>

<table class="table table-striped">
    <thead>
      <tr>
        <th>Project Title</th>
        <th>Start Date</th>
        <th>End Date</th>
        <th>Actions</th>
      </tr>
    </thead>

@if ( !$projects->isEmpty() ) 
    <tbody>
    @foreach ( $projects as $project)
      <tr>
        <td>{{ $project->project_name }} </td>
        <td>
            {{ $project->start_date }}
        </td>
        <td>
           {{ $project->end_date }}
        </td>
        <td>
            <!-- <a href="{{ route('task.edit', ['id' => $task->id]) }}" class="btn btn-primary"> edit </a> -->
            <a href="{{ route('project.view', ['id' => $project->id]) }}" class="btn btn-primary"><span class="glyphicon glyphicon-eye-open" aria-hidden="true"></span></a>
            <a href="{{ route('project.delete', ['id' => $project->id]) }}" class="btn btn-danger"><span class="glyphicon glyphicon-trash" aria-hidden="true"></span></a>

        </td>
      </tr>

    @endforeach
    </tbody>
@else 
    <p><em>No match found</em></p>
@endif


</table>



    <div class="btn-group">
        <a class="btn btn-default" href="{{ redirect()->getUrlGenerator()->previous() }}">Go Back</a>
    </div>



@stop