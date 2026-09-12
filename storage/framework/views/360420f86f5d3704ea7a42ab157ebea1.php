
<div class="content-wrapper iframe-mode <?php echo e(config('adminlte.classes_content_wrapper', '')); ?>" data-widget="iframe"
     data-auto-show-new-tab="<?php echo e(config('adminlte.iframe.options.auto_show_new_tab', true)); ?>"
     data-loading-screen="<?php echo e(config('adminlte.iframe.options.loading_screen', true)); ?>"
     data-use-navbar-items="<?php echo e(config('adminlte.iframe.options.use_navbar_items', true)); ?>">

    
    <div class="nav navbar navbar-expand navbar-white navbar-light border-bottom p-0">

        
        <?php if(config('adminlte.iframe.buttons.close_all', true) || config('adminlte.iframe.buttons.close_all_other', true)): ?>

            <div class="nav-item dropdown">
                <a class="nav-link bg-danger dropdown-toggle" data-toggle="dropdown" href="#"
                   role="button" aria-haspopup="true" aria-expanded="false">
                    <?php echo e(__('adminlte::iframe.btn_close')); ?>

                </a>
                <div class="dropdown-menu mt-0">
                    <?php if(config('adminlte.iframe.buttons.close', false)): ?>
                        <a class="dropdown-item" href="#" data-widget="iframe-close">
                            <?php echo e(__('adminlte::iframe.btn_close_active')); ?>

                        </a>
                    <?php endif; ?>
                    <?php if(config('adminlte.iframe.buttons.close_all', true)): ?>
                        <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all">
                            <?php echo e(__('adminlte::iframe.btn_close_all')); ?>

                        </a>
                    <?php endif; ?>
                    <?php if(config('adminlte.iframe.buttons.close_all_other', true)): ?>
                        <a class="dropdown-item" href="#" data-widget="iframe-close" data-type="all-other">
                            <?php echo e(__('adminlte::iframe.btn_close_all_other')); ?>

                        </a>
                    <?php endif; ?>
                </div>
            </div>

        <?php elseif(config('adminlte.iframe.buttons.close', false)): ?>

            <a class="nav-link bg-danger" href="#" data-widget="iframe-close">
                 <?php echo e(__('adminlte::iframe.btn_close')); ?>

            </a>

        <?php endif; ?>

        
        <?php if(config('adminlte.iframe.buttons.scroll_left', true)): ?>
            <a class="nav-link bg-light" href="#" data-widget="iframe-scrollleft">
                <i class="fas fa-angle-double-left"></i>
            </a>
        <?php endif; ?>

        
        <ul class="navbar-nav overflow-hidden" role="tablist">

            
            <?php if(! empty(config('adminlte.iframe.default_tab.url'))): ?>
                <li class="nav-item active" role="presentation">
                    <a href="#" class="btn-iframe-close" data-widget="iframe-close" data-type="only-this">
                        <i class="fas fa-times"></i>
                    </a>
                    <a id="tab-default" class="nav-link active" data-toggle="row" href="#panel-default"
                       role="tab" aria-controls="panel-default" aria-selected="true">
                        
                        <?php echo e(config('adminlte.iframe.default_tab.title') ?: __('adminlte::iframe.tab_home')); ?>

                    </a>
                </li>
            <?php endif; ?>

        </ul>

        
        <?php if(config('adminlte.iframe.buttons.scroll_right', true)): ?>
            <a class="nav-link bg-light" href="#" data-widget="iframe-scrollright">
                <i class="fas fa-angle-double-right"></i>
            </a>
        <?php endif; ?>

        
        <?php if(config('adminlte.iframe.buttons.fullscreen', true)): ?>
            <a class="nav-link bg-light" href="#" data-widget="iframe-fullscreen">
                <i class="fas fa-expand"></i>
            </a>
        <?php endif; ?>

    </div>

    
    <div class="tab-content">

        
        <div class="tab-loading">
        <div>
            <h2 class="display-4 text-center">
                <i class="fa fa-sync fa-spin text-secondary"></i>
                <br/>
                <?php echo e(__('adminlte::iframe.tab_loading')); ?>

            </h2>
        </div>
        </div>

        
        <?php if(! empty(config('adminlte.iframe.default_tab.url'))): ?>
            <div id="panel-default" class="tab-pane fade" role="tabpanel" aria-labelledby="tab-default">
                <iframe src="<?php echo e(config('adminlte.iframe.default_tab.url')); ?>"></iframe>
            </div>
        <?php endif; ?>

        
        <div class="tab-empty">
            <h2 class="display-4 text-center">
                <?php echo e(__('adminlte::iframe.tab_empty')); ?>

            </h2>
        </div>

    </div>

</div>
<?php /**PATH /home/fdev-ms/Filipe/ZBIZ_PLUS/vendor/jeroennoten/laravel-adminlte/resources/views/partials/cwrapper/cwrapper-iframe.blade.php ENDPATH**/ ?>