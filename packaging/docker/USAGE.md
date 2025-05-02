# display help
```shell
docker run --rm konvergence/ltb-ssp --help
```


# run self service password on openldap
- the ldap must configured with a default ppolicy, a service account 'lssp' member of a group admin with write acl

```shell
docker run --name ltbself \
      -p 80:80 \
      -v ltbself-config:/usr/share/self-service-password/conf \
      -v ltbself-images:/usr/share/self-service-password/images \
      -e VIRTUAL_SUBDIR='lssp' \
      -e LSSP_EXTERNAL_URL='http://localhost/lssp' \
      -e LDAP_BASE='dc=example,dc=ldap' \
      -e LDAP_BINDDN='uid=lssp,ou=services,ou=users,dc=example,dc=ldap' \
      -e LDAP_BINDPWD='lsspPassword' \
      -e LDAP_DEFAULTPWDPOLICYDN='cn=default,ou=policies,dc=example,dc=ldap' \
      -e LDAP_ADMINGROUPDN='cn=administrators,ou=groups,dc=example,dc=ldap' \
      -e LDAP_URL='ldap:389' \
      -e LSSP_HASH_METHOD='clear' \
      -e LSSP_MAIL_SUPPORT='true' \
      -e LSSP_PHRASE_KEY='MySuperSecretPhrase' \
      -e SMTP_FROM='selfpassword@example.ldap' \
      -e SMTP_HOST='yoursmtphost' \
      -e SMTP_PORT='587' \
      -e SMTP_SECURE='tls' \
      -e SMTP_AUTH='true' \
      -e SMTP_USER='selfpassword@example.ldap' \
      -e SMTP_PASS='yoursmtppassword' \
      -e VIRTUAL_SUBDIR='lssp' \
      -e RECAPTCHA_USE='false' \
      -e RECAPTCHA_PUB_KEY='thepublickey' \
      -e RECAPTCHA_PRV_KEY='theprivatekey' \
      -e RECAPTCHA_THEME=white \
      -e RECAPTCHA_SSL=false \
       konvergence/ltb-ssp \
       --run
```

# run batch email for expired password or in warning periode
```shell
docker exec ltbself //etc/service/apache2/entrypoint.sh --checkexpiration
```

# example of openldap configuration

#### config-memberof.ldif
```shell
# LDIF Export

version: 1

#dn: cn=module{1},cn=config
#cn: module{1}
#objectClass: olcModuleList
#olcModuleLoad: memberof
#olcmoduleload: refint
#olcModulePath: /usr/lib/ldap

#dn: olcOverlay={1}refint,olcDatabase={1}hdb,cn=config
#objectclass: olcOverlayConfig
#objectclass: olcRefintConfig
#olcoverlay: {1}refint
#olcrefintattribute: owner
#olcrefintattribute: manager
#olcrefintattribute: uniqueMember
#olcrefintattribute: member
#olcrefintattribute: memberOf

#dn: olcOverlay={0}memberof,olcDatabase={1}hdb,cn=config
#objectclass: olcOverlayConfig
#objectclass: olcMemberOf
#olcmemberofdangling: ignore
#olcmemberofgroupoc: groupOfUniqueNames
#olcmemberofmemberad: uniqueMember
#olcmemberofmemberofad: memberOf
#olcmemberofrefint: TRUE
#olcoverlay: {0}memberof

dn: olcOverlay={3}memberof,olcDatabase={1}hdb,cn=config
objectclass: olcOverlayConfig
objectclass: olcMemberOf
olcmemberofdangling: ignore
olcmemberofgroupoc: groupOfNames
olcmemberofmemberad: member
olcmemberofmemberofad: memberOf
olcmemberofrefint: TRUE
olcoverlay: {3}memberof
```

#### config-ppolicy.ldif
```shell
# LDIF Export

version: 1

# Force default HASH on OpenLDAP side
dn: olcDatabase={-1}frontend,cn=config
changetype: modify
add: olcPasswordHash
olcPasswordHash: {CRYPT},{SHA},{MD5}

# add ppolicy overlay
dn: cn=module{1},cn=config
cn: module{1}
objectClass: olcModuleList
olcModuleLoad: ppolicy
olcModulePath: /usr/lib/ldap


# fix default policy DN
dn: olcOverlay={2}ppolicy,olcDatabase={1}hdb,cn=config
objectclass: olcOverlayConfig
objectClass: olcPpolicyConfig
olcOverlay: {2}ppolicy
olcPPolicyDefault: cn=default,ou=policies,${LDAP_BASE_DN}
olcPPolicyUseLockout: TRUE
olcPPolicyHashCleartext: TRUE
```

#### add-ppolicy.ldif
```shell
# LDIF Export

version: 1

dn: ou=policies,${LDAP_BASE_DN}
objectclass: organizationalUnit
objectclass: top
ou: policies


# default policy 
dn: cn=default,ou=policies,${LDAP_BASE_DN}
objectClass: top
objectClass: device
objectClass: pwdPolicy
cn: default
pwdAttribute: userPassword
pwdMaxAge: 7776000
pwdMinAge: 900
pwdExpireWarning: 864000
pwdInHistory: 4
pwdMinLength: 8
pwdMaxFailure: 5
pwdLockout: TRUE
pwdLockoutDuration: 900
pwdGraceAuthNLimit: 5
pwdFailureCountInterval: 0
pwdMustChange: TRUE
pwdAllowUserChange: TRUE
pwdSafeModify: FALSE
pwdCheckQuality: 1

# service account policy
dn: cn=servicesaccounts,ou=policies,${LDAP_BASE_DN}
cn: servicesaccounts
objectClass: top
objectClass: device
objectClass: pwdPolicy
pwdAllowUserChange: TRUE
pwdAttribute: userPassword
pwdExpireWarning: 0
pwdFailureCountInterval: 0
pwdGraceAuthNLimit: 5
pwdLockout: FALSE
pwdLockoutDuration: 0
pwdInHistory: 0
pwdMaxAge: 0
pwdMaxFailure: 0
pwdMinAge: 0
pwdMinLength: 15
pwdMustChange: FALSE
pwdSafeModify: FALSE
```

#### add-ous-groups.ldif
```shell
# LDIF Export

version: 1

dn: ou=groups,${LDAP_BASE_DN}
objectclass: organizationalUnit
objectclass: top
ou: groups

dn: ou=users,${LDAP_BASE_DN}
objectclass: organizationalUnit
objectclass: top
ou: users

dn: ou=services,ou=users,${LDAP_BASE_DN}
objectclass: organizationalUnit
objectclass: top
ou: users

dn: uid=${LSSP_SERVICE_USER},ou=services,ou=users,${LDAP_BASE_DN}
objectclass: top
objectclass: person
objectclass: organizationalPerson
objectclass: inetOrgPerson
uid: ${LSSP_SERVICE_USER}
cn: ${LSSP_SERVICE_USER}
mail: ${SMTP_FROM}
sn: ${LSSP_SERVICE_USER}
userpassword: ${LSSP_SERVICE_PASSWORD}
pwdPolicySubEntry: cn=servicesaccounts,ou=policies,${LDAP_BASE_DN}

dn: cn=administrators,ou=Groups,${LDAP_BASE_DN}
cn: administrators
description: Administrators
objectclass: groupOfUniqueNames
objectclass: top
uniquemember: uid=${LSSP_SERVICE_USER},ou=services,ou=users,${LDAP_BASE_DN}
```

#### config-olcaccess.ldif
```shell
# LDIF Export

version: 1

dn: olcDatabase={1}hdb,cn=config
changetype: modify
replace: olcAccess
olcAccess: {0}to attrs=userPassword,shadowLastChange by self write by anonymous auth by dn="cn=admin,${LDAP_BASE_DN}" write by group/groupOfUniqueNames/uniqueMember="cn=administrators,ou=groups,${LDAP_BASE_DN}" write by * none
olcAccess: {1}to dn.base="" by users read by anonymous auth by * none
olcAccess: {2}to dn.base="${LDAP_BASE_DN}" by users read by anonymous auth by * none
olcAccess: {3}to dn.base="ou=users,${LDAP_BASE_DN}" attrs=uid by self write by users read by anonymous read
olcAccess: {4}to * by self write by dn="cn=admin,${LDAP_BASE_DN}" write by group/groupOfUniqueNames/uniqueMember="cn=administrators,ou=groups,${LDAP_BASE_DN}" write by * read
```