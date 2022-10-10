<div class="wrap">
    <h1 class="mosparo-header">
        <?php echo esc_html(get_admin_page_title()); ?>
        <img src="<?php echo $this->pluginUrl; ?>assets/images/mosparo.svg" height="44" alt="<?php _e('mosparo', 'mosparo-integration'); ?>">
    </h1>

    <?php $this->displayAdminNotice(); ?>

    <form method="post" action="<?php echo esc_html(admin_url('admin-post.php')); ?>">
        <div>
            <h2><?php _e('Connection', 'mosparo-integration'); ?></h2>

            <?php
                $configHelper = \MosparoIntegration\Helper\ConfigHelper::getInstance();

                $host = $configHelper->isActive() ? $configHelper->getHost() : 'https://';
                $uuid = $configHelper->isActive() ? $configHelper->getUuid() : '';
                $publicKey = $configHelper->isActive() ? $configHelper->getPublicKey() : '';
                $privateKey = $configHelper->isActive() ? $configHelper->getPrivateKey() : '';
                $verifySsl = $configHelper->getVerifySsl();
                $loadResourcesAlways = $configHelper->getLoadResourcesAlways();
                $loadCssResourceOnInitialization = $configHelper->getLoadCssResourceOnInitialization();
            ?>

            <table class="form-table" role="presentation">
                <tbody>
                    <tr>
                        <th>
                            <label for="host"><?php _e('Host', 'mosparo-integration'); ?></label>
                        </th>
                        <td>
                            <input name="host" type="url" id="host" value="<?php echo $host; ?>" class="regular-text code" <?php if ($configHelper->isActive()): ?>readonly<?php endif; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="uuid"><?php _e('Unique identification number', 'mosparo-integration'); ?></label>
                        </th>
                        <td>
                            <input name="uuid" type="text" id="uuid" value="<?php echo $uuid; ?>" class="regular-text code" <?php if ($configHelper->isActive()): ?>readonly<?php endif; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="publicKey"><?php _e('Public Key', 'mosparo-integration'); ?></label>
                        </th>
                        <td>
                            <input name="publicKey" type="text" id="publicKey" value="<?php echo $publicKey; ?>" class="regular-text code" <?php if ($configHelper->isActive()): ?>readonly<?php endif; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th>
                            <label for="privateKey"><?php _e('Private Key', 'mosparo-integration'); ?></label>
                        </th>
                        <td>
                            <input name="privateKey" type="text" id="privateKey" value="<?php echo $this->getMaskedPrivateKey(); ?>" class="regular-text code" <?php if ($configHelper->isActive()): ?>readonly<?php endif; ?>>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <label for="verifySsl">
                                <input name="verifySsl" type="checkbox" id="verifySsl" value="1" <?php echo $verifySsl ? 'checked' : ''; ?>>
                                <?php _e('Verify SSL certificate', 'mosparo-integration'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <label for="loadResourcesAlways">
                                <input name="loadResourcesAlways" type="checkbox" id="loadResourcesAlways" value="1" <?php echo $loadResourcesAlways ? 'checked' : ''; ?>>
                                <?php _e('Load the mosparo resources on all pages.', 'mosparo-integration'); ?>
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th></th>
                        <td>
                            <label for="loadCssResourceOnInitialization">
                                <input name="loadCssResourceOnInitialization" type="checkbox" id="loadCssResourceOnInitialization" value="1" <?php echo $loadCssResourceOnInitialization ? 'checked' : ''; ?>>
                                <?php _e('Load the CSS resource on initialization.', 'mosparo-integration'); ?>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <p>
            <?php
                wp_nonce_field('mosparo-settings-form', 'save-settings');
                submit_button(null, 'primary', 'submit', false);

                if ($configHelper->isActive()):
            ?>
                <a href="<?php echo wp_nonce_url($this->buildConfigPageUrl(['action' => 'reset']), 'reset-connection'); ?>" class="button-secondary">
                    <?php _e('Reset connection', 'mosparo-integration'); ?>
                </a>
                <a href="<?php echo wp_nonce_url($this->buildConfigPageUrl(['action' => 'refresh_css_cache']), 'refresh-css-cache'); ?>" class="button-secondary">
                    <?php _e('Refresh CSS Cache', 'mosparo-integration'); ?>
                </a>
            <?php endif; ?>
        </p>
    </form>
    <br />
    <div>
        <h2><?php _e('Modules', 'mosparo-integration'); ?></h2>

        <form method="post">
            <input type="hidden" name="page" value="">
            <?php
                $moduleTable = new \MosparoIntegration\Admin\ModuleListTable();
                $moduleTable->prepare_items();

                $moduleTable->display();
            ?>
        </form>
    </div>
</div>