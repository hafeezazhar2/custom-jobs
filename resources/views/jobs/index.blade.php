@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Background Jobs</h1>
        <a href="{{ route('jobs.index') }}" class="btn btn-primary">Refresh</a>
    </div>

    <!-- Success and Error Messages -->
    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Jobs Table -->
    <div class="table-responsive">
        <table class="table table-striped table-bordered align-middle">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Class</th>
                    <th>Method</th>
                    <th>Status</th>
                    <th>Priority</th>
                    <th>Scheduled At</th>
                    <th>Retry Count</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->class }}</td>
                    <td>{{ $job->method }}</td>
                    <td>
                        @if($job->status == 'completed')
                            <span class="badge bg-success">{{ ucfirst($job->status) }}</span>
                        @elseif($job->status == 'failed')
                            <span class="badge bg-danger">{{ ucfirst($job->status) }}</span>
                        @elseif($job->status == 'running')
                            <span class="badge bg-info">{{ ucfirst($job->status) }}</span>
                        @elseif($job->status == 'pending')
                            <span class="badge bg-warning">{{ ucfirst($job->status) }}</span>
                        @else
                            {{ ucfirst($job->status) }}
                        @endif
                    </td>
                    <td>{{ $job->priority }}</td>
                    <td>{{ $job->scheduled_at ? $job->scheduled_at : 'Not Scheduled' }}</td>
                    <td>{{ $job->retry_attempts }}</td>
                    <td>
                        <div class="d-flex gap-2">
                            @if($job->status == 'failed' || $job->status == 'canceled')
                            <form action="{{ route('jobs.retry', $job->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-sm">Retry</button>
                            </form>
                            @endif
                            @if($job->status == 'running')
                            <form action="{{ route('jobs.cancel', $job->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Cancel</button>
                            </form>
                            @endif
                            @if($job->status == 'completed')
                                <button type="button" class="btn btn-success btn-sm" disabled>Completed</button>
                            @elseif($job->status == 'pending')
                                <button type="button" class="btn btn-warning btn-sm" disabled>Pending</button>
                            @endif
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
