###########################################################
#ENV VARS
###########################################################
REPO_NAME=$1
APP_DEBUG=$2
DOCUMENTROOT=$3
DYNAMICSBASEURL=$4

ASPNETCORE_ENVIRONMENT=$5
DYNAMICSAUTHENTICATIONSETTINGS__ACTIVEENVIRONMENT=$6
DYNAMICSAUTHENTICATIONSETTINGS__CLOUDWEBAPIURL=$7
DYNAMICSAUTHENTICATIONSETTINGS__CLOUDREDIRECTURL=$8
DYNAMICSAUTHENTICATIONSETTINGS__CLOUDRESOURCEURL=$9
DYNAMICSAUTHENTICATIONSETTINGS__CLOUDBASEURL=${10}

ASPNETCORE_ENVIRONMENT_CAS=${11}
CAS_API_SERVER=${12}
CAS_TOKEN_URI=${13}
CAS_INVOICE_URI=${14}

###########################################################
#Setup for config-maps
###########################################################
echo Creating config map "$REPO_NAME"-config-map
oc create   configmap "$REPO_NAME"-config-map \
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

echo Creating config map "$REPO_NAME"-flb-sc-config-map
oc create  configmap "$REPO_NAME"-flb-sc-config-map \
  --from-literal=fluent-bit.conf="$FLB_CONFIG" \
  --from-literal=parsers.conf="$PARSER_CONFIG" \
  --dry-run=client -o yaml | oc apply -f -
