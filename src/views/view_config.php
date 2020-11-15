<div id="password-policy-admin">
    <h1><?php _e('Password Policy Config Page', 'password-policy'); ?></h1>

    <form action="" method="POST" onsubmit="pssp_encode_b64()">
        <div>
            <label for="weak-password"><?php _e('Disable Weak password for wordpress', 'password-policy'); ?></label>
            <input type="checkbox" id="weak-password" name="weak-password" value="true" <?php if ($data_config['weak-password']) { ?> checked <?php } ?>>
        </div>
        <div>
            <label for="number-characters"><?php _e('Number of characters min', 'password-policy'); ?></label>
            <input type="number" id="number-characters" name="number-characters" value="<?php _e(intval($data_config['number-characters'])) ?>" >
        </div>
        <div>
            <label for="enable-special"><?php _e('Add special character', 'password-policy'); ?></label>
            <input type="checkbox" id="enable-special" name="enable-special" value="true" <?php if ($data_config['enable-special']) { ?> checked <?php } ?>>
        </div>
        <div>
            <label for="enable-number"><?php _e('Add number characters', 'password-policy'); ?></label>
            <input type="checkbox" id="enable-number" name="enable-number" value="true" <?php if ($data_config['enable-number']) { ?> checked <?php } ?>>
        </div>
        <div>
            <label for="enable-uppercase"><?php _e('Need a uppercase character', 'password-policy'); ?></label>
            <input type="checkbox" id="enable-uppercase" name="enable-uppercase" value="true" <?php if ($data_config['enable-uppercase']) { ?> checked <?php } ?>>
        </div>
        <div>
            <label for="regex-password-option"><?php _e('The regex how match with the password (advance mode)', 'password-policy'); ?></label>
            <input type="checkbox" id="regex-password-option" name="regex-password-option" value="true" <?php if ($data_config['regex-password-option']) { ?> checked <?php } ?>>
            
            <div class="regex-zone">
                <input type="text" id="regex-password" value="<?php _e(base64_decode($data_config['regex-password'])) ?>" name="regex-password">
            </div>
        </div>

        <div class="submit-zone">
            <input type="hidden" name="token" id="token" value="<?php _e($token->display_token()); ?>" />
            <input type="hidden" name="securite_nonce" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
            <input type="submit" value="<?php _e('Save', 'password-policy'); ?>">
        </div>

        <div id="password-policy-admin-astuce">
            <h2><?php _e('Documentation', 'password-policy'); ?></h2>
            <a href="<?php _e($url_doc); ?>" rel="nofollow noopener noreferrer" target="_blank"><?php _e('Understood what a regex', 'password-policy'); ?></a>
        </div>
    </form>

    <script>
        function pssp_encode_b64() {
            document.getElementById("regex-password").value = window.btoa(document.getElementById("regex-password").value);
        }
    </script>
</div>
