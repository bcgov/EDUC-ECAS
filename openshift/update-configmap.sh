###########################################################
#ENV VARS
###########################################################
APP_NAME=$1
NAMESPACE=$2
APP_DEBUG=$3
DOCUMENTROOT=$4
DYNAMICSBASEURL=$5

ASPNETCORE_ENVIRONMENT=$6
DYNAMICSAUTHENTICATIONSETTINGS__ACTIVEENVIRONMENT=$7
DYNAMICSAUTHENTICATIONSETTINGS__CLOUDWEBAPIURL=$8
DYNAMICSAUTHENTICATIONSETTINGS__CLOUDREDIRECTURL=$9
DYNAMICSAUTHENTICATIONSETTINGS__CLOUDRESOURCEURL=${10}
DYNAMICSAUTHENTICATIONSETTINGS__CLOUDBASEURL=${11}

ASPNETCORE_ENVIRONMENT_CAS=${12}
CAS_API_SERVER=${13}
CAS_TOKEN_URI=${14}
CAS_INVOICE_URI=${15}

###########################################################
#Setup for config-maps
###########################################################
echo Creating config map "$APP_NAME"-config-map
echo "$NAMESPACE"
oc create   configmap ecas-config-map \
  --from-literal=APP_DEBUG="$APP_DEBUG" \
  --from-literal=DOCUMENTROOT="$DOCUMENTROOT" \
  --from-literal=DYNAMICSBASEURL="$DYNAMICSBASEURL" \
  --from-literal=ASPNETCORE_ENVIRONMENT="$ASPNETCORE_ENVIRONMENT" \
  --from-literal=DynamicsAuthenticationSettings__ActiveEnvironment="$DYNAMICSAUTHENTICATIONSETTINGS__ACTIVEENVIRONMENT" \
  --from-literal=DynamicsAuthenticationSettings__CloudWebApiUrl="$DYNAMICSAUTHENTICATIONSETTINGS__CLOUDWEBAPIURL" \
  --from-literal=DynamicsAuthenticationSettings__CloudRedirectUrl="$DYNAMICSAUTHENTICATIONSETTINGS__CLOUDREDIRECTURL" \
  --from-literal=DynamicsAuthenticationSettings__CloudResourceUrl="$DYNAMICSAUTHENTICATIONSETTINGS__CLOUDRESOURCEURL" \
  --from-literal=DynamicsAuthenticationSettings__CloudBaseUrl="$DYNAMICSAUTHENTICATIONSETTINGS__CLOUDBASEURL" \
  --from-literal=ASPNETCORE_ENVIRONMENT_CAS="$ASPNETCORE_ENVIRONMENT_CAS" \
  --from-literal=CAS_API_SERVER="$CAS_API_SERVER" \
  --from-literal=CAS_TOKEN_URI="$CAS_TOKEN_URI" \
  --from-literal=CAS_INVOICE_URI="$CAS_INVOICE_URI" \
  --dry-run=client -o yaml | oc apply -f -

echo Creating config map "$APP_NAME"-flb-sc-config-map
oc create  configmap ecas-flb-sc-config-map \
  --from-literal=fluent-bit.conf="$FLB_CONFIG" \
  --from-literal=parsers.conf="$PARSER_CONFIG" \
  --dry-run=client -o yaml | oc apply -f -
