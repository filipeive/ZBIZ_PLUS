<?php $__env->startSection('title', 'Meu Perfil'); ?>
<?php $__env->startSection('page-title', 'Meu Perfil'); ?>
<?php $__env->startSection('title-icon', 'fa-user-gear'); ?>

<?php $__env->startSection('breadcrumbs'); ?>
    <li class="breadcrumb-item active">Perfil</li>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
    <div class="row">
        <!-- Informações do Perfil -->
        <div class="col-lg-4 mb-4">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center">
                    <div class="position-relative d-inline-block mb-3">
                        <img src="<?php echo e($user->avatar_url); ?>" 
                             class="rounded-circle border border-3 border-white shadow-sm"
                             width="120" 
                             height="120"
                             style="object-fit: cover;">
                        <?php if($user->is_active): ?>
                            <span class="position-absolute bottom-0 end-0 p-2 bg-success border border-light rounded-circle">
                                <span class="visually-hidden">Ativo</span>
                            </span>
                        <?php else: ?>
                            <span class="position-absolute bottom-0 end-0 p-2 bg-danger border border-light rounded-circle">
                                <span class="visually-hidden">Inativo</span>
                            </span>
                        <?php endif; ?>
                    </div>
                    <h4 class="mb-1 fw-bold"><?php echo e($user->name); ?></h4>
                    <p class="text-muted mb-2"><?php echo e($user->email); ?></p>
                    
                    <span class="badge bg-<?php echo e($user->role?->name === 'admin' ? 'danger' : ($user->role?->name === 'manager' ? 'primary' : 'success')); ?> mb-3">
                        <?php echo e($user->role_display); ?>

                    </span>

                    <div class="d-grid gap-2 mb-4">
                        <button type="button" class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#uploadPhotoModal">
                            <i class="fas fa-camera me-2"></i>Alterar Foto
                        </button>
                        <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-user me-2"></i>Ver Perfil Público
                        </a>
                    </div>

                    <div class="text-muted small">
                        <div class="mb-2">
                            <i class="fas fa-calendar me-2"></i>
                            <span>Membro desde <?php echo e($user->created_at->format('M Y')); ?></span>
                        </div>
                        <div class="mb-2">
                            <i class="fas fa-clock me-2"></i>
                            <span>Último login: <?php echo e($user->last_login_display); ?></span>
                        </div>
                        <div>
                            <i class="fas fa-id-badge me-2"></i>
                            <span>ID: #<?php echo e($user->id); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulários de Edição -->
        <div class="col-lg-8">
            <!-- Atualizar Informações do Perfil -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-user-edit text-primary me-2 fs-5"></i>
                    <h6 class="mb-0">Informações do Perfil</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('profile.update')); ?>" id="profileForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('patch'); ?>

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="name" class="form-label fw-semibold">Nome Completo *</label>
                                <input type="text" class="form-control <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="name" name="name" value="<?php echo e(old('name', $user->name)); ?>" required>
                                <?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label fw-semibold">Email *</label>
                                <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                    id="email" name="email" value="<?php echo e(old('email', $user->email)); ?>" required>
                                <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <?php if(auth()->user()->is_admin): ?>
                                <div class="col-md-6">
                                    <label for="role_id" class="form-label fw-semibold">Função *</label>
                                    <select class="form-select <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="role_id" name="role_id" required>
                                        <option value="">Selecione uma função</option>
                                        <?php $__currentLoopData = $roles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $role): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <option value="<?php echo e($role->id); ?>" <?php echo e(old('role_id', $user->role_id) == $role->id ? 'selected' : ''); ?>>
                                                <?php echo e(ucfirst($role->name)); ?>

                                            </option>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </select>
                                    <?php $__errorArgs = ['role_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                        <div class="invalid-feedback"><?php echo e($message); ?></div>
                                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                                </div>
                            <?php else: ?>
                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">Função</label>
                                    <input type="text" class="form-control" value="<?php echo e($user->role_display); ?>" disabled>
                                </div>
                            <?php endif; ?>

                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Status</label>
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                           <?php echo e(old('is_active', $user->is_active) ? 'checked' : ''); ?>>
                                    <label class="form-check-label" for="is_active">Ativo</label>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>Salvar Alterações
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Atualizar Senha -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white d-flex align-items-center">
                    <i class="fas fa-lock text-warning me-2 fs-5"></i>
                    <h6 class="mb-0">Alterar Senha</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="<?php echo e(route('profile.change-password')); ?>" id="passwordForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('put'); ?>

                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="current_password" class="form-label fw-semibold">Senha Atual *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="current_password" name="current_password" autocomplete="current-password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('current_password')">
                                        <i class="fas fa-eye" id="toggle-current_password"></i>
                                    </button>
                                </div>
                                <?php $__errorArgs = ['current_password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-4">
                                <label for="password" class="form-label fw-semibold">Nova Senha *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="password" name="password" autocomplete="new-password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password')">
                                        <i class="fas fa-eye" id="toggle-password"></i>
                                    </button>
                                </div>
                                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>

                            <div class="col-md-4">
                                <label for="password_confirmation" class="form-label fw-semibold">Confirmar Nova Senha *</label>
                                <div class="input-group">
                                    <input type="password" class="form-control <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                        id="password_confirmation" name="password_confirmation" autocomplete="new-password" required>
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword('password_confirmation')">
                                        <i class="fas fa-eye" id="toggle-password_confirmation"></i>
                                    </button>
                                </div>
                                <?php $__errorArgs = ['password_confirmation'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                            </div>
                        </div>

                        <div class="d-flex gap-3 mt-4">
                            <button type="submit" class="btn btn-warning">
                                <i class="fas fa-key me-2"></i>Atualizar Senha
                            </button>
                            <button type="button" class="btn btn-outline-secondary" onclick="resetPasswordForm()">
                                <i class="fas fa-undo me-2"></i>Cancelar
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Excluir Conta -->
            <div class="card border-0 shadow-sm border-danger">
                <div class="card-header bg-light border-danger d-flex align-items-center">
                    <i class="fas fa-exclamation-triangle text-danger me-2 fs-5"></i>
                    <h6 class="mb-0 text-danger">Zona de Perigo</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-3">
                        <i class="fas fa-exclamation-triangle me-2 text-danger"></i>
                        Uma vez que sua conta for excluída, todos os seus recursos e dados serão permanentemente deletados.
                        Antes de excluir sua conta, certifique-se de que não há dados importantes associados a ela.
                    </p>

                    <button type="button" class="btn btn-outline-danger" data-bs-toggle="modal" data-bs-target="#deleteAccountModal">
                        <i class="fas fa-trash me-2"></i>Excluir Conta
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Upload de Foto -->
    <div class="modal fade" id="uploadPhotoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-camera me-2"></i>Alterar Foto de Perfil
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="<?php echo e(route('profile.update-photo')); ?>" enctype="multipart/form-data" id="photoForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('patch'); ?>

                        <div class="text-center mb-4">
                            <img id="photo-preview" src="<?php echo e($user->avatar_url); ?>" 
                                 class="rounded-circle mb-3" width="150" height="150">
                        </div>

                        <div class="mb-3">
                            <label for="photo" class="form-label fw-semibold">Selecionar nova foto</label>
                            <input type="file" class="form-control <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" 
                                   id="photo" name="photo" accept="image/*" required>
                            <div class="form-text">
                                JPG, PNG ou GIF (máx. 2MB)
                            </div>
                            <?php $__errorArgs = ['photo'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="photoForm" class="btn btn-primary">
                        <i class="fas fa-upload me-2"></i>Enviar Foto
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Confirmação de Exclusão -->
    <div class="modal fade" id="deleteAccountModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Exclusão da Conta
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção!</strong> Esta ação é irreversível!
                    </div>
                    <p class="mb-3">
                        Tem certeza que deseja excluir sua conta? Esta ação não pode ser desfeita.
                    </p>
                    <form method="POST" action="<?php echo e(route('profile.destroy')); ?>" id="deleteAccountForm">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('delete'); ?>

                        <div class="mb-3">
                            <label for="password_delete" class="form-label fw-semibold">Confirme sua senha para continuar:</label>
                            <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>"
                                id="password_delete" name="password" placeholder="Senha atual" required>
                            <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                                <div class="invalid-feedback"><?php echo e($message); ?></div>
                            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" form="deleteAccountForm" class="btn btn-danger">
                        <i class="fas fa-trash me-2"></i>Excluir Conta
                    </button>
                </div>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
// Preview da imagem
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photo-preview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Toggle de senha
function togglePassword(fieldId) {
    const field = document.getElementById(fieldId);
    const toggleIcon = document.getElementById('toggle-' + fieldId);
    
    if (field.type === 'password') {
        field.type = 'text';
        toggleIcon.className = 'fas fa-eye-slash';
    } else {
        field.type = 'password';
        toggleIcon.className = 'fas fa-eye';
    }
}

// Reset forms
function resetForm() {
    document.getElementById('profileForm').reset();
    FDSMULTSERVICES.Toast.show('Alterações canceladas!', 'info');
}

function resetPasswordForm() {
    document.getElementById('passwordForm').reset();
    FDSMULTSERVICES.Toast.show('Alterações de senha canceladas!', 'info');
}

// Show modal if there are deletion errors
<?php if($errors->userDeletion->isNotEmpty()): ?>
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
    deleteModal.show();
<?php endif; ?>

// Show modal if there are photo upload errors
<?php if($errors->has('photo')): ?>
    const photoModal = new bootstrap.Modal(document.getElementById('uploadPhotoModal'));
    photoModal.show();
<?php endif; ?>
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('layouts.app', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/resources/views/profile/edit.blade.php ENDPATH**/ ?>