<?php if (!defined('ABSPATH')) exit;?>
<div class="wrap" id="ocambaPluginWrap">
    <h1>
        <?php esc_html_e('Ocamba Hood Settings', 'ocamba-hood');?>
    </h1>
    <h2>
        <a href="https://docs.ocamba.com/guides/hood/plugins/ocamba-hood-wordpress-plugin/" target="_blank">
            <?php esc_html_e('Documentation', 'ocamba-hood');?>
        </a>
    </h2>
    <div id="responseHolder"></div>
    <table class="form-table">
        <thead>
            <tr>
                <th scope="col">
                    <h2 class="m0 p0">
                        <?php esc_html_e('Code Key', 'ocamba-hood');?>
                    </h2>
                </th>
                <th scope="col">
                    <h2 class="m0 p0">
                        <?php esc_html_e('ACTIVATE/DEACTIVATE', 'ocamba-hood');?>
                    </h2>
                </th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td data-label="Code Key" class="w-100 formSubmitCodeKey">
                    <form method="POST" id="ocamba-hood-settings-form" action="<?php echo esc_html(admin_url('admin-ajax.php')) ?>">
                        <?php wp_nonce_field('ocamba_hood_options_verify');?>
                        <div class="df aic g1rem formSubmitCodeKeyInnerDiv">
                            <input
                                type="text"
                                class="w-100 t0dot3s"
                                name="code_key"
                                id="code_key"
                                data-present-code-key="<?php echo esc_html($macros["CODE_KEY"]); ?>"
                                value="<?php echo esc_html($macros["CODE_KEY"]); ?>" <?php echo esc_html($macros["CODE_KEY_ACTIVE"] != 'true' ? "disabled" : ""); ?>
                            >
                            <p class="mobileShow formSubmitCodeKeyMobileText">
                                <em>
                                    <b>
                                        <?php esc_html_e('Add your Ocamba Hood Code Key from Code Key field in settings on Ocamba -> Hood.', 'ocamba-hood');?>
                                    </b>
                                </em>
                            </p>
                            <input
                                type="submit"
                                value="<?php esc_html_e('Save Changes', 'ocamba-hood');?>"
                                class="button button-primary"
                                id="submit"
                                name="submit"
                                <?php echo esc_html($macros["CODE_KEY_ACTIVE"] != ($macros["CODE_KEY"] ? 'disabled' : 'true') ? "disabled" : ""); ?>
                            >
                        </div>
                        <p class="mobileHide">
                            <em><b><?php esc_html_e('Add your Ocamba Hood Code Key from Code Key field in settings on Ocamba -> Hood.', 'ocamba-hood');?></b></em>
                        </p>

                    </form>
                </td>

                <td data-label="ACTIVATE/DEACTIVATE" class="tac fomrActivateCodeKey">
                    <form method="POST" id="ocamba-hood-settings-form-switch" action="<?php echo esc_html(admin_url('admin-ajax.php')) ?>">
                        <?php wp_nonce_field('ocamba_hood_options_verify_activate_deactivate');?>
                        <label class="switch">
                            <input
                                id="activateAndDeactivateCodeKey"
                                name="activateAndDeactivateCodeKey"
                                type="checkbox"
                                <?php echo esc_html($macros["CODE_KEY_ACTIVE"] == 'true' ? "checked" : ""); ?>
                                <?php echo esc_html($macros["CODE_KEY"] == '' ? 'disabled' : '' ) ?>
                            >
                            <span class="slider round <?php echo esc_html($macros["CODE_KEY"] == '' ? 'disabledSwitch' : '' ) ?>"></span>
                        </label>
                    </form>
                </td>
            </tr>
        </tbody>
    </table>

    <dialog id="loadingDialog">

        <div class="ripple">
            <div></div>
            <div></div>
        </div>

    </dialog>

</div>