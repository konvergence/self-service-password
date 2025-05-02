#! /bin/bash -e

export SCRIPT_DIR=${SCRIPT_DIR:-/etc/service/apache2}


if [ "$1" = '--run' ]; then

. ${SCRIPT_DIR}/functions

configure_vhost_subdir

! test -f /run/apache2/apache2.pid || rm /run/apache2/apache2.pid
apache2ctl -D FOREGROUND

elif [ "$1" = '--help' ]; then
  cat /usr/share/self-service-password/USAGE.md

elif [ "$1" = '--checkexpiration' ]; then

   # get uid of ${LDAP_BINDDN}
   regex=${LSSP_ATTR_LOGIN}'=([^,]+),.+'
   [[ ${LDAP_BINDDN} =~ ${regex} ]] && login=${BASH_REMATCH[1]}

   if [ -z ${login} ]; then
      echo Error : can not get ${LSSP_ATTR_LOGIN} from ${LDAP_BINDDN};
   else
       echo execute curl on "http://$HOSTNAME:80/${VIRTUAL_SUBDIR}/?action=checkexpiration"
       curl -F login=${login} -F password=${LDAP_BINDPWD} "http://$HOSTNAME:80/${VIRTUAL_SUBDIR}/?action=checkexpiration" ;
   fi

fi