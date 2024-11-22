###########################################################
#ENV VARS
###########################################################
APP_NAME=$1
NAMESPACE=$2
APP_DEBUG=$3
DOCUMENTROOT=$4
DYNAMICSBASEURL=$5


SPLUNK_URL="gww.splunk.educ.gov.bc.ca"
FLB_CONFIG="[SERVICE]
   Flush        1
   Daemon       Off
   Log_Level    info
   HTTP_Server   On
   HTTP_Listen   0.0.0.0
   Parsers_File parsers.conf
[INPUT]
   Name   tail
   Path   /mnt/log/*
   Exclude_Path *.gz,*.zip
   Parser docker
   Mem_Buf_Limit 20MB
[FILTER]
   Name record_modifier
   Match *
   Record hostname \${HOSTNAME}
[OUTPUT]
   Name   stdout
   Match  absolutely_nothing_bud
   Log_Level    off
"
PARSER_CONFIG="
[PARSER]
    Name        docker
    Format      json
"
###########################################################
#Override config-maps in DEV
###########################################################
echo Creating config map "$APP_NAME"-config-map
oc create -n "$NAMESPACE" configmap "$APP_NAME"-config-map \
  --from-literal=APP_DEBUG="$APP_DEBUG" \
  --from-literal=DOCUMENTROOT="$DOCUMENTROOT" \
  --from-literal=DYNAMICSBASEURL="$DYNAMICSBASEURL" \
  --dry-run=client -o yaml | oc apply -f -

echo Creating config map "$APP_NAME"-flb-sc-config-map
oc create -n "$NAMESPACE"-"$envValue" configmap "$APP_NAME"-flb-sc-config-map \
  --from-literal=fluent-bit.conf="$FLB_CONFIG" \
  --from-literal=parsers.conf="$PARSER_CONFIG" \
  --dry-run=client -o yaml | oc apply -f -