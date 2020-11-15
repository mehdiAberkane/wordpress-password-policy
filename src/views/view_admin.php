<div id="password-policy-admin-user">
    <h1><?php _e('Password Policy reset page', 'password-policy'); ?></h1>
    <form action="" method="POST">
        <div class="all-reset">
            <input type="checkbox" name="full-reset" value="true" id="full-reset">
            <label for="full-reset"><?php _e('Reset all users Admin include ', 'password-policy'); ?></label>
        </div>
        <div class="role-zone">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th><?php _e('Roles', 'password-policy'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ( pssp_get_all_roles() as $role ) {
                            _e("<tr> <td><input id=".esc_html( $role )." type='checkbox' name='role_name[]' value=".esc_html( $role )."></td>");
                            _e('<td> <label for="' . esc_html( $role ) . '">' . esc_html( $role ) . '</label></td>');
                            _e("<tr/>");
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="user-zone">
            <table>
                <thead>
                    <tr>
                        <th></th>
                        <th>ID</th>
                        <th>User email</th>
                        <th>Username</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ( $users as $user ) {
                            _e("<tr> <td><input id=".esc_html( $user->id )." type='checkbox' name='users_id[]' value=".esc_html( $user->id )."></td>");
                            _e('<td><label for="' . esc_html( $user->id ) . '">' . esc_html( $user->id ) . '</label></td>');
                            _e('<td><label for="' . esc_html( $user->id ) . '">' . esc_html( $user->user_email ) . '</label></td>');
                            _e('<td><label for="' . esc_html( $user->id ) . '">' . esc_html( $user->display_name ) . '</label></td>');
                            _e("<tr/>");
                        }
                    ?>
                </tbody>
            </table>
        </div>

        <div class="submit-zone">
            <input type="hidden" name="token" id="token" value="<?php _e($token->display_token()); ?>" />
            <input type="hidden" name="securite_nonce" value="<?php echo wp_create_nonce('securite-nonce'); ?>"/>
            <input type="submit" value="<?php _e('Save', 'password-policy'); ?>">
        </div>
    </form>
</div>
