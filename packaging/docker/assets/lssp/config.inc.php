<?php
#==============================================================================
# LTB Self Service Password
#
# Copyright (C) 2009 Clement OUDOT
# Copyright (C) 2009 LTB-project.org
#
# This program is free software; you can redistribute it and/or
# modify it under the terms of the GNU General Public License
# as published by the Free Software Foundation; either version 2
# of the License, or (at your option) any later version.
#
# This program is distributed in the hope that it will be useful,
# but WITHOUT ANY WARRANTY; without even the implied warranty of
# MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
# GNU General Public License for more details.
#
# GPL License: http://www.gnu.org/licenses/gpl.txt
#
#==============================================================================

#==============================================================================
# Configuration
#==============================================================================
# LDAP
$ldap_url = getenv('LDAP_URL');
$ldap_starttls = false;
$ldap_binddn = getenv('LDAP_BINDDN');
$ldap_bindpw = getenv('LDAP_BINDPWD');
$ldap_base = getenv('LDAP_BASE');
$ldap_login_attribute = getenv('LSSP_ATTR_LOGIN');
$ldap_fullname_attribute = getenv('LSSP_ATTR_FN');
$ldap_filter = "(&(objectClass=person)($ldap_login_attribute={login}))";

# Active Directory mode
# true: use unicodePwd as password field
# false: LDAPv3 standard behavior
$ad_mode = false;
# Force account unlock when password is changed
$ad_options['force_unlock'] = false;
# Force user change password at next login
$ad_options['force_pwd_change'] = false;
# Allow user with expired password to change password
$ad_options['change_expired_password'] = false;

# Samba mode
# true: update sambaNTpassword and sambaPwdLastSet attributes too
# false: just update the password
$samba_mode = false;
# Set password min/max age in Samba attributes
#$samba_options['min_age'] = 5;
#$samba_options['max_age'] = 45;

# Shadow options - require shadowAccount objectClass
# Update shadowLastChange
$shadow_options['update_shadowLastChange'] = false;

# Hash mechanism for password:
# SSHA
# SHA
# SMD5
# MD5
# CRYPT
# clear (the default)
# auto (will check the hash of current password)
# This option is not used with ad_mode = true
$hash = "clear";

# Prefix to use for salt with CRYPT
$hash_options['crypt_salt_prefix'] = "$6$";

# Local password policy
# This is applied before directory password policy
# Minimal length
$pwd_min_length = 0;
# Maximal length
$pwd_max_length = 0;
# Minimal lower characters
$pwd_min_lower = 0;
# Minimal upper characters
$pwd_min_upper = 0;
# Minimal digit characters
$pwd_min_digit = 0;
# Minimal special characters
$pwd_min_special = 0;
# Definition of special characters
$pwd_special_chars = "^a-zA-Z0-9";
# Forbidden characters
#$pwd_forbidden_chars = "@%";
# Don't reuse the same password as currently
$pwd_no_reuse = true;
# Check that password is different than login
$pwd_diff_login = true;
# Complexity: number of different class of character required
$pwd_complexity = 0;
# Show policy constraints message:
# always
# never
# onerror
$pwd_show_policy = "onerror";
# Position of password policy constraints message:
# above - the form
# below - the form
$pwd_show_policy_pos = "above";

# Who changes the password?
# Also applicable for question/answer save
# user: the user itself
# manager: the above binddn
$who_change_password = "user";

## Standard change
# Use standard change form?
$use_change = true;

## Questions/answers
# Use questions/answers?
# true (default)
# false
$use_questions = false;

# Answer attribute should be hidden to users!
$answer_objectClass = "extensibleObject";
$answer_attribute = "info";

# Extra questions (built-in questions are in lang/$lang.inc.php)
#$messages['questions']['ice'] = "What is your favorite ice cream flavor?";

## Token
# Use tokens?
# true (default)
# false
$use_tokens = (getenv('LSSP_MAIL_SUPPORT') === 'true') ? true : false ;


# Crypt tokens?
# true (default)
# false
$crypt_tokens = true;
# Token lifetime in seconds
$token_lifetime = "3600";

## Mail
# LDAP mail attribute
$mail_attribute =  getenv('LSSP_ATTR_MAIL');



# Who the email should come from
$mail_from =  getenv('SMTP_FROM');
#$mail_from_name = "Self Service Password";

# Notify users anytime their password is changed
$notify_on_change = false;


# PHPMailer configuration (see https://github.com/PHPMailer/PHPMailer)
#$mail_sendmailpath = '/usr/sbin/sendmail';
$mail_protocol = 'smtp';
$mail_smtp_debug = 0;
$mail_debug_format = 'html';
$mail_smtp_host = getenv('SMTP_HOST');
$mail_smtp_auth = (getenv('SMTP_AUTH') === 'true') ? true : false ;
$mail_smtp_user = getenv('SMTP_USER');
$mail_smtp_pass = getenv('SMTP_PASS');
$mail_smtp_port = getenv('SMTP_PORT');
$mail_smtp_timeout = 30;
$mail_smtp_keepalive = false;
$mail_smtp_secure = getenv('SMTP_SECURE');
$mail_contenttype = 'text/plain';
$mail_charset = 'utf-8';
$mail_priority = 3;
$mail_newline = PHP_EOL;

## SMS
# Use sms
$use_sms = false;
# GSM number attribute
$sms_attribute = "mobile";
# Partially hide number
$sms_partially_hide_number = true;
# Send SMS mail to address
$smsmailto = "{sms_attribute}@service.provider.com";
# Subject when sending email to SMTP to SMS provider
$smsmail_subject = "Provider code";
# Message
$sms_message = "{smsresetmessage} {smstoken}";

# SMS token length
$sms_token_length = 6;

# Max attempts allowed for SMS token
$max_attempts = 3;

# Reset URL (if behind a reverse proxy)
#$reset_url = $_SERVER['HTTP_X_FORWARDED_PROTO'] . "://" . $_SERVER['HTTP_X_FORWARDED_HOST'] . $_SERVER['SCRIPT_NAME'];
#$reset_url = $_SERVER['HTTP_X_FORWARDED_PROTO'] . "://" .  $_SERVER['SERVER_NAME'] . ":" .  $_SERVER['HTTP_X_FORWARDED_PORT'] .  $_SERVER['SCRIPT_NAME'];
if( getenv('LSSP_EXTERNAL_URL')) { $reset_url = getenv('LSSP_EXTERNAL_URL'); }

# Display help messages
$show_help = true;

# Language
$lang ="en";

# Display menu on top
$show_menu = true;

# Logo
$logo = "images/ltb-logo.png";

# Background image
#$background_image = "images/unsplash-space.jpeg";

# Debug mode
$debug = true;

# Encryption, decryption keyphrase
$keyphrase = getenv('LSSP_PHRASE_KEY');

# Where to log password resets - Make sure apache has write permission
# By default, they are logged in Apache log
#$reset_request_log = "/var/log/self-service-password";

# Invalid characters in login
# Set at least "*()&|" to prevent LDAP injection
# If empty, only alphanumeric characters are accepted
$login_forbidden_chars = "*()&|";

## CAPTCHA
# Use Google reCAPTCHA (http://www.google.com/recaptcha)
$use_recaptcha = (getenv('RECAPTCHA_USE') === 'true') ? true : false ;
# Go on the site to get public and private key
$recaptcha_publickey = getenv('RECAPTCHA_PUB_KEY');
$recaptcha_privatekey = getenv('RECAPTCHA_PRV_KEY');
# Customization (see https://developers.google.com/recaptcha/docs/display)
$recaptcha_theme = getenv('RECAPTCHA_THEME');
$recaptcha_type = "image";
$recaptcha_size = "normal";

## Default action
# change
# sendtoken
# sendsms
$default_action =  getenv('LSSP_DEFAULT_ACTION'); 


## Extra messages
# They can also be defined in lang/ files
#$messages['passwordchangedextramessage'] = NULL;
#$messages['changehelpextramessage'] = NULL;

# Launch a posthook script after successful password change
#$posthook = "/usr/share/self-service-password/posthook.sh";

## show ldap extended error
$show_extented_ldap_error=true;


## config for checkexpiration batch
$use_checkexpiration = true;
$ldap_defaultpolicydn=getenv('LDAP_DEFAULTPWDPOLICYDN');
$ldap_admingroupdn=getenv('LDAP_ADMINGROUPDN');
$expire_warning=1209600;

# if set false: then send mail, 1st day of warning, last day of warning and 1st day of expire
$expire_always_mail = true;

# message They can also be defined in lang/ files
$messages['emptyexpireform'] = "Checking password expiration for all users";
$messages["expirehelp"] = "Only administrator can run this page";
$messages['checkexpiration'] = "Check expiration of passwords";
$messages['expirechecked'] = "The password expiration check has been completed";
$messages['warningexpiresubject'] = "Warning - Your password will expired";
$messages['warningexpiremessage'] = "Hello {login},\n\nYour password will expired in {days} days.\nClick here to change your password:\n{url}\n\n";
$messages['alertexpiresubject'] = "Alert - Your password is expired";
$messages['alertexpiremessage'] = "Hello {login},\n\nYour password is expired since {days} days.\nClick here to reset your password:\n{url}\n\n";


?>
