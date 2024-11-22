###########################################################
#ENV VARS
###########################################################
APP_NAME=$1
NAMESPACE=$2
APP_DEBUG=$3
DOCUMENTROOT=$4
DYNAMICSBASEURL=$5

###########################################################
#Setup for config-maps
###########################################################
echo Creating config map "$APP_NAME"-config-map
oc create -n "$NAMESPACE" configmap "$APP_NAME"-config-map \
  --from-literal=APP_DEBUG="$APP_DEBUG" \
  --from-literal=DOCUMENTROOT="$DOCUMENTROOT" \
  --from-literal=DYNAMICSBASEURL="$DYNAMICSBASEURL" \
  --dry-run=client -o yaml | oc apply -f -

echo Creating config map "$APP_NAME"-flb-sc-config-map
oc create -n "$NAMESPACE" configmap "$APP_NAME"-flb-sc-config-map \
  --from-literal=fluent-bit.conf="$FLB_CONFIG" \
  --from-literal=parsers.conf="$PARSER_CONFIG" \
  --dry-run=client -o yaml | oc apply -f -
