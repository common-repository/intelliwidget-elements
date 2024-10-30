
<div class="notice-warning notice is-dismissible">
    <h1>IntelliWidget Upgrade - Important Notices</h1>
    <p>The new "Elements" version combines features from the free and "Pro" versions and requires changes to some of the options stored in the database.</p>
    <p><strong>Only Use the button below to remove the original IntelliWidget plugins.</strong></p>
    <p>Do not use the "delete" links to remove the original IntelliWidget plugins or you will lose IntelliWidget configuration data.</strong></p>
<p><strong>IntelliWidget no longer uses the template-tags.php global functions file.</strong> If you have installed any custom templates in your themes, they will no longer work. We recommend you deactivate this plugin and continue to use the original IntelliWidget until you have a chance to migrate your templates to the new system.</p>
<p><strong>All date-based features have been removed in favor of a more general "meta condition" query feature.</strong> This includes date-based templates.</p>
    <h1>Multi-Site Users:</h1>
    <p>The new "Elements" version retrieves ALL TERMS (such as "Categories" and "Tags") from the primary site so they are shared across all sites.</p>
    <p>New features were designed for Network Sites that are related (such as universities and large companies).</p>
    <p><strong>Do not use this plugin for multi-site WordPress if sites must be independent of each other.</strong></p>
    <form action="" method="post">
        <p><strong>Important</strong> If you do not wish to upgrade, click "Cancel and Deactivate." Otherwise, click "Upgrade and remove original Plugins" to install the new version.</p>
        <p><input type="submit" name="iw_init_deactivate" value="Cancel and Deactivate" class="button secondary" />
        <input type="submit" name="iw_init_upgrade" value="Upgrade and remove original Plugins" class="button primary" /></h1>
<?php wp_nonce_field( 'iw_install_option', 'iw_init_nonce' ); ?></p>
    </form>
</div>
