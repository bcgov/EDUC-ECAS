###########################################################
#ENV VARS
###########################################################
envValue=$1
APP_NAME=$2
GRAD_NAMESPACE=$3
COMMON_NAMESPACE=$4
BUSINESS_NAMESPACE=$5
SPLUNK_TOKEN=$6
APP_LOG_LEVEL=$7

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
   Buffer_Max_Size 1MB
[FILTER]
   Name record_modifier
   Match *
   Record hostname \${HOSTNAME}
[OUTPUT]
   Name   stdout
   Match  absolutely_nothing_bud
   Log_Level    off
[OUTPUT]
   Name  splunk
   Match *
   Host  $SPLUNK_URL
   Port  443
   TLS         On
   TLS.Verify  Off
   Message_Key $APP_NAME
   Splunk_Token $SPLUNK_TOKEN
"
PARSER_CONFIG="
[PARSER]
    Name        docker
    Format      json
"
###########################################################
#Setup for config-maps
###########################################################
echo Creating config map "$APP_NAME"-config-map
oc create -n "$GRAD_NAMESPACE"-"$envValue" configmap "$APP_NAME"-config-map \
  --from-literal=APP_LOG_LEVEL="$APP_LOG_LEVEL" \
  --from-literal=EDUC_SCHOOL_API="http://school-api-master.$COMMON_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=ENABLE_FLYWAY="true" \
  --from-literal=ENABLE_SPLUNK_LOG_HELPER="true" \
  --from-literal=ENABLE_STUDENT_ID_PEN_XREF="true" \
  --from-literal=ENABLE_TRAX_UPDATE="true" \
  --from-literal=GRAD_GRADUATION_REPORT_API="http://educ-grad-graduation-report-api.$GRAD_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=GRAD_PROGRAM_API="http://educ-grad-program-api.$GRAD_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=GRAD_STUDENT_GRADUATION_API="http://educ-grad-student-graduation-api.$GRAD_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=GRAD_TRAX_API="http://educ-grad-trax-api.$GRAD_NAMESPACE-$envValue.svc.cluster.local:8080/"\
  --from-literal=MAX_RETRY_ATTEMPTS="3" \
  --from-literal=PEN_API="http://student-api-master.$COMMON_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=CRON_SCHEDULED_REFRESH_NON_GRAD_STATUS="0 0 0 1 * ?" \
  --from-literal=CONNECTION_TIMEOUT='60000' \
  --from-literal=MAXIMUM_POOL_SIZE='20' \
  --from-literal=MIN_IDLE='20' \
  --from-literal=IDLE_TIMEOUT='300000' \
  --from-literal=MAX_LIFETIME='1500000' \
  --dry-run=client -o yaml | oc apply -f -

echo Creating config map "$APP_NAME"-flb-sc-config-map
oc create -n "$GRAD_NAMESPACE"-"$envValue" configmap "$APP_NAME"-flb-sc-config-map \
  --from-literal=fluent-bit.conf="$FLB_CONFIG" \
  --from-literal=parsers.conf="$PARSER_CONFIG" \
  --dry-run=client -o yaml | oc apply -f -
