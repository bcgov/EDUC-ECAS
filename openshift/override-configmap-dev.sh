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
  --from-literal=APP_LOG_LEVEL="$APP_LOG_LEVEL" \
  --from-literal=CONNECTION_TIMEOUT="60000" \
  --from-literal=EDUC_SCHOOL_API="http://school-api-master.$COMMON_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=ENABLE_FLYWAY="true" \
  --from-literal=ENABLE_SPLUNK_LOG_HELPER="false" \
  --from-literal=ENABLE_STUDENT_ID_PEN_XREF="true" \
  --from-literal=ENABLE_TRAX_UPDATE="true" \
  --from-literal=GRAD_GRADUATION_REPORT_API="http://educ-grad-graduation-report-api.$GRAD_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=GRAD_PROGRAM_API="http://educ-grad-program-api.$GRAD_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=GRAD_STUDENT_GRADUATION_API="http://educ-grad-student-graduation-api.$GRAD_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=GRAD_TRAX_API="http://educ-grad-trax-api.$GRAD_NAMESPACE-$envValue.svc.cluster.local:8080/"\
  --from-literal=MAXIMUM_POOL_SIZE="20" \
  --from-literal=MAX_RETRY_ATTEMPTS="3" \
  --from-literal=PEN_API="http://student-api-master.$COMMON_NAMESPACE-$envValue.svc.cluster.local:8080/" \
  --from-literal=CRON_SCHEDULED_REFRESH_NON_GRAD_STATUS="0 0 0 1 * ?" \
  --dry-run=client -o yaml | oc apply -f -

echo Creating config map "$APP_NAME"-flb-sc-config-map
oc create -n "$GRAD_NAMESPACE"-"$envValue" configmap "$APP_NAME"-flb-sc-config-map \
  --from-literal=fluent-bit.conf="$FLB_CONFIG" \
  --from-literal=parsers.conf="$PARSER_CONFIG" \
  --dry-run=client -o yaml | oc apply -f -
