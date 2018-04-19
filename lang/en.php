<?php
$lang = array();


/*Generic*/
$lang['version'] = "0.0.1";
$lang['title'] = "TMS - VTC Management System";
$lang['description'] = "An open source project for managing VTCs! Requires a web host with PHP 7.0 and at least 1 SQL Database.";
$lang['en_only'] = "Available only in English";
$lang['steamID'] = "SteamID";
$lang['steam_id'] = "Steam id";


/*Home Page*/
$lang['welcome_to'] = "Welcome to";
$lang['Quick_VTC_Statistics'] = "Quick VTC Statistics";

/*Header*/
$lang['statistics'] = "Statistics";
$lang['driving_record'] = "Driving Record";
$lang['view_jobs'] = "View Jobs";
$lang['account_settings'] = "Account Settings";
$lang['logout'] = "Logout";
$lang['administration'] = "Administration";
$lang['edit_jobs'] = "Edit Jobs";
$lang['edit_users'] = "Manage Drivers";
$lang['edit_VTC_settings'] = "VTC Settings";

/*Login Page*/
$lang['sign_in_through_steam'] = "Sign in through Steam by clicking the image below.";
$lang["sign_in_through_steam_img"] = "Sign in through Steam.";
$lang['register_through_steam'] = "Register with Steam by clicking the image below.";

/*Logout Page*/
$lang['sits_logout_success'] = "You have been logged out successfully.";

/*Register User*/
$lang['user_register_error'] = "There was an error creating your profile.";
$lang['user_register_success'] = "Your profile has been created, however, a member from the management team needs to approve your account.";

/*Footer*/
$lang['footer_madeBy'] = "Made by Josh Menzel";
$lang['footer_css'] = "Using the <a href=\"http://metroui.org.ua\">Metro UI CSS Library</a>";

/*Edit VTC Settings*/
$lang['VTCName'] = "VTC Name: ";
$lang['VTCSlogan'] = "VTC Slogan: ";
$lang['VTCUrl'] = "VTC URL: ";
$lang['VTCId'] = "VTC ID: ";
$lang['VTCRegistrationOpen'] = "Registration Open: ";
$lang['VTCDBHost'] = "Database Host: ";
$lang['VTCDBUser'] = "Database User: ";
$lang['VTCDBPassword'] = "Database Password: ";
$lang['VTCDBName'] = "Database Name: ";
$lang['VTCSteamAPIKey'] = "Steam Web API Key: ";

/*Profile Page*/
$lang['profile_username'] = "Username: ";
$lang['profile_api_link'] = "VTC API Link: ";
$lang['profile_logger_key'] = "Your logger key: ";

/*Permission Errors*/
$lang['permission_denied'] = "Your account does not have permission to view this page.";

/*Stats*/
$lang['TotalDeliveries'] = "Total Deliveries";
$lang['TotalIncome'] = "Total Income";
$lang['TotalExpenses'] = "Total Expenses";
$lang['TotalDistanceDriven'] = "Total Distance Driven";
$lang['GameETS'] = "ETS 2";
$lang['GameATS'] = "ATS";
$lang['company_stats_text'] = "Company Statistics";
$lang['company_stats_description'] = "Below are the stats of the company.";

/* Generic/Sign in through Steam(SITS) Errors*/
$lang['error'] = "Error";
$lang['success'] = "Success";
$lang['err_unknown'] = "An unknown error has ocurred.";
$lang['err_1'] = "Your setup directory still exists. Please delete or rename it to continue.";
$lang['sits_general_error'] = "Oops! It looks like there was an error signing you in through Steam.";
$lang['sits_logout_failed'] = "Oops! There was a problem signing you out. Please try again later.";
$lang['sits_user_not_found'] = "Account not found.";
$lang['sits_user_Pending'] = "Your application is still pending and hasn't been reviewed by management as of yet.";
$lang['sits_user_LOA'] = "You have requested a Leave of Abscence(LOA). You MUST contact management to be reinstated.";
$lang['sits_user_Retired'] = "You have been retired from your position. Thank you for your service to this company!";
$lang['sits_user_Banned'] = "You have been banned from this company. Contact management for further assistance.";
$lang['sits_user_Suspended'] = "You have been suspended from this company. This is only temporary and you should contact management for further assistance.";
$lang['sits_user_Rejected'] = "Your application has been rejected. Please try applying again 30 days after your original application date.";
$lang['sits_user_InActive'] = "Your account has been marked inactive by a member of the management team. Contact them for more information.";
$lang['sits_register_user_exists'] = "This Steam account has already been registered. If you feel this was a mistake, please contact your VTC Management Team.";
?>