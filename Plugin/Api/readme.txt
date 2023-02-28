AddBasicCompanyFetchApiViaPlugin.php a basic wordpress api using a plugin.

1. Run the AddBasicCompanyFetchApiViaPlugin.php file in the root of your project folder. Type: php AddBasicCompanyFetchApiViaPlugin.php
2. Once run, zip the CompanyApi.php
3. Upload the zip folder in the plugin section of the wordpress admin.
4. Add the [display_company_api] in whichever page you want in the Wordpress section or if you are using php code add it in the page via: echo do_shortcode["display_company_api"]
5. Navigate to your page and you should see the info from the api in no special format.

If you don't want to add the api via a plugin, you can add the getCompany() function and the add_action function to the functions.php file.
