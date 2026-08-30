<div class="text-center mb-4">
    @if($user->foto_perfil)
        <img src="{{ asset($user->foto_perfil) }}" class="rounded-circle mb-3" style="width: 100px; height: 100px; object-fit: cover;">
    @else
        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center mb-3 mx-auto" style="width: 100px; height: 100px;">
            <i class="fas fa-user text-white fa-2x"></i>
        </div>
    @endif
    <h5 class="mb-0">{{ $user->name }}</h5>
    <p class="text-muted">{{ $user->email }}</p>
</div>

<div class="card mb-3">
    <div class="card-header bg-primary text-white">
        <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i> Informações</h6>
    </div>
    <div class="card-body">
        <p><strong>Telefone:</strong> {{ $user->telefone ?? 'Não informado' }}</p>
        <p><strong>Tipo:</strong> 
            <span class="badge {{ $user->role === 'admin' ? 'bg-danger' : 'bg-info' }}">
                {{ ucfirst($user->role) }}
            </span>
        </p>
        <p><strong>Status:</strong> 
            <span class="badge {{ $user->is_active ? 'bg-success' : 'bg-secondary' }}">
                {{ $user->is_active ? 'Ativo' : 'Inativo' }}
            </span>
        </p>
        <p><strong>Criado em:</strong> {{ $user->created_at->format('d/m/Y \à\s H:i') }}</p>
    </div>
</div>

<div class="d-grid gap-2">
    <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
        <i class="fas fa-edit me-2"></i> Editar Usuário
    </a>
    @if($loggedId !== $user->id)
        <form action="{{ route('users.destroy', $user) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este usuário?')">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger">
                <i class="fas fa-trash me-2"></i> Excluir Usuário
            </button>
        </form>
    @endif
</div>