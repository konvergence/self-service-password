FROM ubuntu:22.04
# Image Label
LABEL maintainer="support_shuttle@konvergence.com"

# dpkg no interactive mode
ENV DEBIAN_FRONTEND noninteractive

ENV LDAP_URL="ldap://ldap:389" \
    VIRTUAL_SUBDIR=lssp \
    LDAP_BINDDN="cn=lssp,dc=ldap,dc=example,dc=ldap" \
    LDAP_BINDPWD="changeme" \
    LDAP_BASE="dc=example,dc=ldap" \
    LDAP_DEFAULTPWDPOLICYDN='cn=default,ou=policies,dc=example,dc=ldap' \
    LDAP_ADMINGROUPDN='cn=administrators,ou=groups,dc=example,dc=ldap' \
    SMTP_HOST=smtp \
    SMTP_PORT=25 \
    SMTP_USER=smtpuser \
    SMTP_PASS=smtppass \
    SMTP_FROM="root@localhost" \
    SMTP_SECURE=tls \
    SMTP_AUTH=true \
    LSSP_PHRASE_KEY=secret \
    LSSP_MAIL_SUPPORT=false \
    LSSP_ATTR_LOGIN=uid \
    LSSP_ATTR_FN=cn \
    LSSP_ATTR_MAIL=mail \
    LSSP_HASH_METHOD=auto \
    LSSP_EXTERNAL_URL='http://localhost/lssp' \
    LSSP_DEFAULT_ACTION=change \
    RECAPTCHA_USE=false \
    RECAPTCHA_PUB_KEY=RECAPTCHA_PUB_KEY \
    RECAPTCHA_PRV_KEY=RECAPTCHA_PRV_KEY \
    RECAPTCHA_THEME=white \
    RECAPTCHA_SSL=false


# Install Apache2, PHP 8.1.0 cause mcrypt
RUN apt update &&  apt-get install -y software-properties-common python3-software-properties \
&&  add-apt-repository -y ppa:ondrej/php && apt update \
&&  apt-get install -y apache2  php8.1 php8.1-ldap php8.1-mcrypt curl gettext-base ldap-utils gawk \
&& apt-get clean \
&& rm -rf /var/lib/apt/lists/* \
&& rm -rf /tmp/*


# Add Tini
ARG TINI_VERSION="v0.19.0"
ADD https://github.com/krallin/tini/releases/download/${TINI_VERSION}/tini /tini
RUN chmod +x /tini


RUN curl https://ltb-project.org/archives/self-service-password_1.0-2_all.deb > self-service-password.deb && dpkg -i self-service-password.deb ; rm -f self-service-password.deb

# Configure self-service-password site
## /etc/php/8.1/mods-available/mcrypt.ini ever exist
##RUN ln -s ../../mods-available/mcrypt.ini /etc/php/8.1/apache2/conf.d/20-mcrypt.ini

RUN a2dissite 000-default && a2ensite self-service-password




# change in pages to display ldap_extended_error, checkexpiration, reset_url automatic build from HTTP_X_FORWARDED_PROTO/HOST/PORT 
RUN curl -s -L https://github.com/konvergence/self-service-password/archive/v1.1-beta.3.tar.gz -o /tmp/v1.1-beta.3.tar.gz \
&&  cd /tmp; tar -zxvf /tmp/v1.1-beta.3.tar.gz \
&&  cp -rf /tmp/self-service-password-1.1-beta.3/* /usr/share/self-service-password/ \
&& rm -rf /tmp/self-service-password-1.1-beta.3/ /tmp/v1.1-beta.3.tar.gz 


# This is where configuration goes coming from https://raw.githubusercontent.com/konvergence/self-service-password/v1.1-beta.3/conf/config.inc.php
ADD assets/lssp/config.inc.php /usr/share/self-service-password/conf/config.inc.php


# change logo
COPY assets/images/ /usr/share/self-service-password/images/

# add vhost template
ADD ["assets/apache2/self-service-password.conf.dist", "/etc/apache2/sites-available/self-service-password.conf.dist"]

# add usage.md
COPY USAGE.md /usr/share/self-service-password

# make a run foreground for apache
# Add bin
COPY assets/scripts/ /etc/service/apache2/
RUN chmod +x /etc/service/apache2/*


# Entrypoint and CMD
ENTRYPOINT ["/tini", "--", "bash", "/etc/service/apache2/entrypoint.sh" ]
CMD ["--run"]

EXPOSE 80
