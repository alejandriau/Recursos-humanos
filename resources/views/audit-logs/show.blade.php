{{-- resources/views/audit-logs/show.blade.php --}}
@extends('dashboard')

@section('title', 'Detalles de Auditoría')

@section('contenido')
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Detalles del Registro de Auditoría #{{ $auditLog->id }}</h3>
                    <div class="card-tools">
                        <a href="{{ route('audit-logs.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Volver
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información General</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Evento:</th>
                                    <td>
                                        <span class="badge badge-{{ $auditLog->getEventBadge() }}">
                                            {{ ucfirst($auditLog->event) }}
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <th>Descripción:</th>
                                    <td>{{ $auditLog->description }}</td>
                                </tr>
                                <tr>
                                    <th>Usuario:</th>
                                    <td>{{ $auditLog->user->name ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Email:</th>
                                    <td>{{ $auditLog->user->email ?? 'N/A' }}</td>
                                </tr>
                                <tr>
                                    <th>Fecha y Hora:</th>
                                    <td>{{ $auditLog->created_at->format('d/m/Y H:i:s') }}</td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h5>Información Técnica</h5>
                            <table class="table table-bordered">
                                <tr>
                                    <th>Dirección IP:</th>
                                    <td>{{ $auditLog->ip_address }}</td>
                                </tr>
                                <tr>
                                    <th>User Agent:</th>
                                    <td>{{ $auditLog->user_agent }}</td>
                                </tr>
                                <tr>
                                    <th>URL:</th>
                                    <td>{{ $auditLog->url }}</td>
                                </tr>
                                <tr>
                                    <th>Modelo:</th>
                                    <td>{{ $auditLog->model_type }}</td>
                                </tr>
                                <tr>
                                    <th>ID del Modelo:</th>
                                    <td>{{ $auditLog->model_id ?? 'N/A' }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    @if($auditLog->old_values || $auditLog->new_values)
                    <div class="row mt-4">
                        <div class="col-md-12">
                            <h5>Cambios Realizados</h5>
                            <div class="table-responsive">
                                <table class="table table-bordered">
                                    <thead>
                                        <tr>
                                            <th>Campo</th>
                                            @if($auditLog->old_values)
                                            <th>Valor Anterior</th>
                                            @endif
                                            @if($auditLog->new_values)
                                            <th>Valor Nuevo</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @php
                                            $allFields = array_unique(
                                                array_merge(
                                                    array_keys($auditLog->old_values ?? []),
                                                    array_keys($auditLog->new_values ?? [])
                                                )
                                            );
                                        @endphp
                                        @foreach($allFields as $field)
                                        <tr>
                                            <td><strong>{{ ucfirst(str_replace('_', ' ', $field)) }}</strong></td>
                                            @if($auditLog->old_values)
                                            <td>
                                                @if(isset($auditLog->old_values[$field]))
                                                    {{ is_array($auditLog->old_values[$field]) ? json_encode($auditLog->old_values[$field]) : $auditLog->old_values[$field] }}
                                                @else
                                                    <em>N/A</em>
                                                @endif
                                            </td>
                                            @endif
                                            @if($auditLog->new_values)
                                            <td>
                                                @if(isset($auditLog->new_values[$field]))
                                                    {{ is_array($auditLog->new_values[$field]) ? json_encode($auditLog->new_values[$field]) : $auditLog->new_values[$field] }}
                                                @else
                                                    <em>N/A</em>
                                                @endif
                                            </td>
                                            @endif
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
